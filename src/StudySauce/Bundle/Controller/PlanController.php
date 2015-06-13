<?php

namespace StudySauce\Bundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use Google_Auth_OAuth2;
use HWI\Bundle\OAuthBundle\OAuth\ResourceOwner\GoogleResourceOwner;
use HWI\Bundle\OAuthBundle\Templating\Helper\OAuthHelper;
use StudySauce\Bundle\Entity\Course;
use StudySauce\Bundle\Entity\Deadline;
use StudySauce\Bundle\Entity\Event;
use StudySauce\Bundle\Entity\Schedule;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * Class PlanController
 * @package StudySauce\Bundle\Controller
 */
class PlanController extends Controller
{
    const OVERLAP_INCREMENT = 600;

    private static $weekConversion = [
        'M' => 86400,
        'Tu' => 172800,
        'W' => 259200,
        'Th' => 345600,
        'F' => 432000,
        'Sa' => 518400,
        'Su' => 0
    ];

    /**
     * @return array
     */
    private static function holidays() {
        $holidays = [
            date('Y') . '/09/01' => 'Memorial day',
            date('Y') . '/11/27' => 'Thanksgiving',
            date('Y') . '/11/28' => 'Thanksgiving',
            date('Y') . '/12/25' => 'Christmas',
            date('Y') . '/12/26' => 'Christmas',
            date('Y') . '/01/19' => 'Martin Luther King Jr.',
            date('Y') + 1 . '/09/01' => 'Memorial day',
            date('Y') + 1 . '/11/27' => 'Thanksgiving',
            date('Y') + 1 . '/11/28' => 'Thanksgiving',
            date('Y') + 1 . '/12/25' => 'Christmas',
            date('Y') + 1 . '/12/26' => 'Christmas',
            date('Y') + 1 . '/01/19' => 'Martin Luther King Jr.'
        ];
        return $holidays;
    }

    /**
     * @param User $user
     * @param ContainerInterface $container
     * @param \Google_Client $client
     * @param \Google_Service_Calendar $service
     */
    public static function syncEvents(User $user, ContainerInterface $container, \Google_Client &$client = null, \Google_Service_Calendar &$service = null) {
        // do initial sync
        /** @var $orm EntityManager */
        $orm = $container->get('doctrine')->getManager();
        /** @var $schedule \StudySauce\Bundle\Entity\Schedule */
        $schedule = $user->getSchedules()->first();

        $id = self::getCalendar($user, $container, $client, $service);

        $sync = $user->getProperty('eventSync');
        /*try {
            if(empty($sync))
                $list = $service->events->listEvents($id);
            else
                $list = $service->events->listEvents($id, ['syncToken' => $sync]);
        }
        catch(\Exception $e) {
            if($e->getCode() == 410)
                $list = $service->events->listEvents($id);
        }*/
        $list = $service->events->listEvents($id);
        $items = $list->getItems();
        $user->setProperty('eventSync', $list->getNextSyncToken());
        $existing = [];
        foreach($items as $item) {
            /** @var \Google_Service_Calendar_Event $item */
            /** @var Event[] $stored */
            $stored = $orm->getRepository('StudySauceBundle:Event')->createQueryBuilder('n')
                ->andWhere('n.remoteId = :id')
                ->setParameter('id', $item->getId())
                ->getQuery()
                ->getResult();
            /** @var Event $event */
            if(empty($stored)) {
                $event = new Event();
                $event->setSchedule($schedule);
                $event->setCreated(new \DateTime());
            }
            else {
                $event = $stored[0];
            }
            /** @var \Google_Service_Calendar_EventDateTime $start */
            $start = $item->getStart();
            /** @var \Google_Service_Calendar_EventDateTime $end */
            $end = $item->getEnd();
            $event->setStart(date_timezone_set(new \DateTime($start->getDateTime()), new \DateTimeZone(date_default_timezone_get())));
            $event->setEnd(date_timezone_set(new \DateTime($end->getDateTime()), new \DateTimeZone(date_default_timezone_get())));
            if(!empty($stored)) {
                $orm->merge($event);
            }
            $existing[] = $item->getId();
        }

        // TODO: sync changes from google
        foreach($schedule->getEvents()->toArray() as $event)
        {
            /** @var Event $event */
            if(empty($event->getRemoteId()) || !in_array($event->getRemoteId(), $existing)) {
                $newEvent = new \Google_Service_Calendar_Event([
                    'summary' => $event->getName(),
                    'location' => $event->getLocation(),
                    'description' => 'Log in to StudySauce to take notes.',
                    'start' => [
                        'dateTime' => $event->getStart()->format('c'),
                        'timeZone' => date_default_timezone_get(),
                    ],
                    'end' => [
                        'dateTime' => $event->getEnd()->format('c'),
                        'timeZone' => date_default_timezone_get(),
                    ],
                    //'recurrence' => [
                    //    'RRULE:FREQ=DAILY;COUNT=2'
                    //],
                    'attendees' => [
                        ['email' => $user->getEmail()]
                    ],
                    'reminders' => [
                        'useDefault' => FALSE,
                        'overrides' => [
                            ['method' => 'email', 'minutes' => $event->getAlert()?:15],
                            ['method' => 'sms', 'minutes' => $event->getAlert()?:15],
                        ],
                    ],
                ]);
                try {
                    $newEvent = $service->events->insert($id, $newEvent);
                }
                catch (\Exception $e) {
                    break;
                }
                $event->setRemoteId($newEvent->getId());
                $orm->merge($event);
            }
        }
        $orm->flush();
    }

    /**
     * @param User $user
     * @param ContainerInterface $container
     * @param \Google_Client $client
     * @param \Google_Service_Calendar $service
     * @return \Google_Service_Calendar_CalendarList
     */
    public static function getCalendars(User $user, ContainerInterface $container, \Google_Client &$client = null, \Google_Service_Calendar &$service = null)
    {
        require_once(__DIR__ . '/../../../../vendor/google/apiclient/autoload.php');
        $client = new \Google_Client();
        /** @var OAuthHelper $oauth */
        $ownerMap = $container->get('hwi_oauth.resource_ownermap.main');
        /** @var GoogleResourceOwner $resourceOwner */
        $resourceOwner = $ownerMap->getResourceOwnerByName('gcal');
        $client->setAccessType('offline');
        $client->setClientId($resourceOwner->getOption('client_id'));
        $client->setClientSecret($resourceOwner->getOption('client_secret'));
        $client->setAccessToken(json_encode(['access_token' => $user->getGcalAccessToken(), 'created' => time(), 'expires_in' => 86400]));
        $service = new \Google_Service_Calendar($client);

        try {
            // list calendars so the user can select which calendar to sync with
            $calendars = $service->calendarList->listCalendarList();
        }
        catch (\Exception $e) {
            if ($e->getCode() == 401) {
                /** @var Google_Auth_OAuth2 $auth */
                $auth = $client->getAuth();
                $auth->refreshToken($user->getGcalAccessToken());
                //$user->setGcalAccessToken($auth->getAccessToken());
                $calendars = $service->calendarList->listCalendarList();
            }
        }
        return $calendars;
    }

    /**
     * @param User $user
     * @param ContainerInterface $container
     * @param \Google_Client $client
     * @param \Google_Service_Calendar $service
     * @return string
     */
    public static function getCalendar(User $user, ContainerInterface $container, \Google_Client &$client = null, \Google_Service_Calendar &$service = null) {

        /** @var $orm EntityManager */
        $orm = $container->get('doctrine')->getManager();

        $calendars = self::getCalendars($user, $container, $client, $service);
        /** @var \Google_Client $client */
        /** @var \Google_Service_Calendar $service */
        $id = '';
        foreach($calendars->getItems() as $cal) {
            /** @var \Google_Service_Calendar_CalendarListEntry $cal */
            if($cal->getId() == $user->getProperty('calendarId') || $cal->getSummary() == 'StudySauce') {
                $id = $cal->getId();
                break;
            }
        }

        // check if studysauce calendar already exists
        if(empty($user->getProperty('calendarId')) || empty($id)) {
            $calendar = new \Google_Service_Calendar_Calendar();
            $calendar->setSummary('StudySauce');
            $calendar->setDescription('Take notes with your classes using StudySauce, check-in to track your studying.');
            $calendar = $service->calendars->insert($calendar);
            $user->setProperty('calendarId', $calendar->getId());
            $user->setProperty('eventSync', '');
            $orm->merge($user);
            $orm->flush();
            $id = $calendar->getId();
        }

        return $id;
    }

    /**
     * @param ContainerInterface $container
     */
    public static function createDemoEvents(ContainerInterface $container)
    {
        /** @var $orm EntityManager */
        $orm = $container->get('doctrine')->getManager();
        /** @var $userManager UserManager */
        $userManager = $container->get('fos_user.user_manager');
        /** @var SecurityContext $context */
        /** @var TokenInterface $token */
        /** @var User $user */
        /** @var User $guest */
        if(!empty($context = $container->get('security.context')) && !empty($token = $context->getToken()) &&
            !empty($user = $token->getUser()) && $user->hasRole('ROLE_DEMO')) {
            $guest = $user;
        }
        else {
            $guest = $userManager->findUserByUsername('guest');
        }

        $schedule = $guest->getSchedules()->first();
        $eventInfo = [];
        foreach($schedule->getClasses()->toArray() as $c)
        {
            self::createCourseEvents($c, $orm);
            /** @var Course $c */
            $week = strtotime('last Sunday', $c->getStartTime()->getTimestamp());
            foreach ($c->getDotw() as $j => $d) {
                if (!isset(self::$weekConversion[$d])) {
                    continue;
                }

                $t = $week + self::$weekConversion[$d];
                $classT = new \DateTime();
                $classT->setTimestamp($t);
                // skip class on holidays
                if (isset(self::holidays()[$classT->format('Y/m/d')])) {
                    continue;
                }
                $classT->setTime($c->getStartTime()->format('H'), $c->getStartTime()->format('i'), $c->getStartTime()->format('s'));

                $eventInfo[] = [
                    'courseId' => $c->getId(),
                    'type' => 'p',
                    'start' => date_timestamp_set(clone $classT, $classT->getTimestamp() - 86400 + 4 * 3600)->format('r'),
                    'end' => date_timestamp_set(clone $classT, $classT->getTimestamp() - 86400 + 5 * 3600)->format('r')
                ];
                $eventInfo[] = [
                    'courseId' => $c->getId(),
                    'type' => 'sr',
                    'start' => date_timestamp_set(clone $classT, $classT->getTimestamp() + 86400)->format('r'),
                    'end' => date_timestamp_set(clone $classT, $classT->getTimestamp() + 86400 + 3600)->format('r')
                ];
            }
        }
        self::createStudyEvents($schedule, $eventInfo, $orm);
    }

    /**
     * @param User $user
     * @param array $template
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(User $user = null, $template = ['Plan', 'tab'])
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        /** @var $user \StudySauce\Bundle\Entity\User */
        if(empty($user))
            $user = $this->getUser();

        /** @var $schedule \StudySauce\Bundle\Entity\Schedule */
        $schedule = $user->getSchedules()->first();

        // get demo schedule instead
        $isDemo = false;
        $isEmpty = false;
        if (empty($schedule) ||
            empty($schedule->getClasses()->count()) ||
            !$user->hasRole('ROLE_PAID')) {
            $schedule = ScheduleController::getDemoSchedule($this->container);
            if($user->hasRole('ROLE_PAID'))
                $isEmpty = true;
            else
                $isDemo = true;
            self::createDemoEvents($this->container);
        }

        // get events for current week
        $emails = new EmailsController();
        $emails->setContainer($this->container);
        $courses = $schedule->getClasses()->toArray();
        $step = self::getPlanStep($user);
        foreach($schedule->getClasses()->toArray() as $c) {
            /** @var Course $c */
            if(empty($c->getEvents()->count())) {
                self::createCourseEvents($c, $orm);
            }
        }
        return $this->render('StudySauceBundle:' . $template[0] . ':' . $template[1] . '.html.php', [
                'schedule' => $schedule,
                'courses' => array_values($courses),
                'jsonEvents' =>  self::getJsonEvents($schedule->getEvents()->toArray()),
                'user' => $user,
                'overlap' => false,
                'step' => $step,
                'isDemo' => $isDemo,
                'isEmpty' => $isEmpty
            ]);
    }

    /**
     * @param User $user
     * @return bool|string
     */
    public static function getPlanStep(User $user)
    {
        /** @var $schedule \StudySauce\Bundle\Entity\Schedule */
        $schedule = $user->getSchedules()->first() ?: new Schedule();

        if($schedule->getClasses()->exists(function ($_, Course $c) {
            return empty($c->getStudyDifficulty()); })) {
            return 1;
        }
        elseif($schedule->getClasses()->exists(function ($_, Course $c) {
                    return $c->getStudyDifficulty() != 'none' && !$c->getEvents()->exists(function ($_, Event $e) {
                        return $e->getType() == 'p' && $e->getDeleted() == false;
                    });}) ||
                $schedule->getClasses()->exists(function ($_, Course $c) {
                    return $c->getStudyDifficulty() != 'none' && !$c->getEvents()->exists(function ($_, Event $e) {
                        return $e->getType() == 'sr' && $e->getDeleted() == false;
                    });})) {
            return 2;
        }
        elseif($schedule->getClasses()->exists(function ($_, Course $c) {
            return empty($c->getStudyType()); })) {
            return 4;
        }
        /*
        if(empty($schedule->getWeekends()) || empty($schedule->getGrades()))
            return 'profile';

        if (empty($schedule->getUniversity()) ||
            empty($schedule->getClasses()->count())) {
            return 'schedule';
        }

        if($schedule->getClasses()->exists(function ($i, Course $c) {
            return empty($c->getStudyType()) || empty($c->getStudyDifficulty()); }))
        {
            return 'customization';
        }
        */

        return false;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function widgetAction()
    {
        /** @var $user \StudySauce\Bundle\Entity\User */
        $user = $this->getUser();
        /** @var $schedule \StudySauce\Bundle\Entity\Schedule */
        $schedule = $user->getSchedules()->first();

        // get events for current week
        if (empty($schedule) ||
            empty($schedule->getCourses()->filter(function (Course $b) {return !$b->getDeleted();})->count())) {
            $schedule = ScheduleController::getDemoSchedule($this->container);
        }

        // TODO: get demo Deadlines?
        $emails = new EmailsController();
        $emails->setContainer($this->container);

        $events = $schedule->getEvents();

        $courses = $schedule->getClasses()->toArray();
        return $this->render('StudySauceBundle:Plan:widget.html.php', [
                'events' => $events,
                'user' => $user,
                'classes' => array_map(function (Course $c) {return $c->getName();}, array_values($courses))
            ]
        );
    }

    /**
     * @param $events
     * @return array
     */
    public static function getJsonEvents($events)
    {
        $jsEvents = [];
        foreach ($events as $i => $x) {
            /** @var Event $x */
            // skip data entry events
            if ($x->getDeleted()) {
                continue;
            }

            $label = '';
            $skip = false;
            switch($x->getType()) {
                case 'c':
                    $label = 'C';
                    break;
                case 'sr':
                    $label = 'SR';
                    break;
                case 'f':
                    $label = 'F';
                    break;
                case 'p':
                    $label = 'P';
                    break;
                case 'o':
                    $label = 'O';
                    break;
                case 'd':
                    $label = 'D';
                    break;
                case 'h':
                    $label = 'H';
                    break;
                /*
            case 'r':
                $label = 'REMINDER';
                break;
            case 'm':
                $label = 'MEAL';
                break;
            case 'z':
                $label = 'SLEEP';
                break;
                */
                default:
                    $skip = true;
            }
            if($skip)
                continue;

            // set up dates recurrence
            if (!empty($x->getCourse()) && $x->getType() == 'sr') {
                $startDay = $x->getCourse()->getStartTime()->getTimestamp();
                $endDay = $x->getCourse()->getEndTime()->getTimestamp();
                $t = $x->getStart()->getTimestamp();
                $dates = [];
                if ($t <= $endDay)
                    $dates[] = date('n/d', $t);
                if ($t - 86400 * 7 >= $startDay && $t - 86400 * 7 <= $endDay)
                    $dates[] = date('n/d', $t - 86400 * 7);
                if ($t - 86400 * 14 >= $startDay && $t - 86400 * 14 <= $endDay)
                    $dates[] = date('n/d', $t - 86400 * 14);
                if ($t - 86400 * 28 >= $startDay && $t - 86400 * 28 <= $endDay)
                    $dates[] = date('n/d', $t - 86400 * 28);
            }

            $jsEvents[$i] = [
                'eventId' => $x->getId(),
                'title' => '<h4>' . $label . '</h4>' . $x->getName(),
                'start' => $x->getStart()->format('r'),
                'end' => $x->getEnd()->format('r'),
                'className' => 'event-type-' . $x->getType() . ' ' . (!empty($x->getCourse()) &&
                    $x->getCourse()->getIndex() !== false
                        ? ('class' . $x->getCourse()->getIndex())
                        : '') . ' ' . (!empty($x->getCourse())
                        ? ('course-id-' . $x->getCourse()->getId())
                        : ''),
                // all day for deadlines, reminders, and holidays
                'allDay' => $x->getType() == 'd' || $x->getType() == 'h' ||
                    $x->getType() == 'r',
                'editable' => ($x->getType() == 'sr' || $x->getType() == 'f' || $x->getType() == 'p'),
                'dates' => isset($dates) ? $dates : null,
                'alert' => $x->getAlert(),
                'location' => $x->getLocation()
            ];
            if(!empty($x->getCourse()))
                $jsEvents[$i]['courseId'] = $x->getCourse()->getId();
        }
        return $jsEvents;
    }

    /**
     * @param Schedule $schedule
     * @param Deadline[] $deadlines
     * @param EntityManager $orm
     */
    public static function createAllDay(Schedule $schedule, $deadlines, EntityManager $orm)
    {
        $events = [];

        // add deadlines and holidays
        foreach ($deadlines as $did => $d) {
            /** @var Deadline $d */
            $classT = clone $d->getDueDate();
            $classT->setTime(0, 0, 0);

            // create a new event
            $deadline = [
                'deadline' => $d,
                'course' => $d->getCourse(),
                'name' => $d->getAssignment(),
                'type' => 'd',
                'start' => $classT,
                'end' => date_add(clone $classT, new \DateInterval('PT86399S'))
            ];
            $events[] = $deadline;

            if(empty($d->getReminder()))
                continue;

            foreach ($d->getReminder() as $i => $r) {
                $classR = clone $classT;
                $classR->sub(new \DateInterval('PT' . $r . 'S'));

                $reminder = [
                    'deadline' => $d,
                    'course' => $d->getCourse(),
                    'name' => $d->getAssignment(),
                    'type' => 'r',
                    'start' => $classR,
                    'end' => date_add(clone $classR, new \DateInterval('PT86399S'))
                ];
                $events[] = $reminder;
            }
        }

        foreach (self::holidays() as $k => $h) {
            $classT = new \DateTime($k);

            $holiday = [
                'name' => $h,
                'type' => 'h',
                'start' => $classT,
                'end' => date_add(clone $classT, new \DateInterval('PT86399S'))
            ];
            $events[] = $holiday;
        }

        self::mergeSaved($schedule, $schedule->getEvents()->filter(function (Event $event) {
            return $event->getType() == 'd' || $event->getType() == 'r' || $event->getType() == 'h';
        }), $events, $orm);
    }

    /**
     * @param $events
     */
    private static function sortEvents(&$events)
    {
        usort($events, function (array $a, array $b) use ($events) {
                /** @var \DateTime $aStart */
                $aStart = $a['start'];
                /** @var \DateTime $bStart */
                $bStart = $b['start'];
                return $aStart->getTimestamp() - $bStart->getTimestamp();
            });
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateProfileAction(Request $request)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var $user \StudySauce\Bundle\Entity\User */
        $user = $this->getUser();

        /** @var $schedule \StudySauce\Bundle\Entity\Schedule */
        $schedule = $user->getSchedules()->first();
        if(!empty($schedule)) {
            if(!empty($alerts = $request->get('alerts'))) {
                $toSave = [];
                foreach(['c', 'p', 'sr', 'f', 'o'] as $t) {
                    if(isset($alerts[$t]))
                        $toSave[$t] = intval($alerts[$t]);
                    else
                        $toSave[$t] = 15;
                }
                $schedule->setAlerts($toSave);
                $orm->merge($schedule);
            }
            $courses = $schedule->getClasses()->toArray();
            foreach($courses as $i => $c)
            {
                /** @var Course $c */
                if(!empty($request->get('profile-type-' . $c->getId()))) {
                    $c->setStudyType($request->get('profile-type-' . $c->getId()));
                    $orm->merge($c);
                }
                if(!empty($request->get('profile-difficulty-' . $c->getId()))) {
                    $c->setStudyDifficulty($request->get('profile-difficulty-' . $c->getId()));
                    $orm->merge($c);
                }
            }
            $orm->flush();
        }

        // check if schedule is empty
        return $this->forward('StudySauceBundle:Plan:index', ['_format' => 'tab']);
    }

    /**
     * @param $events
     * @param double $notBefore
     * @param double $notAfter
     * @return array
     */
    private static function getWorkingEvents($events, $notBefore, $notAfter)
    {
        // get a list of events within a 24 hour time range to work with, we should never move more than that
        $beforeDistances = [];
        $afterDistances = [];
        foreach ($events as $i => $event)
        {
            /** @var array $event */
            // ignore all day events
            if($event['type'] == 'd' || $event['type'] == 'h' ||
                $event['type'] == 'r')
                continue;

            /** @var \DateTime $s */
            $s = $event['start'];
            /** @var \DateTime $e */
            $e = $event['end'];
            $startDistance = $e->getTimestamp() - $notBefore;
            $endDistance = $notAfter - $s->getTimestamp();
            $beforeDistances[$i] = $startDistance;
            $afterDistances[$i] = $endDistance;
        }

        // working events are made of intersection of keys from between before and after times
        //  including 1 negative from both meaning occurs before and after
        asort($beforeDistances);
        asort($afterDistances);
        $workingBefore = [];
        $workingAfter = [];
        $first = true;
        unset($last);
        foreach($beforeDistances as $i => $distance)
        {
            if($distance >= 0)
            {
                if($first)
                {
                    if(isset($last))
                        $workingBefore[$last] = $events[$last];
                    $first = false;
                }
                $workingBefore[$i] = $events[$i];
            }
            $last = $i;
        }
        $first = true;
        unset($last);
        foreach($afterDistances as $i => $distance)
        {
            if($distance >= 0)
            {
                if($first)
                {
                    if(isset($last))
                        $workingAfter[$last] = $events[$last];
                    $first = false;
                }
                $workingAfter[$i] = $events[$i];
            }
            $last = $i;
        }

        $workingEvents = array_intersect_key($workingBefore, $workingAfter);

        self::sortEvents($workingEvents);

        return array_values($workingEvents);
    }

    /**
     * @param $workingEvents
     * @param double $length
     * @param $notBefore
     * @param $notAfter
     * @return array
     */
    private static function getGaps(&$workingEvents, $length, $notBefore, $notAfter)
    {
        // find gaps
        $gapStart = new \DateTime();
        $gapStart->setTimestamp($notBefore);
        $last = $gapStart->getTimestamp();
        $gaps = [];
        foreach ($workingEvents as $c => $e) {
            /** @var array $e */
            /** @var \DateTime $gapEnd */
            $gapEnd = clone $e['start'];
            $gapEnd->setTimestamp(min($gapEnd->getTimestamp(), $notAfter));
            $gap = $gapEnd->getTimestamp() - $gapStart->getTimestamp();

            // make sure gap is large enough for the event
            if ($gap >= $length + 2 * self::OVERLAP_INCREMENT &&
                $gapEnd->getTimestamp() > $notBefore &&
                $gapStart->getTimestamp() < $notAfter
            ) {
                $gaps['i'.$c] = clone $gapStart;
            }

            /** @var \DateTime $gapStart */
            $gapStart = clone $e['end'];
            $gapStart->setTimestamp(max($gapStart->getTimestamp(), $notBefore));
            // fix situation where this is one already overlapping
            $gapStart->setTimestamp(max($gapStart->getTimestamp(), $last));
            $last = $gapStart->getTimestamp();
        }
        if(empty($workingEvents))
        {
            $gapEnd = new \DateTime();
            $gapEnd->setTimestamp($notAfter);

            $gaps['empty'] = $gapStart;
            $workingEvents['empty']['start'] = clone $gapEnd;
        }
        return $gaps;
    }

    /**
     * @param Schedule $schedule
     * @param Collection $saved
     * @param array $events
     * @param EntityManager $orm
     * @return array
     */
    public static function mergeSaved(Schedule $schedule, Collection $saved, array $events, EntityManager $orm)
    {
        // find matching saved events
        foreach ($events as $i => $event) {
            /** @var array $event */
            /** @var \DateTime $s */
            $s = $event['start'];
            // no notes are enabled for these events so they don't have any associations
            if($event['type'] == 'm' || $event['type'] == 'z' || $event['type'] == 'h')
                continue;

            // check if we have a saved event around the same time
            $lastEvent = null;
            $lastDistance = 172800;
            $similarEvents = $saved->filter(
                function (Event $e) use ($event) {
                    return $e->getType() == $event['type'] && $e->getName() == $event['name'] &&
                    $e->getCourse() == (!empty($event['course']) ? $event['course'] : null) &&
                    $e->getDeadline() == (!empty($event['deadline']) ? $event['deadline'] : null);
                }
            )->toArray();
            foreach ($similarEvents as $j => $e) {
                /** @var Event $e */
                // if saved is 24 hours before current event, skip and never go back because we are ordered by time
                $distance = abs($s->getTimestamp() - $e->getStart()->getTimestamp());
                if ($distance < $lastDistance) {
                    $lastEvent = $e;
                    $lastDistance = $distance;
                } elseif (!empty($lastEvent)) {
                    break;
                }
            }

            // change times of existing event to fit in to new schedule
            if ($lastEvent) {
                $lastEvent->setStart($event['start']);
                $lastEvent->setEnd($event['end']);
                $events[$i] = $lastEvent;
                $saved->removeElement($lastEvent);
            }
        }

        // remove unassociated events from database,
        //   unless they have data attached we will just hide them in historic view
        $remove = $saved->toArray();
        foreach ($remove as $i => $save) {
            /** @var Event $save */
            if(!empty($save->getCompleted()))
            {
                $save->setDeleted(true);
                $orm->merge($save);
            }
            else {
                $schedule->removeEvent($save);
                $orm->remove($save);
            }
        }
        $orm->flush();

        // create new entities if needed
        foreach ($events as $i => $event) {
            if(is_array($event))
            {
                $newEvent = new Event();
                $newEvent->setDeadline(!empty($event['deadline']) ? $event['deadline'] : null);
                $newEvent->setCourse(!empty($event['course']) ? $event['course'] : null);
                $newEvent->setName($event['name']);
                $newEvent->setType($event['type']);
                $newEvent->setStart($event['start']);
                $newEvent->setEnd($event['end']);
                $newEvent->setSchedule($schedule);
                $schedule->addEvent($newEvent);
                $orm->persist($newEvent);
                $events[$i] = $newEvent;
            }
            else
            {
                /** @var Event $event */
                $orm->merge($event);
            }
        }
        $orm->flush();
    }

    /**
     * @return Response
     */
    public function downloadAction()
    {
        /** @var \StudySauce\Bundle\Entity\User $user */
        $user = $this->getUser();
        $email = $user->getEmail();
        $name = $user->getFirst() . ' ' . $user->getLast();
        $now = new \DateTime();
        $stamp = $now->format('Ymd') . 'T' . $now->format('His') . 'Z';
        $calendar = <<<EOCAL
BEGIN:VCALENDAR
PRODID:STUDYSAUCE.COM
VERSION:2.0
CALSCALE:GREGORIAN
METHOD:PUBLISH
X-WR-CALNAME:Study Sauce
X-WR-TIMEZONE:America/Phoenix

EOCAL;
        /** @var $schedule \StudySauce\Bundle\Entity\Schedule */
        $schedule = $user->getSchedules()->first();
        $events = $schedule->getEvents();
        $max = max(array_map(function (Event $e) {return $e->getCreated()->getTimestamp();}, $events->toArray()));
        $last = new \DateTime();
        $last->setTimestamp($max);
        $lastModified = $last->format('Ymd') . 'T' . $last->format('His') . 'Z';
        foreach($events->toArray() as $event) {
            /** @var Event $event */
            $id = $event->getId();
            $title = $event->getName();
            $start = date_timezone_set(clone $event->getStart(), new \DateTimeZone('GMT'))->format('Ymd') . 'T' . date_timezone_set(clone $event->getStart(), new \DateTimeZone('GMT'))->format('His') . 'Z';
            $end = date_timezone_set(clone $event->getEnd(), new \DateTimeZone('GMT'))->format('Ymd') . 'T' . date_timezone_set(clone $event->getEnd(), new \DateTimeZone('GMT'))->format('His') . 'Z';
            $created = $event->getCreated()->format('Ymd') . 'T' . $event->getCreated()->format('His') . 'Z';
            // TODO: load alert from settings
            $alert = '30M';
            $eventStr = <<<EOEVT
BEGIN:VEVENT
DTSTART:$start
DTEND:$end
DTSTAMP:$stamp
ORGANIZER;CN=$name:mailto:$email
UID:STUDYSAUCE-$id
ATTENDEE;CUTYPE=INDIVIDUAL;ROLE=REQ-PARTICIPANT;PARTSTAT=ACCEPTED;CN=$name;X-NUM-GUESTS=0:mailto:$email
CREATED:$created
DESCRIPTION:Log in to studysauce.com to take notes
LAST-MODIFIED:$lastModified
LOCATION:
SEQUENCE:0
STATUS:CONFIRMED
SUMMARY:$title
TRANSP:OPAQUE
BEGIN:VALARM
TRIGGER:-PT$alert
REPEAT:1
DURATION:PT$alert
ACTION:DISPLAY
DESCRIPTION:Reminder
END:VALARM
END:VEVENT

EOEVT;
            $calendar .= $eventStr;
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'text/calendar');
        $response->headers->set('Content-Disposition', 'attachment; filename="studysauce.ics"');
        $response->setContent($calendar . '
END:VCALENDAR');
        return $response;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function createStudyAction(Request $request)
    {

        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        /** @var $user User */
        $user = $this->getUser();

        /** @var Schedule $schedule */
        $schedule = $user->getSchedules()->first();

        self::createStudyEvents($schedule, $request->get('events'), $orm);

        return $this->forward('StudySauceBundle:Plan:index', ['_format' => 'tab']);
    }

    /**
     * @param Schedule $schedule
     * @param $eventInfo
     * @param EntityManager $orm
     */
    public static function createStudyEvents(Schedule $schedule, $eventInfo, EntityManager $orm)
    {
        $events = [];
        $existing = [];

        foreach($eventInfo as $event) {

            if(!empty($event['courseId'])) {
                /** @var Course $course */
                $course = $schedule->getCourses()->filter(function (Course $x) use($event) {
                    return !$x->getDeleted() && $x->getId() == $event['courseId'];})->first();
                $classStart = $course->getStartTime();
                $classEnd = $course->getEndTime();
                $title = $course->getName();
                $existing = array_merge($existing, $course->getEvents()->filter(function (Event $e) use($event){return $e->getType() == $event['type'];})->toArray());
            }
            elseif($event['type'] == 'f') {
                $course = null;
                // find earliest class
                $classStart = min(array_map(function (Course $c) {return $c->getStartTime();}, $schedule->getClasses()->toArray()));
                $classEnd = max(array_map(function (Course $c) {return $c->getEndTime();}, $schedule->getClasses()->toArray()));
                $title = 'Free study';
                $existing = array_merge($existing, $schedule->getEvents()->filter(function (Event $e) {return $e->getType() == 'f';})->toArray());
            }
            else
                continue;

            $newStart = new \DateTime($event['start']);
            $newStart->setTimezone(new \DateTimeZone(date_default_timezone_get()));
            $newEnd = new \DateTime($event['end']);
            $newEnd->setTimezone(new \DateTimeZone(date_default_timezone_get()));
            $length = $newEnd->getTimestamp() - $newStart->getTimestamp();
            $d = array_keys(self::$weekConversion)[$newStart->format('N') - 1];

            // get day of the week from start
            for($week = strtotime('last Sunday', $classStart->getTimestamp());
                $week < strtotime('last Sunday', $classEnd->getTimestamp()) + 604800;
                $week += 604800) {

                $t = $week + self::$weekConversion[$d];
                if ($t < $classStart->getTimestamp() || $t > $classEnd->getTimestamp()) {
                    continue;
                }

                $classT = new \DateTime();
                $classT->setTimestamp($t);
                $classT->setTime($newStart->format('H'), $newStart->format('i'), $newStart->format('s'));

                $event = [
                    'course' => $course,
                    'name' => $title,
                    'type' => $event['type'],
                    'start' => $classT,
                    'end' => date_add(clone $classT, new \DateInterval('PT' . $length . 'S'))
                ];
                $events[] = $event;
            }

        }
        // merge events with saved
        self::mergeSaved($schedule, new ArrayCollection($existing), $events, $orm);
    }

    /**
     * @param Course $course
     * @param EntityManager $orm
     */
    public static function createCourseEvents(Course $course, EntityManager $orm)
    {
        $events = [];
        $once = false;
        // TODO: uncomment this to bring back singly occurring events
        // if ($course->getType() == 'o' && !in_array('Weekly', $course->getDotw())) {
        //    $once = true;
        //}
        $existing = $course->getEvents()->filter(function (Event $e) use ($course) {
            return $e->getType() == 'c' && $e->getCourse() == $course;});

        $classStart = $course->getStartTime();
        $classEnd = $course->getEndTime();
        $length = $course->getLength();
        for($week = strtotime('last Sunday', $classStart->getTimestamp());
            $week < strtotime('last Sunday', $classEnd->getTimestamp()) + 604800;
            $week += 604800) {
            foreach ($course->getDotw() as $j => $d) {
                if (!isset(self::$weekConversion[$d])) {
                    continue;
                }

                $t = $week + self::$weekConversion[$d];
                if ($t < $classStart->getTimestamp() - 86400 || $t > $classEnd->getTimestamp() + 86400) {
                    continue;
                }

                $classT = new \DateTime();
                $classT->setTimestamp($t);
                // skip class on holidays
                if ($course->getType() == 'c' && isset(self::holidays()[$classT->format('Y/m/d')])) {
                    continue;
                }
                $classT->setTime($classStart->format('H'), $classStart->format('i'), $classStart->format('s'));

                $event = [
                    'course' => $course,
                    'name' => $course->getName(),
                    'type' => $course->getType(),
                    'start' => $classT,
                    'end' => $once
                        ? date_timestamp_set(
                            clone $classT,
                            min(
                                date_add(clone $classT, new \DateInterval('PT86400S'))->getTimestamp(),
                                $classEnd->getTimestamp()
                            )
                        )
                        : date_add(clone $classT, new \DateInterval('PT' . $length . 'S'))
                ];
                $events[] = $event;
            }
        }

        // merge events with saved
        self::mergeSaved($course->getSchedule(), $existing, $events, $orm);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var User $user */
        $user = $this->getUser();

        /** @var Schedule $schedule */
        $schedule = $user->getSchedules()->first();

        /** @var Event $event */
        if(!empty($schedule))
            $event = $schedule->getEvents()->filter(function (Event $e)use($request) {return $e->getId() == $request->get('eventId');})->first();

        if (!empty($event)) {
            $oldStart = clone $event->getStart();
            if($request->get('start') !== null) {
                $newStart = new \DateTime($request->get('start'));
                $newStart->setTimezone(new \DateTimeZone(date_default_timezone_get()));
                $newEnd = new \DateTime($request->get('end'));
                $newEnd->setTimezone(new \DateTimeZone(date_default_timezone_get()));

                $event->setStart($newStart);
                $event->setEnd($newEnd);
                $event->setMoved(true);

                if ($newStart->getTimestamp() - $oldStart->getTimestamp() < 0) {
                    $diff = new \DateInterval('PT' . abs($newStart->getTimestamp() - $oldStart->getTimestamp()) . 'S');
                    $diff->invert = 1;
                } else {
                    $diff = new \DateInterval('PT' . ($newStart->getTimestamp() - $oldStart->getTimestamp()) . 'S');
                }
            }
            else {
                $diff = $diff = new \DateInterval('PT0S');
                $newStart = clone $event->getStart();
            }

            if($request->get('location') !== null)
                $event->setLocation($request->get('location'));
            if($request->get('alert') !== null)
                $event->setAlert($request->get('alert'));
            if($request->get('title') !== null)
                $event->setName($request->get('title'));

            $orm->merge($event);

            // move subsequent events of the same type
            $events = $schedule->getEvents()
                ->filter(function (Event $e)use($event, $oldStart) {
                    return $e->getType() == $event->getType() &&
                            $e->getName() == $event->getName() &&
                            $e->getStart() > $oldStart &&
                            $e->getId() != $event->getId(); })
                ->toArray();
            usort($events, function (Event $a, Event $b) use ($events) {
                    return $a->getStart()->getTimestamp() - $b->getStart()->getTimestamp();
                });

            foreach ($events as $k => $e)
            {
                /** @var Event $e */
                // if more than a week has passed add a week to $newStart and test again
                if ($e->getStart()->getTimestamp() - $oldStart->getTimestamp() > 60 * 60 * 24 * 8)
                    $oldStart->setTimestamp($oldStart->getTimestamp() + 60 * 60 * 24 * 7);

                // make sure next event is about a week later
                if ($e->getStart()->getTimestamp() - $oldStart->getTimestamp() > 60 * 60 * 24 * 6 &&
                    $e->getStart()->getTimestamp() - $oldStart->getTimestamp() < 60 * 60 * 24 * 8 &&
                    date_add(clone $e->getStart(), $diff)->format('H:i:s') == $newStart->format('H:i:s')
                ) {
                    // get events for the day to make sure we are not overlapping
                    $working = $schedule->getEvents()
                        ->filter(function (Event $x) use ($e) {
                                // ignore all these event types because they are not displayed to the user
                                return $e->getId() != $x->getId() && $x->getType() != 'h' && $x->getType() != 'd' &&
                                    $x->getType() != 'r' && $x->getType() != 'm' && $x->getType() != 'z'; })
                        ->map(function (Event $e) {return [
                                'start' => $e->getStart(),
                                'end' => $e->getEnd(),
                                'type' => $e->getType()
                            ];})->toArray();
                    self::sortEvents($working);
                    $working = self::getWorkingEvents($working,
                        $e->getStart()->getTimestamp() - 86400,
                        $e->getEnd()->getTimestamp() + 86400);
                    self::sortEvents($working);
                    /** @var \DateTime $tempStart */
                    $tempStart = date_add(clone $e->getStart(), $diff);
                    /** @var \DateTime $tempEnd */
                    $tempEnd = date_add(clone $e->getEnd(), $diff);

                    $gaps = self::getGaps(
                        $working,
                        $tempEnd->getTimestamp() - $tempStart->getTimestamp(),
                        $e->getStart()->getTimestamp() - 604800,
                        $e->getEnd()->getTimestamp() + 604800);
                    // make sure one of the gaps overlaps the newTime
                    foreach($gaps as $c => $d) {
                        /** @var \DateTime $d */
                        /** @var \DateTime $gapEnd */
                        $gapEnd = $working[intval(substr($c, 1))]['start'];
                        if ($d <= $tempStart &&
                            $gapEnd >= $tempEnd
                        ) {
                            $e->setStart($tempStart);
                            $e->setEnd($tempEnd);
                            $e->setMoved(true);

                            if($request->get('location') !== null)
                                $event->setLocation($request->get('location'));
                            if($request->get('alert') !== null)
                                $event->setAlert($request->get('alert'));
                            if($request->get('title') !== null)
                                $event->setName($request->get('title'));

                            $orm->merge($e);
                            break;
                        }
                    }
                }
            }

        }
        $orm->flush();

        return $this->forward('StudySauceBundle:Plan:index', ['_format' => 'tab']);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function completeAction(Request $request)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var User $user */
        $user = $this->getUser();

        /** @var Schedule $schedule */
        $schedule = $user->getSchedules()->first();

        /** @var Event $event */
        $event = $schedule->getEvents()->filter(function (Event $e)use($request) {return $e->getId() == $request->get('eventId');})->first();
        if(empty($event))
            throw new NotFoundHttpException();

        $event->setCompleted($request->get('completed') == 'true');
        $orm->merge($event);
        $orm->flush();

        return new JsonResponse(true);
    }

}



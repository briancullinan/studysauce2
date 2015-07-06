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

    public static $weekConversion = [
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
     * @param Collection $events
     * @return array
     */
    private static function groupRecurrenceEvents(Collection $events) {

        // group events so we can create reoccurring settings easily
        $grouped = [];
        foreach($events as $event) {

            /** @var Event $event */
            if($event->getDeleted())
                continue;

            // group by course
            $cid = !empty($event->getCourse()) ? $event->getCourse()->getId() : '';
            // group by event type
            $t = $event->getType();
            // group by hour and minute setting
            $hi = $event->getStart()->format('H:i');

            if(substr_count($event->getRemoteId() ?: '', '_') > 1)
                $hi .= $event->getRemoteId();

            // group by consecutive weeks
            $last = $event->getStart()->format('W');
            if (!empty($grouped[$cid][$t][$hi])) {
                $lastE = end($grouped[$cid][$t][$hi]);
                /** @var Event $consecutive */
                $consecutive = end($lastE);
                if ($consecutive->getStart()->format('W') == $event->getStart()->format('W') ||
                    $consecutive->getStart()->format('W') == intval($event->getStart()->format('W')) + 1 ||
                    $consecutive->getStart()->format('W') == intval($event->getStart()->format('W')) - 1
                ) {
                    $last = key($grouped[$cid][$t][$hi]);
                }
            }

            $grouped[$cid][$t][$hi][$last][] = $event;
        }

        return $grouped;
    }

    /**
     * @param \Google_Service_Calendar_Event[] $items
     * @param $calendarId
     * @param \Google_Service_Calendar $service
     * @param ArrayCollection $stored
     * @param $remoteIds
     * @return array
     */
    public static function generateEventsFromGoogle($items, $calendarId, \Google_Service_Calendar $service, ArrayCollection $stored, &$remoteIds)
    {

        $events = [];
        $remoteIds = [];

        return $events;
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

        $calendarId = self::getCalendar($user, $container, $client, $service);

        $list = $service->events->listEvents($calendarId);
        /** @var \Google_Service_Calendar_Event[] $items */
        $items = $list->getItems();
        $user->setProperty('eventSync', $list->getNextSyncToken());

        // sync changes from google
        $existing = [];
        $events = [];
        /** @var \Google_Service_Calendar_Event[] $remoteIds */
        $remoteIds = [];
        $allIds = [];
        foreach($items as $item) {
            /** @var \Google_Service_Calendar_Event[] $instances */
            /** @var Event[] $editing */
            // get all events involved in the series
            $editing = array_values($schedule->getEvents()->filter(function (Event $e) use ($item) {
                // get ID from iCAL setting
                return substr($e->getRemoteId(), 0, strlen($item->getId())) == $item->getId();
            })->toArray());
            $remoteIds[$item->getId()] = $item;
            if(in_array($item->getId(), $allIds)) {
                continue;
            }
            $allIds[] = $item->getId();
            // TODO: only do this part if update is greater than any event in the series
            $existing = array_merge($existing, $editing);
            $instances = [];
            if(empty($item->getRecurringEventId())) {
                $instances = $service->events->instances($calendarId, $item->getId())->getItems();
            }
            if(empty($instances)) {
                $instances = [$item];
            }
            $working = array_map(function (\Google_Service_Calendar_Event $instance) use ($editing, &$allIds) {
                $allIds[] = $instance->getId();
                return [
                    'deadline' => empty($editing) ? null : $editing[0]->getDeadline(),
                    'course' => empty($editing) ? null : $editing[0]->getCourse(),
                    'name' => str_replace([': Pre-work', ': Study session', ': Class'], '', $instance->getSummary()),
                    'type' => empty($editing) ? 'o' : $editing[0]->getType(),
                    'start' => date_timezone_set(
                        new \DateTime($instance->getStart()->getDateTime()),
                        new \DateTimeZone(date_default_timezone_get())),
                    'end' => date_timezone_set(
                        new \DateTime($instance->getEnd()->getDateTime()),
                        new \DateTimeZone(date_default_timezone_get())),
                    'remoteId' => $instance->getId()
                ];
            }, $instances);
            $events = array_merge($events, $working);
        }
        self::mergeSaved($schedule, new ArrayCollection(self::arrayUniqueObj($existing)), $events, $orm);

        $grouped = self::groupRecurrenceEvents($schedule->getEvents());

        // sync changes to google
        $reserved = [];
        foreach($grouped as $cid => $types) {
            foreach ($types as $type => $hours) {
                $working = call_user_func_array('array_merge_recursive', call_user_func_array('array_merge_recursive', $hours));
                $shouldUpdate = (new ArrayCollection($working))
                    ->exists(function ($_, Event $e) {
                        return empty($e->getRemoteUpdated()) || (!empty($e->getUpdated())
                            && $e->getUpdated() > $e->getRemoteUpdated());});
                foreach ($hours as $consecutive) {
                    foreach($consecutive as $events) {

                        $events = new ArrayCollection($events);
                        $config = self::getGoogleCalendarConfig($events);
                        $newEvent = new \Google_Service_Calendar_Event($config);

                        $instances = [];
                        /** @var Event $remote */
                        $remote = $events->filter(
                            function (Event $e) use ($remoteIds, $reserved) {
                                return !empty($e->getRemoteId())
                                && ((in_array($e->getRemoteId(), array_keys($remoteIds))
                                && !in_array($e->getRemoteId(), $reserved))
                                    || (in_array(substr($e->getRemoteId(), 0, strpos($e->getRemoteId(), '_', 1)), array_keys($remoteIds))
                                        && !in_array(substr($e->getRemoteId(), 0, strpos($e->getRemoteId(), '_', 1)), $reserved)));
                            }
                        )->first();
                        if (empty($remote)) {
                            $newEvent = $service->events->insert($calendarId, $newEvent);
                            if(empty($config['recurrence']))
                                $instances = [$newEvent];
                            else
                                $instances = $service->events->instances($calendarId, $newEvent->getId())->getItems();
                        } else {
                            $remoteId = isset($remoteIds[$remote->getRemoteId()])
                                ? $remote->getRemoteId()
                                : substr($remote->getRemoteId(), 0, strpos($remote->getRemoteId(), '_', 1));
                            $reserved[] = $remoteId;
                            if ($shouldUpdate || $remoteIds[$remoteId]->getRecurrence() != $config['recurrence'] ||
                                new \DateTime($remoteIds[$remoteId]->getStart()->getDateTime()) != $events->last()->getStart()) {
                                $newEvent = $service->events->update($calendarId, $remoteId, $newEvent);
                                if(empty($config['recurrence']))
                                    $instances = [$newEvent];
                                else
                                    $instances = $service->events->instances($calendarId, $remoteId)->getItems();
                            }
                        }
                        foreach ($instances as $instance) {
                            /** @var \Google_Service_Calendar_Event $instance */
                            $start = date_timezone_set(
                                new \DateTime($instance->getStart()->getDateTime()),
                                new \DateTimeZone(date_default_timezone_get())
                            )->format('Y/m/d');
                            /** @var Event $event */
                            $event = $events->filter(
                                function (Event $e) use ($start) {
                                    return $e->getStart()->format('Y/m/d') == $start;
                                }
                            )->first();
                            $event->setRemoteUpdated(new \DateTime());
                            $event->setRemoteId($instance->getId());
                            $orm->merge($event);
                        }
                    }
                }
            }
        }

        $orm->flush();

    }

    /**
     * @param ArrayCollection $events
     * @return array
     */
    private static function getGoogleCalendarConfig(ArrayCollection $events)
    {

        $config = [
            'summary' => $events->last()->getTitle(),
            'location' => $events->last()->getLocation(),
            'description' => 'Log in to StudySauce to take notes.',
            'start' => [
                'dateTime' => $events->last()->getStart()->format('c'),
                'timeZone' => date_default_timezone_get(),
            ],
            'end' => [
                'dateTime' => $events->last()->getEnd()->format('c'),
                'timeZone' => date_default_timezone_get(),
            ],
//                        'attendees' => [[
//                            'email' => $user->getEmail(),
//                            'displayName' => $name,
//                            'responseStatus' => 'accepted',
//                            'optional' => true
//                        ]],
            'colorId' => 6
        ];

        if($events->count() > 1) {
            $rRules = [];
            $rRules[] = 'RRULE:FREQ=WEEKLY' .
                ';UNTIL=' . date_add(date_timezone_set(clone $events->first()->getStart(), new \DateTimeZone('Z')), new \DateInterval('P1D'))->format('Ymd') . 'T000000Z' .
                ';BYDAY=' . implode(',', array_unique(array_map(function (Event $e) {return ['SU', 'MO', 'TU', 'WE', 'TH', 'FR', 'SA'][$e->getStart()->format('w')];}, $events->toArray())));
            $config['recurrence'] = $rRules;
        }
        else {
            $config['recurrence'] = [];
        }

        if (!empty($events->last()->getAlert())) {
            $config['reminders'] = [
                'useDefault' => false,
                'overrides' => [
                    ['method' => 'popup', 'minutes' => $events->last()->getAlert()]
                ],
            ];
        }

        return $config;
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
        if (empty($schedule) || empty($schedule->getClasses()->count()) ||
            !$user->hasRole('ROLE_PAID') || !$schedule->getClasses()->exists(function ($_, Course $c) {
                return $c->getEndTime() > new \DateTime();})) {
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
            $existing = $c->getEvents()->filter(function(Event $e) {
                return !$e->getDeleted() && $e->getType() == 'c';})->toArray();
            if(empty($existing)) {
                self::createCourseEvents($c, $orm);
            }
        }

        // list oauth services
        /** @var OAuthHelper $oauth */
        $oauth = $this->get('hwi_oauth.templating.helper.oauth');
        foreach($oauth->getResourceOwners() as $o) {
            if($o != 'gcal')
                continue;
            $services[$o] = $oauth->getLoginUrl($o);
        }

        return $this->render('StudySauceBundle:' . $template[0] . ':' . $template[1] . '.html.php', [
                'schedule' => $schedule,
                'courses' => array_values($courses),
                'jsonEvents' =>  self::getJsonEvents($schedule->getEvents()->toArray()),
                'user' => $user,
                'overlap' => false,
                'step' => $step,
                'isDemo' => $isDemo,
                'isEmpty' => $isEmpty,
                'services' => $services
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
                        return $e->getType() == 'p' && !$e->getDeleted();
                    });}) ||
                $schedule->getClasses()->exists(function ($_, Course $c) {
                    return $c->getStudyDifficulty() != 'none' && !$c->getEvents()->exists(function ($_, Event $e) {
                        return $e->getType() == 'sr' && !$e->getDeleted();
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
        $isDemo = false;
        if (empty($schedule) || empty($schedule->getClasses()->count())) {
            $events = [];
            $isDemo = true;
        }
        else {
            $events = $schedule->getEvents()->filter(function (Event $e) {return !$e->getDeleted();})->toArray();
        }

        return $this->render('StudySauceBundle:Plan:widget.html.php', [
                'isDemo' => $isDemo,
                'events' => $events,
                'user' => $user
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
            return ($event->getType() == 'd' || $event->getType() == 'r' || $event->getType() == 'h') && !$event->getDeleted();
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
                    $difficulty = $request->get('profile-difficulty-' . $c->getId());
                    $c->setStudyDifficulty($difficulty);
                    /** @var Event[] $prework */
                    $prework = $c->getEvents()->filter(function (Event $e) {return $e->getType() == 'p';})->toArray();
                    foreach($prework as $e) {
                        if($difficulty == 'easy')
                            $e->setEnd(date_add(clone $e->getStart(), new \DateInterval('PT45M')));
                        if($difficulty == 'average')
                            $e->setEnd(date_add(clone $e->getStart(), new \DateInterval('PT60M')));
                        if($difficulty == 'tough')
                            $e->setEnd(date_add(clone $e->getStart(), new \DateInterval('PT90M')));
                    }
                    /** @var Event[] $study */
                    $study = $c->getEvents()->filter(function (Event $e) {return $e->getType() == 'sr';})->toArray();
                    foreach($study as $e) {
                        if($difficulty == 'easy')
                            $e->setEnd(date_add(clone $e->getStart(), new \DateInterval('PT45M')));
                        if($difficulty == 'average')
                            $e->setEnd(date_add(clone $e->getStart(), new \DateInterval('PT60M')));
                        if($difficulty == 'tough')
                            $e->setEnd(date_add(clone $e->getStart(), new \DateInterval('PT120M')));
                    }
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
                    return $e->getType() == $event['type']
                    && $e->getCourse() == (!empty($event['course']) ? $event['course'] : null)
                    && $e->getDeadline() == (!empty($event['deadline']) ? $event['deadline'] : null);
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
                if(isset($event['remoteId'])) {
                    $lastEvent->setRemoteId($event['remoteId']);
                    $lastEvent->setRemoteUpdated(new \DateTime());
                }
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
            $orm->remove($save);
            $schedule->removeEvent($save);
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
                if(isset($event['remoteId'])) {
                    $newEvent->setRemoteId($event['remoteId']);
                    $newEvent->setRemoteUpdated(new \DateTime());
                }
                $schedule->addEvent($newEvent);
                $orm->persist($newEvent);
                $events[$i] = $newEvent;
            }
            else
            {
                /** @var Event $event */
                $event->setUpdated(new \DateTime());
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
        $grouped = self::groupRecurrenceEvents($schedule->getEvents());
        // sync changes to google
        foreach($grouped as $types) {

            foreach ($types as $type => $hours) {

                foreach ($hours as $recurrence) {

                    /** @var Event $first */
                    $last = end($recurrence);
                    $first = end($last);

                    $max = max(array_map(function ($events) {return max(array_map(function (Event $e) {return $e->getCreated()->getTimestamp();}, $events));}, $recurrence));
                    $last = new \DateTime();
                    $last->setTimestamp($max);
                    $lastModified = $last->format('Ymd') . 'T' . $last->format('His') . 'Z';

                    $id = $first->getId();
                    $title = $first->getTitle();
                    $start = date_timezone_set(clone $first->getStart(), new \DateTimeZone('GMT'))->format('Ymd') . 'T' . date_timezone_set(clone $first->getStart(), new \DateTimeZone('GMT'))->format('His') . 'Z';
                    $end = date_timezone_set(clone $first->getEnd(), new \DateTimeZone('GMT'))->format('Ymd') . 'T' . date_timezone_set(clone $first->getEnd(), new \DateTimeZone('GMT'))->format('His') . 'Z';
                    $created = $first->getCreated()->format('Ymd') . 'T' . $first->getCreated()->format('His') . 'Z';
                    // load alert from settings
                    //ATTENDEE;CUTYPE=INDIVIDUAL;ROLE=REQ-PARTICIPANT;PARTSTAT=ACCEPTED;CN=$name;X-NUM-GUESTS=0:mailto:$email

                    $alert = $first->getAlert() . 'M';
                    $rrules = implode("\r\n", self::getRRules($recurrence));
                    $eventStr = <<<EOEVT
BEGIN:VEVENT
DTSTART:$start
$rrules
DTEND:$end
DTSTAMP:$stamp
ORGANIZER;CN=$name:mailto:$email
UID:STUDYSAUCE-$id
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
            }
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

        if(!empty($request->get('events')))
            self::createStudyEvents($schedule, $request->get('events'), $orm);

        return $this->forward('StudySauceBundle:Plan:index', ['_format' => 'tab']);
    }

    /**
     * @param $merged
     * @return array
     */
    private static function arrayUniqueObj($merged)
    {
        $final  = [];

        foreach ($merged as $current) {
            if ( ! in_array($current, $final)) {
                $final[] = $current;
            }
        }

        return $final;
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
                $existing = array_merge($existing, $course->getEvents()->filter(function (Event $e) use($event){
                    return !$e->getDeleted() && $e->getType() == $event['type'];})->toArray());
            }
            elseif($event['type'] == 'f') {
                $course = null;
                // find earliest class
                $classStart = min(array_map(function (Course $c) {return $c->getStartTime();}, $schedule->getClasses()->toArray()));
                $classEnd = max(array_map(function (Course $c) {return $c->getEndTime();}, $schedule->getClasses()->toArray()));
                $title = 'Free study';
                $existing = array_merge($existing, $schedule->getEvents()->filter(function (Event $e) {
                    return !$e->getDeleted() && $e->getType() == 'f';})->toArray());
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
                if ($t < $classStart->getTimestamp() - 86400 || $t > $classEnd->getTimestamp() + 86400 * 7 * 4) {
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
        self::mergeSaved($schedule, new ArrayCollection(self::arrayUniqueObj($existing)), $events, $orm);
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
        self::mergeSaved($course->getSchedule(), $course->getEvents()->filter(function (Event $e) use ($course) {
            return !$e->getDeleted() && $e->getType() == 'c' && $e->getCourse() == $course;}), $events, $orm);
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
            $event = $schedule->getEvents()->filter(function (Event $e)use($request) {
                return !$e->getDeleted() && $e->getId() == $request->get('eventId');})->first();

        if (empty($event)) {
            return $this->forward('StudySauceBundle:Plan:index', ['_format' => 'tab']);
        }

        // change the original event
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

        $event->setUpdated(new \DateTime());
        $orm->merge($event);

        // change similar future events
        $events = [];
        if($request->get('reoccurring') !== 'false') {
            // move subsequent events of the same type
            $events = $schedule->getEvents()
                ->filter(function (Event $e)use($event, $oldStart) {
                    return !$e->getDeleted() && $e->getType() == $event->getType() &&
                    $e->getCourse() == $event->getCourse() && $e->getStart() > $oldStart &&
                    $e->getId() != $event->getId(); })
                ->toArray();
            usort($events, function (Event $a, Event $b) use ($events) {
                return $a->getStart()->getTimestamp() - $b->getStart()->getTimestamp();
            });
        }

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
                        return !$x->getDeleted() && $x->getId() != $e->getId() && !$e->getDeleted() && $e->getId() != $x->getId() && $x->getType() != 'h'
                            && $x->getType() != 'd' && $x->getType() != 'r' && $x->getType() != 'm'
                            && $x->getType() != 'z'; })
                    ->map(function (Event $e) {return [
                        'start' => $e->getStart(),
                        'end' => $e->getEnd(),
                        'type' => $e->getType()];})->toArray();
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
                            $e->setLocation($request->get('location'));
                        if($request->get('alert') !== null)
                            $e->setAlert($request->get('alert'));
                        if($request->get('title') !== null)
                            $e->setName($request->get('title'));

                        $e->setUpdated(new \DateTime());
                        $orm->merge($e);
                        break;
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
        $event = $schedule->getEvents()->filter(function (Event $e)use($request) {
            return !$e->getDeleted() && $e->getId() == $request->get('eventId');})->first();
        if(empty($event))
            throw new NotFoundHttpException();

        $event->setCompleted($request->get('completed') == 'true');
        $orm->merge($event);
        $orm->flush();

        return new JsonResponse(true);
    }

}



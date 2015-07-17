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
        'Su' => 0,
        'M' => 86400,
        'Tu' => 172800,
        'W' => 259200,
        'Th' => 345600,
        'F' => 432000,
        'Sa' => 518400
    ];

    /**
     * @return array
     */
    private static function holidays()
    {
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

    private static function getInstaceFromId($id, &$parentId = null)
    {
        if(preg_match('/^(.*?)(_([0-9]{8}T[0-9]{6}Z?))*$/i', $id, $matches)) {
            $parentId = $matches[1];
            return isset($matches[3]) ? $matches[3] : null;
        }

        return null;
    }

    /**
     * @param User $user
     * @param ContainerInterface $container
     * @param \Google_Client $client
     * @param \Google_Service_Calendar $service
     */
    public static function syncEvents(
        User $user,
        ContainerInterface $container,
        \Google_Client &$client = null,
        \Google_Service_Calendar &$service = null
    ) {
        // do initial sync
        if (self::getPlanStep($user) !== false) {
            return;
        }
        /** @var $orm EntityManager */
        $orm = $container->get('doctrine')->getManager();
        /** @var $schedule \StudySauce\Bundle\Entity\Schedule */
        $schedule = $user->getSchedules()->first();

        $calendarId = self::getCalendar($user, $container, $client, $service);

        $list = $service->events->listEvents($calendarId);
        /** @var \Google_Service_Calendar_Event[] $items */
        $items = $list->getItems();
        $user->setProperty('eventSync', $list->getNextSyncToken());

        $working = $schedule->getEvents()->filter(
            function (Event $e) {
                return !$e->getDeleted();
            }
        );

        // sync changes from google
        $remoteIds = [];
        usort(
            $items,
            function (\Google_Service_Calendar_Event $item) {
                return !empty(self::getInstaceFromId($item->getId()));
            }
        );
        $deleted = [];
        foreach ($items as $item) {
            $remoteIds[] = $item->getId();
            /** @var \DateTime[] $instances */
            $instances = self::getInstances(
                new \DateTime($item->getStart()->getDateTime(),
                    !empty($item->getStart()->getTimeZone()) ? new \DateTimeZone($item->getStart()->getTimeZone()) : null
                ),
                $item->getRecurrence()
            );
            $instanceId = self::getInstaceFromId($item->getId(), $parentId);
            foreach ($instances as $i) {
                $remoteIds[] = $parentId . '_' . $i->format('Ymd') . 'T' . $i->format('Hise');
            }
            $remoteInstances = $service->events->instances($calendarId, $item->getId())->getItems();
            $remoteInstanceIds = array_map(
                function (\Google_Service_Calendar_Event $instance) {
                    return $instance->getId();
                },
                $remoteInstances
            );
            if(!empty(array_diff($remoteInstanceIds, $remoteIds))) {
                $diff = array_diff($remoteInstanceIds, $remoteIds);
            }

            // find event by remote id
            /** @var Event $parent */
            $parent = $working->filter(
                function (Event $e) use ($parentId) {
                    return $e->getRemoteId() == $parentId;
                }
            )->first();
            /** @var Event[] $children */
            if(!empty($parent)) {
                $children = $working->filter(
                    function (Event $e) use ($parent) {
                        return !empty($e->getRecurrence())
                        && strpos($e->getRecurrence()[0], 'RECURRENCE-ID:' . $parent->getId() . '_') !== false;
                    }
                )->toArray();
            }

            $isNew = false;
            $start = date_timezone_set(new \DateTime(
                $item->getStart()->getDateTime(),
                !empty($item->getStart()->getTimeZone()) ? new \DateTimeZone($item->getStart()->getTimeZone()) : null
            ), new \DateTimeZone(date_default_timezone_get()));
            $end = date_timezone_set(new \DateTime(
                $item->getEnd()->getDateTime(),
                !empty($item->getEnd()->getTimeZone()) ? new \DateTimeZone($item->getEnd()->getTimeZone()) : null
            ), new \DateTimeZone(date_default_timezone_get()));

            // ignore instances that are no longer in series
            if ((new \DateTime($item->getCreated()))->getTimestamp() < $schedule->getCreated()->getTimestamp()) {
                // skip parents that where already deleted
                if(!in_array($parentId, $deleted)) {
                    $deleted[] = $item->getId();
                    $service->events->delete($calendarId, $item->getId());
                }
                if (!empty($parent)) {
                    $orm->remove($parent);
                    if (!empty($parent->getCourse())) {
                        $parent->getCourse()->removeEvent($parent);
                    }
                    $parent->getSchedule()->removeEvent($parent);
                    // remove children of old events
                    foreach ($children as $e) {
                        /** @var Event $e */
                        $orm->remove($e);
                        if (!empty($e->getCourse())) {
                            $e->getCourse()->removeEvent($e);
                        }
                        $e->getSchedule()->removeEvent($e);
                    }
                }
                continue;
            } elseif (empty($parent)) {
                $isNew = true;
                $event = new Event();
                $working->add($event);
                $event->setName(trim(explode(':', $item->getSummary())[0]));
                $event->setSchedule($schedule);
                $schedule->addEvent($event);
                $event->setRecurrence($item->getRecurrence());
                $event->setRemoteId($item->getId());
                // recognize event by name and type in title
                /** @var Course $course */
                if(!empty($course = $schedule->getCourses()->filter(
                        function (Course $c) use ($item) {
                            return strpos($item->getSummary(), $c->getName()) !== false;
                        }
                    )->first())) {
                    $event->setCourse($course);
                    $course->addEvent($event);
                }
                if (strpos(strtolower($item->getSummary()), 'free study') !== false) {
                    $event->setType('f');
                } elseif (strpos(strtolower($item->getSummary()), 'study session') !== false) {
                    $event->setType('sr');
                } elseif (strpos(strtolower($item->getSummary()), 'class') !== false) {
                    $event->setType('c');
                } elseif (strpos(strtolower($item->getSummary()), 'pre-work') !== false) {
                    $event->setType('p');
                    // TODO: check for deadlines
                } else {
                    $event->setType('o');
                }
                // TODO: look up all free study events and ids and make sure there is only one per day?
            } // only update event if the updated timestamp is greater than the studysauce database
            else {
                if (!empty($instanceId)) {
                    // find child event
                    $event = $working->filter(
                        function (Event $e) use ($parent, $instanceId) {
                            return !empty($e->getRecurrence())
                            && strpos($e->getRecurrence()[0], 'RECURRENCE-ID:' . $parent->getId() . '_') !== false
                            && strpos($e->getRecurrence()[0], $instanceId) !== false;
                        }
                    )->first();
                    if (empty($event)) {
                        // create instance event
                        $isNew = true;
                        $event = new Event();
                        $working->add($event);
                        $event->setCourse($parent->getCourse());
                        if(!empty($parent->getCourse())) {
                            $parent->getCourse()->addEvent($event);
                        }
                        $event->setName($parent->getName());
                        $event->setType($parent->getType());
                        $event->setSchedule($parent->getSchedule());
                        $parent->getSchedule()->addEvent($event);
                        $event->setRecurrence(['RECURRENCE-ID:' . $parent->getId() . '_' . $instanceId]);
                    }
                } else {
                    $event = $parent;
                    $event->setRecurrence($item->getRecurrence());
                    // reset all event in series by removing child instances
                    if($start != $event->getStart()) {
                        // update child IDs
                        foreach ($children as $e) {
                            /** @var Event $e */
                            $orm->remove($e);
                            $e->getSchedule()->removeEvent($e);
                        }
                    }
                }

            }

            // update fields
            if (empty($event->getRemoteUpdated())
                || new \DateTime($item->getUpdated()) > $event->getRemoteUpdated()) {
                $event->setName(str_replace([': Pre-work', ': Study session', ': Class'], '', $item->getSummary()));
                $event->setLocation($item->getLocation());

                /** @var \Google_Service_Calendar_EventReminders $reminders */
                $reminders = $item->getReminders();
                if(!empty($reminders->getOverrides()) && isset($reminders->getOverrides()[0]['minutes'])) {
                    $event->setAlert($reminders->getOverrides()[0]['minutes']);
                }
                $event->setStart($start);
                $event->setEnd($end);
                $event->setRemoteUpdated(new \DateTime());
                if ($isNew) {
                    $orm->persist($event);
                } else {
                    $orm->merge($event);
                }
                $orm->flush();
            }
        }

        // sync changes to google
        $sorted = $working->toArray();
        usort(
            $sorted,
            function (Event $e) {
                return !empty($e->getRecurrence()) && substr($e->getRecurrence()[0], 0, 14) == 'RECURRENCE-ID:';
            }
        );
        foreach ($sorted as $event) {
            /** @var Event $event */
            $config = self::getGoogleCalendarConfig($event);
            $newEvent = new \Google_Service_Calendar_Event($config);
            $parent = !empty($event->getRecurrence()) && substr($event->getRecurrence()[0], 0, 14) == 'RECURRENCE-ID:'
                ? $working->filter(
                    function (Event $e) use ($event) {
                        return $e->getId() == substr(
                            $event->getRecurrence()[0],
                            14,
                            strpos($event->getRecurrence()[0], '_')
                        );
                    }
                )->first()
                : $event;
            if (empty($parent->getRemoteId()) || !in_array($parent->getRemoteId(), $remoteIds)) {
                $newEvent = $service->events->insert($calendarId, $newEvent);
                // store remoteId in all existing instances
                $parent->setRemoteUpdated(new \DateTime());
                $parent->setRemoteId($newEvent->getId());
                $orm->merge($parent);
            } elseif (empty($event->getRemoteUpdated()) || !empty($event->getUpdated()) && $event->getUpdated(
                ) > $event->getRemoteUpdated()
            ) {
                $remoteId = $parent->getRemoteId();
                if ($parent != $event) {
                    $remoteId .= '_' . explode('_', $event->getRecurrence()[0])[1];
                }
                $service->events->update($calendarId, $remoteId, $newEvent);
                /*foreach($working->filter(function (Event $e) use ($event) {
                    return !empty($e->getRecurrence()) && strpos($e->getRecurrence()[0], 'RECURRENCE-ID:'
                    && substr($e->getRecurrence()[0], 14, strpos($e->getRecurrence(), '_'));})->toArray() as $child) {
                    /** @var Event $child
                    $child->setRemoteUpdated(new \DateTime());
                    $orm->merge($child);
                }
                */
                $event->setRemoteUpdated(new \DateTime());
                $orm->merge($event);
            }

        }

        $orm->flush();
    }

    /**
     * @param Event $event
     * @return array
     */
    private static function getGoogleCalendarConfig($event)
    {

        $config = [
            'summary' => $event->getTitle(),
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
//                        'attendees' => [[
//                            'email' => $user->getEmail(),
//                            'displayName' => $name,
//                            'responseStatus' => 'accepted',
//                            'optional' => true
//                        ]],
            'colorId' => 6
        ];

        if (!empty($event->getRecurrence()) && substr($event->getRecurrence()[0], 0, 14) != 'RECURRENCE-ID:') {
            $config['recurrence'] = $event->getRecurrence();
        } else {
            $config['recurrence'] = null;
        }

        if (!empty($event->getAlert())) {
            $config['reminders'] = [
                'useDefault' => false,
                'overrides' => [
                    ['method' => 'popup', 'minutes' => $event->getAlert()]
                ],
            ];
        } else {
            $config['reminders'] = null;
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
    public static function getCalendars(
        User $user,
        ContainerInterface $container,
        \Google_Client &$client = null,
        \Google_Service_Calendar &$service = null
    ) {
        require_once(__DIR__ . '/../../../../vendor/google/apiclient/autoload.php');
        $client = new \Google_Client();
        /** @var OAuthHelper $oauth */
        $ownerMap = $container->get('hwi_oauth.resource_ownermap.main');
        /** @var GoogleResourceOwner $resourceOwner */
        $resourceOwner = $ownerMap->getResourceOwnerByName('gcal');
        $client->setAccessType('offline');
        $client->setClientId($resourceOwner->getOption('client_id'));
        $client->setClientSecret($resourceOwner->getOption('client_secret'));
        $client->setAccessToken(
            json_encode(['access_token' => $user->getGcalAccessToken(), 'created' => time(), 'expires_in' => 86400])
        );
        $service = new \Google_Service_Calendar($client);

        try {
            // list calendars so the user can select which calendar to sync with
            $calendars = $service->calendarList->listCalendarList();
        } catch (\Exception $e) {
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
    public static function getCalendar(
        User $user,
        ContainerInterface $container,
        \Google_Client &$client = null,
        \Google_Service_Calendar &$service = null
    ) {

        /** @var $orm EntityManager */
        $orm = $container->get('doctrine')->getManager();

        $calendars = self::getCalendars($user, $container, $client, $service);
        /** @var \Google_Client $client */
        /** @var \Google_Service_Calendar $service */
        $id = '';
        foreach ($calendars->getItems() as $cal) {
            /** @var \Google_Service_Calendar_CalendarListEntry $cal */
            if ($cal->getId() == $user->getProperty('calendarId') || $cal->getSummary() == 'StudySauce') {
                $id = $cal->getId();
                break;
            }
        }

        // check if studysauce calendar already exists
        if (empty($user->getProperty('calendarId')) || empty($id)) {
            $calendar = new \Google_Service_Calendar_Calendar();
            $calendar->setSummary('StudySauce');
            $calendar->setDescription(
                'Take notes with your classes using StudySauce, check-in to track your studying.'
            );
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
        if (!empty($context = $container->get('security.context')) && !empty($token = $context->getToken()) &&
            !empty($user = $token->getUser()) && $user->hasRole('ROLE_DEMO')
        ) {
            $guest = $user;
        } else {
            $guest = $userManager->findUserByUsername('guest');
        }

        $schedule = $guest->getSchedules()->first();
        $eventInfo = [];
        foreach ($schedule->getClasses()->toArray() as $c) {
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
                $classT->setTime(
                    $c->getStartTime()->format('H'),
                    $c->getStartTime()->format('i'),
                    $c->getStartTime()->format('s')
                );

                $eventInfo[] = [
                    'courseId' => $c->getId(),
                    'type' => 'p',
                    'start' => date_timestamp_set(clone $classT, $classT->getTimestamp() - 86400 + 4 * 3600)->format(
                        'r'
                    ),
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
        if (empty($user)) {
            $user = $this->getUser();
        }

        /** @var $schedule \StudySauce\Bundle\Entity\Schedule */
        $schedule = $user->getSchedules()->first();

        // get demo schedule instead
        $isDemo = false;
        $isEmpty = false;
        if (empty($schedule) || empty($schedule->getClasses()->count()) ||
            !$user->hasRole('ROLE_PAID') || !$schedule->getClasses()->exists(
                function ($_, Course $c) {
                    return $c->getEndTime() > new \DateTime();
                }
            )
        ) {
            $schedule = ScheduleController::getDemoSchedule($this->container);
            if ($user->hasRole('ROLE_PAID')) {
                $isEmpty = true;
            } else {
                $isDemo = true;
            }
            self::createDemoEvents($this->container);
        }

        // get events for current week
        $emails = new EmailsController();
        $emails->setContainer($this->container);
        $courses = $schedule->getClasses()->toArray();
        $step = self::getPlanStep($user);
        foreach ($schedule->getClasses()->toArray() as $c) {
            /** @var Course $c */
            $existing = $c->getEvents()->filter(
                function (Event $e) {
                    return $e->getType() == 'c';
                }
            )->toArray();
            if (empty($existing)) {
                self::createCourseEvents($c, $orm);
            }
        }

        // list oauth services
        /** @var OAuthHelper $oauth */
        $oauth = $this->get('hwi_oauth.templating.helper.oauth');
        $services = [];
        foreach ($oauth->getResourceOwners() as $o) {
            if ($o != 'gcal') {
                continue;
            }
            $services[$o] = $oauth->getLoginUrl($o);
        }

        return $this->render(
            'StudySauceBundle:' . $template[0] . ':' . $template[1] . '.html.php',
            [
                'schedule' => $schedule,
                'courses' => array_values($courses),
                'jsonEvents' => self::getJsonEvents($schedule->getEvents()),
                'user' => $user,
                'overlap' => false,
                'step' => $step,
                'isDemo' => $isDemo,
                'isEmpty' => $isEmpty,
                'services' => $services
            ]
        );
    }

    /**
     * @param User $user
     * @return bool|string
     */
    public static function getPlanStep(User $user)
    {
        /** @var $schedule \StudySauce\Bundle\Entity\Schedule */
        $schedule = $user->getSchedules()->first() ?: new Schedule();

        if ($schedule->getClasses()->exists(
            function ($_, Course $c) {
                return empty($c->getStudyDifficulty());
            }
        )
        ) {
            return 1;
        } elseif ($schedule->getClasses()->exists(
                function ($_, Course $c) {
                    return $c->getStudyDifficulty() != 'none' && !$c->getEvents()->exists(
                        function ($_, Event $e) {
                            return $e->getType() == 'p';
                        }
                    );
                }
            ) ||
            $schedule->getClasses()->exists(
                function ($_, Course $c) {
                    return $c->getStudyDifficulty() != 'none' && !$c->getEvents()->exists(
                        function ($_, Event $e) {
                            return $e->getType() == 'sr';
                        }
                    );
                }
            )
        ) {
            return 2;
        } elseif ($schedule->getClasses()->exists(
            function ($_, Course $c) {
                return empty($c->getStudyType());
            }
        )
        ) {
            return 4;
        }

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
        } else {
            $events = $schedule->getEvents()->toArray();
        }

        return $this->render(
            'StudySauceBundle:Plan:widget.html.php',
            [
                'isDemo' => $isDemo,
                'events' => $events,
                'user' => $user
            ]
        );
    }

    /**
     * @param Collection $events
     * @return array
     */
    public static function getJsonEvents(Collection $events)
    {
        $jsEvents = [];
        foreach ($events->filter(
            function (Event $e) {
                return empty($e->getRecurrence()) || substr($e->getRecurrence()[0], 0, 5) == 'RRULE';
            }
        )->toArray() as $x) {
            /** @var Event $x */
            // skip data entry events
            if ($x->getDeleted()) {
                continue;
            }

            $label = '';
            $skip = false;
            switch ($x->getType()) {
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
            if ($skip) {
                continue;
            }

            /** @var \DateTime[] $instances */
            $instances = self::getInstances($x->getStart()->setTimezone(new \DateTimeZone('Z')), $x->getRecurrence());

            foreach ($instances as $start) {

                $id = $x->getId() . '_' . $start->format('Ymd') . 'T' . $start->format('Hise');
                $end = date_add(
                    clone $start,
                    new \DateInterval('PT' . ($x->getEnd()->getTimestamp() - $x->getStart()->getTimestamp()) . 'S')
                );

                // check database for event id
                if (!empty($child = $events->filter(
                    function (Event $e) use ($id) {
                        return !empty($e->getRecurrence()) && strpos($e->getRecurrence()[0], $id) !== false;
                    }
                )->first())
                ) {
                    $start = $child->getStart();
                    $end = $child->getEnd();
                }
                else {
                    $child = $x;
                }

                // set up dates recurrence
                if (!empty($child->getCourse()) && $child->getType() == 'sr') {
                    $startDay = $child->getCourse()->getStartTime()->getTimestamp();
                    $endDay = $child->getCourse()->getEndTime()->getTimestamp();
                    $t = $child->getStart()->getTimestamp();
                    $dates = [];
                    if ($t <= $endDay) {
                        $dates[] = date('n/d', $t);
                    }
                    if ($t - 86400 * 7 >= $startDay && $t - 86400 * 7 <= $endDay) {
                        $dates[] = date('n/d', $t - 86400 * 7);
                    }
                    if ($t - 86400 * 14 >= $startDay && $t - 86400 * 14 <= $endDay) {
                        $dates[] = date('n/d', $t - 86400 * 14);
                    }
                    if ($t - 86400 * 28 >= $startDay && $t - 86400 * 28 <= $endDay) {
                        $dates[] = date('n/d', $t - 86400 * 28);
                    }
                }

                $newEvent = [
                    'eventId' => $id,
                    'title' => '<h4>' . $label . '</h4>' . $child->getName(),
                    'start' => $start->format('r'),
                    'end' => $end->format('r'),
                    'className' => 'event-type-' . $child->getType() . ' ' . (!empty($child->getCourse()) &&
                        $child->getCourse()->getIndex() !== false
                            ? ('class' . $child->getCourse()->getIndex())
                            : '') . ' ' . (!empty($child->getCourse())
                            ? ('course-id-' . $child->getCourse()->getId())
                            : ''),
                    // all day for deadlines, reminders, and holidays
                    'allDay' => $child->getType() == 'd' || $child->getType() == 'h' ||
                        $child->getType() == 'r',
                    'editable' => ($child->getType() == 'sr' || $child->getType() == 'f' || $child->getType() == 'p'),
                    'dates' => isset($dates) ? $dates : null,
                    'alert' => $child->getAlert(),
                    'location' => $child->getLocation()
                ];
                if (!empty($child->getCourse())) {
                    $newEvent['courseId'] = $child->getCourse()->getId();
                }
                $jsEvents[] = $newEvent;
            }
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

            $event = $schedule->getEvents()->filter(
                function (Event $e) use ($d) {
                    return $e->getDeadline()->getId() == $d->getId();
                }
            )->first();
            if (empty($event)) {

            }
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
        if (!empty($schedule)) {
            if (!empty($alerts = $request->get('alerts'))) {
                $toSave = [];
                foreach (['c', 'p', 'sr', 'f', 'o'] as $t) {
                    if (isset($alerts[$t])) {
                        $toSave[$t] = intval($alerts[$t]);
                    } else {
                        $toSave[$t] = 15;
                    }
                }
                foreach ($schedule->getEvents()->toArray() as $e) {
                    /** @var Event $e */
                    $e->setUpdated(new \DateTime());
                    $orm->merge($e);
                }
                $schedule->setAlerts($toSave);
                $orm->merge($schedule);
            }
            $courses = $schedule->getClasses()->toArray();
            foreach ($courses as $i => $c) {
                /** @var Course $c */
                if (!empty($request->get('profile-type-' . $c->getId()))) {
                    $c->setStudyType($request->get('profile-type-' . $c->getId()));
                    $orm->merge($c);
                }
                if (!empty($request->get('profile-difficulty-' . $c->getId()))) {
                    $difficulty = $request->get('profile-difficulty-' . $c->getId());
                    $c->setStudyDifficulty($difficulty);
                    if($difficulty == 'none') {
                        foreach($c->getEvents()->toArray() as $e) {
                            if($e->getType() == 'p' || $e->getType() == 'sr') {
                                $orm->remove($e);
                                $c->removeEvent($e);
                                $schedule->removeEvent($e);
                            }
                        }
                        continue;
                    }
                    /** @var Event[] $prework */
                    $prework = $c->getEvents()->filter(
                        function (Event $e) {
                            return $e->getType() == 'p';
                        }
                    )->toArray();
                    foreach ($prework as $e) {
                        if ($difficulty == 'easy') {
                            $e->setEnd(date_add(clone $e->getStart(), new \DateInterval('PT45M')));
                        }
                        if ($difficulty == 'average') {
                            $e->setEnd(date_add(clone $e->getStart(), new \DateInterval('PT60M')));
                        }
                        if ($difficulty == 'tough') {
                            $e->setEnd(date_add(clone $e->getStart(), new \DateInterval('PT90M')));
                        }
                        $e->setUpdated(new \DateTime());
                        $orm->merge($e);
                    }
                    /** @var Event[] $study */
                    $study = $c->getEvents()->filter(
                        function (Event $e) {
                            return $e->getType() == 'sr';
                        }
                    )->toArray();
                    foreach ($study as $e) {
                        if ($difficulty == 'easy') {
                            $e->setEnd(date_add(clone $e->getStart(), new \DateInterval('PT45M')));
                        }
                        if ($difficulty == 'average') {
                            $e->setEnd(date_add(clone $e->getStart(), new \DateInterval('PT60M')));
                        }
                        if ($difficulty == 'tough') {
                            $e->setEnd(date_add(clone $e->getStart(), new \DateInterval('PT120M')));
                        }
                        $e->setUpdated(new \DateTime());
                        $orm->merge($e);
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
     * @return Response
     */
    public function downloadAction()
    {
        /** @var \StudySauce\Bundle\Entity\User $user */
        $user = $this->getUser();
        $email = $user->getEmail();
        $name = $user->getFirst() . ' ' . $user->getLast();
        $now = new \DateTime('now', new \DateTimeZone('Z'));
        $stamp = $now->format('Ymd') . 'T' . $now->format('Hise');
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
        // sync changes to google
        foreach ($schedule->getEvents()->toArray() as $event) {
            /** @var Event $event */
            $rrules = $event->getRecurrence();
            if(!empty($rrules)) {
                $rrules = implode("\r\n", $rrules);
                if(strpos($rrules, 'RECURRENCE-ID:') !== false) {
                    $rrules = explode('_', $rrules)[1];
                }
            }
            $id = $event->getId();
            $title = $event->getTitle();
            $start = $event->getStart()->format('Ymd') . 'T' . $event->getStart()->format('His');
            $end = $event->getEnd()->format('Ymd') . 'T' . $event->getEnd()->format('His');
            $alert = $event->getAlert() . 'M';
            $created = date_timezone_set(clone $event->getCreated(), new \DateTimeZone('Z'));
            $created = $created->format('Ymd') . 'T' . $created->format('Hise');
            $modified = date_timezone_set(empty($event->getUpdated()) ? clone $event->getCreated() : clone $event->getUpdated(), new \DateTimeZone('Z'));
            $lastModified = $modified->format('Ymd') . 'T' . $modified->format('Hise');
            $location = $event->getLocation();
            $eventStr = <<<EOEVT
BEGIN:VEVENT
DTSTART;TZID=America/Phoenix:$start
DTEND;TZID=America/Phoenix:$end
DTSTAMP:$stamp
$rrules
ORGANIZER;CN=$name:mailto:$email
UID:STUDYSAUCE-$id@studysauce.com
CREATED:$created
DESCRIPTION:Log in to studysauce.com to take notes
LAST-MODIFIED:$lastModified
LOCATION:$location
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
        $response->setContent(
            $calendar . '
END:VCALENDAR'
        );

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

        if (!empty($request->get('events'))) {
            self::createStudyEvents($schedule, $request->get('events'), $orm);
        }

        return $this->forward('StudySauceBundle:Plan:index', ['_format' => 'tab']);
    }

    /**
     * @param Schedule $schedule
     * @param $eventInfo
     * @param EntityManager $orm
     */
    public static function createStudyEvents(Schedule $schedule, $eventInfo, EntityManager $orm)
    {
        // delete previous course events
        foreach ($eventInfo as $event) {
            if (!empty($event['courseId'])) {
                /** @var Course $course */
                $course = $schedule->getClasses()->filter(
                    function (Course $c) use ($event) {
                        return $c->getId() == $event['courseId'];
                    }
                )->first();
                if (empty($course)) {
                    continue;
                }
                foreach ($course->getEvents()->toArray() as $e) {
                    /** @var Event $e */
                    if ($e->getType() == $event['type']) {
                        $orm->remove($e);
                        $course->removeEvent($e);
                        $schedule->removeEvent($e);
                    }
                }
            } elseif ($event['type'] == 'f') {
                foreach ($schedule->getEvents()->toArray() as $e) {
                    /** @var Event $e */
                    if ($e->getType() == $event['type']) {
                        $orm->remove($e);
                        $schedule->removeEvent($e);
                    }
                }
            } else {
                continue;
            }
        }
        $orm->flush();

        // create new events
        foreach ($eventInfo as $event) {
            if (!empty($event['courseId'])) {
                /** @var Course $course */
                $course = $schedule->getClasses()->filter(
                    function (Course $c) use ($event) {
                        return $c->getId() == $event['courseId'];
                    }
                )->first();
                if (empty($course)) {
                    continue;
                }

                $newEvent = new Event();
                $newEvent->setCourse($course);
                $course->addEvent($newEvent);
                $newEvent->setName($course->getName());
                $start = clone $course->getStartTime();
                $end = clone $course->getEndTime();
            } elseif ($event['type'] == 'f') {

                $newEvent = new Event();
                $newEvent->setType($event['type']);
                $newEvent->setName('Free study');
                $start = date_timestamp_set(
                    new \DateTime(),
                    min(
                        array_map(
                            function (Course $c) {
                                return $c->getStartTime()->getTimestamp();
                            },
                            $schedule->getClasses()->toArray()
                        )
                    )
                );
                $end = date_timestamp_set(
                    new \DateTime(),
                    max(
                        array_map(
                            function (Course $c) {
                                return $c->getEndTime()->getTimestamp();
                            },
                            $schedule->getClasses()->toArray()
                        )
                    )
                );
            } else {
                continue;
            }

            $newEvent->setType($event['type']);
            $newEvent->setSchedule($schedule);
            $schedule->addEvent($newEvent);
            $newStart = date_timezone_set(
                new \DateTime($event['start']),
                new \DateTimeZone(date_default_timezone_get())
            );
            $newEnd = date_timezone_set(new \DateTime($event['end']), new \DateTimeZone(date_default_timezone_get()));
            if (date_sub(clone $newStart, new \DateInterval('P7D')) > date_sub($start, new \DateInterval('P1D'))) {
                $newStart = date_sub($newStart, new \DateInterval('P7D'));
                $newEnd = date_sub($newEnd, new \DateInterval('P7D'));
            }
            $newEvent->setStart($newStart);
            $newEvent->setEnd($newEnd);
            // TODO: set alert and locations
            // TODO: add exceptions for holidays
            $newEvent->setRecurrence(
                [
                    'RRULE:FREQ=WEEKLY' .
                    ';UNTIL=' . date_timestamp_set(
                        new \DateTime(),
                        strtotime('this Sunday', $end->getTimestamp()) + array_values(
                            self::$weekConversion
                        )[$newStart->format('w')]
                    )->format('Ymd') . 'T000000Z' .
                    ';BYDAY=' . strtoupper(substr($newStart->format('D'), 0, 2))
                ]
            );
            $orm->persist($newEvent);
            $orm->flush();
        }
    }

    /**
     * @param Course $course
     * @param EntityManager $orm
     */
    public static function createCourseEvents(Course $course, EntityManager $orm)
    {
        $days = ['SU' => 'Su', 'MO' => 'M', 'TU' => 'Tu', 'WE' => 'W', 'TH' => 'Th', 'FR' => 'F', 'SA' => 'Sa'];
        // delete previous course events
        foreach ($course->getEvents()->toArray() as $e) {
            /** @var Event $e */
            if ($e->getType() == 'c') {
                $orm->remove($e);
                $course->removeEvent($e);
                $course->getSchedule()->removeEvent($e);
            }
        }

        $event = new Event();
        $event->setType('c');
        $event->setCourse($course);
        $course->addEvent($event);
        $event->setSchedule($course->getSchedule());
        $course->getSchedule()->addEvent($event);

        $event->setName($course->getName());
        $firstDay = min(
            array_map(
                function ($d) use ($course) {
                    return ($next = $course->getStartTime()->getTimestamp()
                        - array_values(self::$weekConversion)[$course->getStartTime()->format('w')]
                        + self::$weekConversion[$d]) < $course->getStartTime()->getTimestamp()
                        ? $next + 608400
                        : $next;
                },
                $course->getDotw()
            )
        );
        $event->setStart(date_timestamp_set(new \DateTime(), $firstDay));
        $event->setEnd(date_add(clone $event->getStart(), new \DateInterval('PT' . $course->getLength() . 'S')));
        // TODO: add exceptions for holidays
        $event->setRecurrence(
            [
                'RRULE:FREQ=WEEKLY' .
                ';UNTIL=' . date_add(
                    date_timezone_set(clone $course->getEndTime(), new \DateTimeZone('Z')),
                    new \DateInterval('P1D')
                )->format('Ymd') . 'T000000Z' .
                ';BYDAY=' . implode(',', array_keys(array_intersect($days, $course->getDotw())))
            ]
        );
        $orm->persist($event);
        $orm->flush();
    }

    private static function getInstances(\DateTime $start, $rrules)
    {
        $instances = [clone $start];
        if (empty($rrules) || strpos($rrules[0], 'RECURRENCE-ID:') !== false) {
            return $instances;
        }
        $days = ['SU' => 0, 'MO' => 1, 'TU' => 2, 'WE' => 3, 'TH' => 4, 'FR' => 5, 'SA' => 6];
        foreach ($rrules as $r) {
            if (preg_match('/freq=weekly/i', $r, $matches)) {
                $freq = 604800;
            }
            if (preg_match('/until=(.*?Z)/i', $r, $matches)) {
                $until = new \DateTime($matches[1]);
            }
            if (preg_match('/byday=([a-z\,]*)/i', $r, $matches)) {
                $dotw = explode(',', $matches[1]);
            }
            if (isset($until) && isset($dotw) && isset($freq)) {
                for ($t = $start->getTimestamp(); $t <= $until->getTimestamp(); $t += $freq) {
                    $sunday = $t - array_values(self::$weekConversion)[$start->format('w')];
                    foreach ($dotw as $d) {
                        $newDate = date_timestamp_set(
                            new \DateTime('now', $start->getTimezone()),
                            $sunday + array_values(self::$weekConversion)[$days[$d]]
                        );
                        if (!in_array($newDate, $instances)) {
                            $instances[] = $newDate;
                        }
                    }
                }
            }
        }

        return $instances;
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

        /** @var Event $parent */
        if (!empty($schedule)) {
            $parent = $schedule->getEvents()->filter(
                function (Event $e) use ($request) {
                    return $e->getId() == explode('_', $request->get('eventId'))[0];
                }
            )->first();
        }

        if (empty($parent)) {
            return $this->forward('StudySauceBundle:Plan:index', ['_format' => 'tab']);
        }

        $isNew = false;
        $newStart = date_timezone_set(
            new \DateTime($request->get('start')),
            new \DateTimeZone(date_default_timezone_get())
        );
        $newEnd = date_timezone_set(
            new \DateTime($request->get('end')),
            new \DateTimeZone(date_default_timezone_get())
        );
        $original = new \DateTime(explode('_', $request->get('eventId'))[1]);

        if ($request->get('reoccurring') !== 'false') {
            // get original instance day and change days
            if (strpos($parent->getRecurrence()[0], strtoupper(substr($newStart->format('D'), 0, 2))) === false) {
                // change original day to new day of the week,
                // unless current event was moved before make that the new time
                $parent->setRecurrence(
                    str_replace(
                        strtoupper(substr($original->format('D'), 0, 2)),
                        strtoupper(substr($newStart->format('D'), 0, 2)),
                        $parent->getRecurrence()
                    )
                );
            }

            // select the earliest date, either the original day with new time or the new day and time
            if ($newStart < $parent->getStart()) {
                $parent->setStart($newStart);
                $parent->setEnd($newEnd);
            } else {
                // only change times
                $newStart = date_time_set(
                    clone $parent->getStart(),
                    $newStart->format('H'),
                    $newStart->format('i'),
                    $newStart->format('s')
                );
                $newEnd = date_time_set(
                    clone $parent->getEnd(),
                    $newEnd->format('H'),
                    $newEnd->format('i'),
                    $newEnd->format('s')
                );
                $parent->setStart($newStart);
                $parent->setEnd($newEnd);
            }
            // remove out of series children
            // TODO: does this only work if we are changing days?
            $children = $schedule->getEvents()->filter(function (Event $e) use ($parent) {!empty($e->getRecurrence()) && strpos(
                $e->getRecurrence()[0],
                'RECURRENCE-ID:' . $parent->getId() . '_'
            ) !== false;})->toArray();
            foreach ($children as $e) {
                /** @var Event $e */
                $orm->remove($e);
                $e->getSchedule()->removeEvent($e);
            }
            $event = $parent;
        } else {
            // add event out of series
            /** @var Event $event */
            $event = $schedule->getEvents()->filter(
                function (Event $e) use ($request) {
                    return !empty($e->getRecurrence()) && strpos(
                        $e->getRecurrence()[0],
                        $request->get('eventId')
                    ) !== false;
                }
            )->first();
            if (empty($event)) {
                $isNew = true;
                $event = new Event();
                $event->setCourse($parent->getCourse());
                if(!empty($parent->getCourse())) {
                    $parent->getCourse()->addEvent($event);
                }
                $event->setDeadline($parent->getDeadline());
                $event->setType($parent->getType());
                $event->setSchedule($parent->getSchedule());
                $parent->getSchedule()->addEvent($event);
                $event->setName($parent->getName());
                $event->setRecurrence(
                    [
                        'RECURRENCE-ID:' . $parent->getId() . '_' . $original->format('Ymd') . 'T' . $original->format(
                            'Hise'
                        )
                    ]
                );
            }
            $event->setStart($newStart);
            $event->setEnd($newEnd);
        }
        if ($request->get('location') !== null) {
            $event->setLocation($request->get('location'));
        }
        if ($request->get('alert') !== null) {
            $event->setAlert($request->get('alert'));
        }
        if ($request->get('title') !== null) {
            $event->setName($request->get('title'));
        }

        $event->setUpdated(new \DateTime());

        if ($isNew) {
            $orm->persist($event);
        } else {
            $orm->merge($event);
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
        $event = $schedule->getEvents()->filter(
            function (Event $e) use ($request) {
                return $e->getId() == $request->get('eventId');
            }
        )->first();
        if (empty($event)) {
            throw new NotFoundHttpException();
        }

        $event->setCompleted($request->get('completed') == 'true');
        $orm->merge($event);
        $orm->flush();

        return new JsonResponse(true);
    }

}



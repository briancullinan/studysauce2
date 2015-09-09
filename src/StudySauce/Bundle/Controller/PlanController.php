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
use Symfony\Component\HttpFoundation\StreamedResponse;
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

    public static function expandStudyEvents(Course $course, Schedule $schedule, EntityManager $orm)
    {
        // get the last closest event to each day of the week
        if ($course->getEvents()->filter(
                function (Event $e) {
                    return $e->getType() == 'sr' || $e->getType() == 'p';
                }
            )->count() == 0
        ) {
            return;
        }
        $eventInfo = [];
        foreach ($course->getDotw() as $d) {
            $classT = date_time_set(
                date_timestamp_set(
                    new \DateTime(),
                    strtotime('last Sunday', $course->getStartTime()->getTimestamp())
                    + PlanController::$weekConversion[$d]
                ),
                $course->getStartTime()->format('H'),
                $course->getStartTime()->format('i'),
                $course->getStartTime()->format('s')
            );
            $classDiff = $classT->getTimestamp() - strtotime('last Sunday', $classT->getTimestamp());
            $closestP = -86400;
            $closestSR = 86400;
            foreach ($course->getEvents()->toArray() as $e) {
                /** @var Event $e */
                /** @var Event $closestP */
                $beginning = $e->getStart()->getTimestamp() - strtotime('last Sunday', $e->getStart()->getTimestamp());
                if ($e->getType(
                    ) == 'p' && (!isset($closestP) || $beginning - $classDiff > $closestP && $beginning - $classDiff < 0)
                ) {
                    $closestP = $beginning - $classDiff;
                }
                if ($e->getType(
                    ) == 'sr' && (!isset($closestSR) || $beginning - $classDiff < $closestSR && $beginning - $classDiff > 0)
                ) {
                    $closestSR = $beginning - $classDiff;
                }
            }
            $eventInfo[] = [
                'courseId' => $course->getId(),
                'type' => 'p',
                'start' => date_timestamp_set(clone $classT, $classT->getTimestamp() + $closestP)->format('r'),
                'end' => date_add(
                    date_timestamp_set(clone $classT, $classT->getTimestamp() + $closestP),
                    new \DateInterval('PT60M')
                )->format('r')
            ];
            $eventInfo[] = [
                'courseId' => $course->getId(),
                'type' => 'sr',
                'start' => date_timestamp_set(clone $classT, $classT->getTimestamp() + $closestSR)->format('r'),
                'end' => date_add(
                    date_timestamp_set(clone $classT, $classT->getTimestamp() + $closestSR),
                    new \DateInterval('PT60M')
                )->format('r')
            ];
        }
        // get final weeks free study events to extend
        $freeStudy = [];
        foreach ($schedule->getEvents()->toArray() as $e) {
            /** @var Event $e */
            if ($e->getType() == 'f') {
                $freeStudy[$e->getStart()->format('W')][$e->getStart()->format('w')] = $e;
            }
        }
        $mostF = '';
        foreach ($freeStudy as $w => $f) {
            if (empty($mostF) || count($f) > count($freeStudy[$mostF])) {
                $mostF = $w;
            }
        }
        if (isset($freeStudy[$mostF])) {
            foreach ($freeStudy[$mostF] as $f) {
                // add free study time of week to schedule start time of week for expansion
                /** @var Event $f */
                $freeDiff = $f->getStart()->getTimestamp() - strtotime('last Sunday', $f->getStart()->getTimestamp());
                $freeT = strtotime('last Sunday', $schedule->getStart()->getTimestamp()) + $freeDiff;
                $eventInfo[] = [
                    'courseId' => null,
                    'type' => 'f',
                    'start' => date_timestamp_set(new \DateTime(), $freeT)->format('r'),
                    'end' => date_add(date_timestamp_set(new \DateTime(), $freeT), new \DateInterval('PT60M'))->format(
                        'r'
                    )
                ];
            }
        }
        self::createStudyEvents($schedule, $eventInfo, $orm);
    }

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
        if (preg_match('/^(.*?)(_([0-9]{8}T[0-9]{6}Z?))*$/i', $id, $matches)) {
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
        $eventTimestamps = $schedule->getCourses()->map(
            function (Course $c) {
                return $c->getCreated()->getTimestamp();
            }
        )->toArray();
        if (empty($eventTimestamps)) {
            $eventTimestamps = [$schedule->getCreated()->getTimestamp()];
        }
        $syncCreated = count($eventTimestamps) < 2 ? $eventTimestamps[0] : min($eventTimestamps);

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

            $instanceId = self::getInstaceFromId($item->getId(), $parentId);
            // find event by remote id
            /** @var Event $parent */
            $parent = $schedule->getEvents()->filter(
                function (Event $e) use ($parentId) {
                    return $e->getRemoteId() == $parentId;
                }
            )->first();

            /** @var Event[] $children */
            if (!empty($parent)) {
                $children = $schedule->getEvents()->filter(
                    function (Event $e) use ($parent) {
                        return !empty($e->getRecurrence())
                        && strpos($e->getRecurrence()[0], 'RECURRENCE-ID:' . $parent->getId() . '_') !== false;
                    }
                )->toArray();
            }
            // add a deleted record for events that are cancelled
            if ($item->getStatus() == 'cancelled') {
                /** @var Event $cancelled */
                $cancelled = $schedule->getEvents()->filter(
                    function (Event $e) use ($parent, $instanceId) {
                        return !empty($e->getRecurrence())
                        && strpos($e->getRecurrence()[0], 'RECURRENCE-ID:' . $parent->getId() . '_') !== false
                        && strpos($e->getRecurrence()[0], $instanceId) !== false;
                    }
                )->first();
                if (empty($cancelled)) {
                    $cancelled = new Event();
                    $cancelled->setCourse($parent->getCourse());
                    if (!empty($parent->getCourse())) {
                        $parent->getCourse()->addEvent($cancelled);
                    }
                    $cancelled->setDeadline($parent->getDeadline());
                    $cancelled->setName($parent->getName());
                    $cancelled->setType($parent->getType());
                    $cancelled->setSchedule($parent->getSchedule());
                    $parent->getSchedule()->addEvent($cancelled);
                    $cancelled->setRecurrence(['RECURRENCE-ID:' . $parent->getId() . '_' . $instanceId]);
                    $cancelled->setRemoteUpdated(
                        date_timezone_set(
                            new \DateTime($item->getUpdated()),
                            new \DateTimeZone(date_default_timezone_get())
                        )
                    );
                    $cancelled->setDeleted(true);
                    $cancelled->setStart($parent->getStart());
                    $cancelled->setEnd($parent->getEnd());
                    $orm->persist($cancelled);
                } else {
                    $cancelled->setDeleted(true);
                    $orm->merge($cancelled);
                }
                $orm->flush();
                continue;
            }

            $start = date_timezone_set(
                new \DateTime(
                    $item->getStart()->getDateTime(),
                    !empty($item->getStart()->getTimeZone()) ? new \DateTimeZone(
                        $item->getStart()->getTimeZone()
                    ) : null
                ),
                new \DateTimeZone(date_default_timezone_get())
            );
            $end = date_timezone_set(
                new \DateTime(
                    $item->getEnd()->getDateTime(),
                    !empty($item->getEnd()->getTimeZone()) ? new \DateTimeZone($item->getEnd()->getTimeZone()) : null
                ),
                new \DateTimeZone(date_default_timezone_get())
            );

            // delete instances that were created before the current calendar
            if ((new \DateTime($item->getCreated()))->getTimestamp() < $syncCreated) {
                // skip parents that where already deleted
                if (!in_array($parentId, $deleted)) {
                    $deleted[] = $item->getId();
                    $service->events->delete($calendarId, $item->getId());
                }
                continue;
                // TODO: merge remote events with local events
            } elseif (empty($parent)) {

                // recognize event by name and type in title
                /** @var Course $course */
                $course = $schedule->getCourses()->filter(
                    function (Course $c) use ($item) {
                        return strpos($item->getSummary(), $c->getName()) !== false;
                    }
                )->first();

                // try to match type
                if (strpos(strtolower($item->getSummary()), 'free study') !== false) {
                    $type = 'f';
                } elseif (strpos(strtolower($item->getSummary()), 'study session') !== false) {
                    $type = 'sr';
                } elseif (strpos(strtolower($item->getSummary()), 'class') !== false) {
                    $type = 'c';
                } elseif (strpos(strtolower($item->getSummary()), 'pre-work') !== false) {
                    $type = 'p';
                    // TODO: check for deadlines
                } else {
                    $type = 'o';
                }

                // try to find event in existing because we know some types of events on have specific occurrences
                $event = $schedule->getEvents()->filter(
                    function (Event $e) use ($parent, $instanceId, $items, $type, $course) {

                        return $e->getType() == $type && $e->getCourse() == $course &&
                            // pick a parent event
                            (empty($e->getRecurrence()) || strpos($e->getRecurrence()[0], 'RECURRENCE-ID:') === false) &&
                            // without a remote id or remote id no longer exists in our list
                            (empty($e->getRemoteId()) || !in_array($e->getRemoteId(), array_map(function (\Google_Service_Calendar_Event $e) { return $e->getId();}, $items)));
                    }
                )->first();
                $isNew = false;
                if(empty($event)) {
                    $isNew = true;
                    $event = new Event();
                    $event->setSchedule($schedule);
                    $schedule->addEvent($event);
                    if(!empty($course)) {
                        $event->setCourse($course);
                        $course->addEvent($event);
                    }
                    $event->setRemoteId($item->getId());
                    $event->setType($type);
                }
                $event->setName(trim(explode(':', $item->getSummary())[0]));
                $event->setRecurrence($item->getRecurrence());
                // TODO: look up all free study events and ids and make sure there is only one per day?
            } // only update event if the updated timestamp is greater than the studysauce database
            else {
                // update the instance from remote to local
                $isNew = false;
                if (!empty($instanceId)) {
                    // find child event
                    $event = $schedule->getEvents()->filter(
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
                        $event->setCourse($parent->getCourse());
                        if (!empty($parent->getCourse())) {
                            $parent->getCourse()->addEvent($event);
                        }
                        $event->setDeadline($parent->getDeadline());
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
                    if ($start != $event->getStart()) {
                        // update child IDs
                        foreach ($children as $e) {
                            /** @var Event $e */
                            $orm->remove($e);
                            if (!empty($e->getCourse())) {
                                $e->getCourse()->removeEvent($e);
                            }
                            $e->getSchedule()->removeEvent($e);
                        }
                    }
                }
            }
            $remoteIds[] = $item->getId();

            // update changes from remote to local
            if (empty($event->getRemoteUpdated())
                || new \DateTime($item->getUpdated()) > $event->getRemoteUpdated()
            ) {
                $event->setName(str_replace([': Pre-work', ': Study session', ': Class'], '', $item->getSummary()));
                $event->setLocation($item->getLocation());

                /** @var \Google_Service_Calendar_EventReminders $reminders */
                $reminders = $item->getReminders();
                if (!empty($reminders->getOverrides()) && isset($reminders->getOverrides()[0]['minutes'])) {
                    $event->setAlert($reminders->getOverrides()[0]['minutes']);
                }
                $event->setStart($start);
                $event->setEnd($end);
                $event->setRemoteUpdated(
                    date_timezone_set(
                        new \DateTime($item->getUpdated()),
                        new \DateTimeZone(date_default_timezone_get())
                    )
                );
                if ($isNew) {
                    $orm->persist($event);
                } else {
                    $orm->merge($event);
                }
            }
            $orm->flush();
        }

        // sync changes to google
        $sorted = $schedule->getEvents()->toArray();
        usort(
            $sorted,
            function (Event $e) {
                return !empty($e->getRecurrence()) && strpos($e->getRecurrence()[0], 'RECURRENCE-ID:') !== false;
            }
        );
        foreach ($sorted as $event) {
            /** @var Event $event */
            $config = self::getGoogleCalendarConfig($event);
            $newEvent = new \Google_Service_Calendar_Event($config);
            $parent = !empty($event->getRecurrence()) && strpos($event->getRecurrence()[0], 'RECURRENCE-ID:') !== false
                ? $schedule->getEvents()->filter(
                    function (Event $e) use ($event) {
                        return strpos($event->getRecurrence()[0], $e->getId() . '_') !== false;
                    }
                )->first()
                : $event;
            // create remote events if there is nothing like it already
            if (empty($parent->getRemoteId())) {
                $newEvent = $service->events->insert($calendarId, $newEvent);
                // store remoteId in all existing instances
                $parent->setRemoteUpdated(
                    date_timezone_set(
                        new \DateTime($newEvent->getUpdated()),
                        new \DateTimeZone(date_default_timezone_get())
                    )
                );
                $parent->setRemoteId($newEvent->getId());
                $orm->merge($parent);
                // update events that have changed
            } elseif (empty($event->getRemoteUpdated()) || !empty($event->getUpdated()) && $event->getUpdated(
                ) > $event->getRemoteUpdated()
            ) {
                $remoteId = $parent->getRemoteId();
                if ($parent != $event) {
                    $remoteId .= '_' . explode('_', $event->getRecurrence()[0])[1];
                }
                $newEvent = $service->events->update($calendarId, $remoteId, $newEvent);
                $event->setRemoteUpdated(
                    date_timezone_set(
                        new \DateTime($newEvent->getUpdated()),
                        new \DateTimeZone(date_default_timezone_get())
                    )
                );
                $orm->merge($event);
                // remove events that have a remote id but no longer exist in remote
            } elseif (!empty($event->getRemoteId()) && !in_array($event->getRemoteId(), $remoteIds)) {
                $orm->remove($event);
                if (!empty($event->getCourse())) {
                    $event->getCourse()->removeEvent($event);
                }
                $event->getSchedule()->removeEvent($event);
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
        switch(!empty($event->getCourse()) && $event->getCourse()->getType() == 'c' ? $event->getCourse()->getIndex() : null) {
            case 0:
                $color = 11;
                break;
            case 1:
                $color = 6;
                break;
            case 2:
                $color = 5;
                break;
            case 3:
                $color = 2;
                break;
            case 4:
                $color = 10;
                break;
            case 5:
                $color = 7;
                break;
            case 6:
                $color = 9;
                break;
            case 7:
                $color = 1;
                break;
            case 8:
                $color = 3;
                break;
            case 9:
                $color = 4;
                break;
            default:
                $color = 8;

        }
        $config = [
            'summary' => $event->getTitle(),
            'location' => $event->getLocation(),
            'description' => 'Log in to StudySauce to take notes.',
//                        'attendees' => [[
//                            'email' => $user->getEmail(),
//                            'displayName' => $name,
//                            'responseStatus' => 'accepted',
//                            'optional' => true
//                        ]],
            'colorId' => $color
        ];
        if ($event->getType() == 'h' || $event->getType() == 'd') {
            $config['start'] = [
                'date' => $event->getStart()->format('Y-m-d'),
                'timeZone' => date_default_timezone_get(),
            ];
            $config['end'] = [
                'date' => $event->getEnd()->format('Y-m-d'),
                'timeZone' => date_default_timezone_get(),
            ];
        } else {
            $config['start'] = [
                'dateTime' => $event->getStart()->format('c'),
                'timeZone' => date_default_timezone_get(),
            ];
            $config['end'] = [
                'dateTime' => $event->getEnd()->format('c'),
                'timeZone' => date_default_timezone_get(),
            ];
        }

        if (!empty($event->getRecurrence()) && strpos($event->getRecurrence()[0], 'RECURRENCE-ID:') === false) {
            $config['recurrence'] = $event->getRecurrence();
        } else {
            $config['recurrence'] = null;
        }

        if (!empty($event->getAlert()) || $event->getType() == 'd') {
            $config['reminders'] = [
                'useDefault' => false,
                'overrides' => []
            ];
            if ($event->getType() == 'd') {
                foreach ($event->getDeadline()->getReminder() as $r) {
                    $config['reminders']['overrides'][] = ['method' => 'popup', 'minutes' => $r / 60 - 540];
                }
            } else {
                $config['reminders']['overrides'][] = ['method' => 'popup', 'minutes' => $event->getAlert()];
            }
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
        /** @var $userManager UserManager */
        $userManager = $container->get('fos_user.user_manager');

        $calendars = self::getCalendars($user, $container, $client, $service);
        /** @var \Google_Client $client */
        /** @var \Google_Service_Calendar $service */
        $oldId = $user->getProperty('calendarId');
        $id = '';
        foreach ($calendars->getItems() as $cal) {
            /** @var \Google_Service_Calendar_CalendarListEntry $cal */
            if ($cal->getId() == $user->getProperty('calendarId') || $cal->getSummary() == 'StudySauce') {
                $id = $cal->getId();
                $user->setProperty('calendarId', $id);
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
            $userManager->updateUser($user);
            $id = $calendar->getId();
        }
        if ($user->getProperty('calendarId') != $oldId && !empty($schedule = $user->getSchedules()->first())) {
            /** @var Schedule $schedule */
            foreach ($schedule->getEvents()->toArray() as $e) {
                /** @var Event $e */
                if (!empty($e->getRemoteId())) {
                    $e->setRemoteId(null);
                    $e->setRemoteUpdated(null);
                    $orm->merge($e);
                }
            }
            $orm->flush();
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

        /** @var Schedule $schedule */
        $schedule = $guest->getSchedules()->first();
        $eventInfo = [];
        $courses = $schedule->getCourses()->filter(function (Course $c) {return !$c->getDeleted();})->toArray();
        foreach ($courses as $i => $c) {
            self::createCourseEvents($c, $orm);
            /** @var Course $c */
            if($c->getType() != 'c')
                continue;

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
                    'start' => date_timestamp_set(
                        clone $classT,
                        $classT->getTimestamp() - 86400 + 4 * 3600 + $i * 10 * 60
                    )->format('r'),
                    'end' => date_timestamp_set(
                        clone $classT,
                        $classT->getTimestamp() - 86400 + 5 * 3600 + $i * 10 * 60
                    )->format('r')
                ];
                $eventInfo[] = [
                    'courseId' => $c->getId(),
                    'type' => 'sr',
                    'start' => date_timestamp_set(
                        clone $classT,
                        $classT->getTimestamp() + 86400 - ($schedule->getClasses()->count() - $i) * 10 * 60
                    )->format('r'),
                    'end' => date_timestamp_set(
                        clone $classT,
                        $classT->getTimestamp() + 86400 + 3600 - ($schedule->getClasses()->count() - $i) * 10 * 60
                    )->format('r')
                ];
            }
        }
        self::createStudyEvents($schedule, $eventInfo, $orm);
        self::createAllDay($schedule, $guest->getDeadlines()->toArray(), $orm);
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
            !$schedule->getClasses()->exists(
                function ($_, Course $c) {
                    return $c->getEndTime() > new \DateTime();
                }
            )
        ) {
            $schedule = ScheduleController::getDemoSchedule($this->container);
            $isEmpty = true;
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
                self::createAllDay($schedule, $user->getDeadlines()->toArray(), $orm);
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

        $showConnected = false;
        if($step === false && empty($user->getProperty('showConnected')) && !empty($user->getGcalAccessToken())) {
            $showConnected = true;
            $user->setProperty('showConnected', true);
            /** @var $userManager UserManager */
            $userManager = $this->get('fos_user.user_manager');
            $userManager->updateUser($user);
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
                'services' => $services,
                'showConnected' => $showConnected
            ]
        );
    }

    /**
     * @param User $user
     * @return bool|string
     */
    public static function getPlanStep(User $user)
    {
        if($user->getProperty('plan_complete'))
            return false;
        /** @var $schedule \StudySauce\Bundle\Entity\Schedule */
        $schedule = $user->getSchedules()->first();
        if (empty($schedule)) {
            return 0;
        }

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
        if (empty($schedule) || empty($schedule->getClasses()->count()) || self::getPlanStep($user) !== false) {
            $events = new ArrayCollection();
            $isDemo = true;
        } else {
            $events = $schedule->getEvents();
        }

        return $this->render(
            'StudySauceBundle:Plan:widget.html.php',
            [
                'isDemo' => $isDemo,
                'events' => self::getJsonEvents($events),
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
            // skip deleted entry events
            if ($x->getDeleted() || (!empty($x->getCourse()) && $x->getCourse()->getDeleted())) {
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
            $instances = self::getInstances(
                date_timezone_set(clone $x->getStart(), new \DateTimeZone('Z')),
                $x->getRecurrence()
            );

            foreach ($instances as $start) {

                $id = $x->getId() . '_' . $start->format('Ymd') . 'T' . $start->format('Hise');
                $end = date_add(
                    clone $start,
                    new \DateInterval('PT' . ($x->getEnd()->getTimestamp() - $x->getStart()->getTimestamp()) . 'S')
                );

                // check database for event id
                /** @var Event $child */
                if (!empty($child = $events->filter(
                    function (Event $e) use ($id) {
                        return !empty($e->getRecurrence()) && strpos($e->getRecurrence()[0], $id) !== false;
                    }
                )->first())
                ) {
                    if ($child->getDeleted()) {
                        continue;
                    }
                    $start = $child->getStart();
                    $end = $child->getEnd();
                } else {
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
                    'location' => $child->getLocation(),
                    'completed' => $child->getCompleted()
                ];
                if (!empty($child->getCourse())) {
                    $newEvent['courseId'] = $child->getCourse()->getId();
                }
                $jsEvents[] = $newEvent;
            }
        }
        usort(
            $jsEvents,
            function ($a, $b) {
                return (new \DateTime($a['start']))->getTimestamp() - (new \DateTime($b['start']))->getTimestamp();
            }
        );

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
            $isNew = false;
            /** @var Event $event */
            $event = $schedule->getEvents()->filter(
                function (Event $e) use ($d) {
                    return !empty($e->getDeadline()) && $e->getDeadline()->getId() == $d->getId();
                }
            )->first();
            if (empty($event)) {
                $isNew = true;
                $event = new Event();
                $event->setDeadline($d);
                $event->setSchedule($schedule);
                $schedule->addEvent($event);
            } elseif ($d->getDeleted()) {
                // remove event
                $event->setDeleted(true);
            }
            $event->setCourse($d->getCourse());
            if (!empty($d->getCourse())) {
                $d->getCourse()->addEvent($event);
            }
            $event->setName(
                (empty($d->getCourse()) ? 'Non-academic: ' : ($d->getCourse()->getName() . ': ')) . $d->getAssignment()
            );
            $event->setType('d');

            /** @var Deadline $d */
            $event->setStart(date_time_set(clone $d->getDueDate(), 0, 0, 0));
            $event->setEnd(date_add(clone $event->getStart(), new \DateInterval('PT86399S')));
            $event->setUpdated(new \DateTime());
            if ($isNew) {
                $orm->persist($event);
            } else {
                $orm->merge($event);
            }
            $orm->flush();
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
                    $e->setAlert(null);
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
                    // remove existing study events when it is switched to none
                    if ($difficulty == 'none') {
                        foreach ($c->getEvents()->toArray() as $e) {
                            if ($e->getType() == 'p' || $e->getType() == 'sr') {
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
        $user->setProperty('plan_complete', true);
        /** @var $userManager UserManager */
        $userManager = $this->get('fos_user.user_manager');
        $userManager->updateUser($user);

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
            $rrules = implode("\r\n", $event->getRecurrence() ?: []);
            if (strpos($rrules, 'RECURRENCE-ID:') !== false) {
                $rrules = explode('_', $rrules)[1];
            }
            $id = $event->getId();
            $title = $event->getTitle();
            $start = $event->getStart()->format('Ymd') . 'T' . $event->getStart()->format('His');
            $end = $event->getEnd()->format('Ymd') . 'T' . $event->getEnd()->format('His');
            $alert = '';
            if (!empty($event->getAlert())) {
                $minutes = $event->getAlert() . 'M';
                $alert = <<<EOA
BEGIN:VALARM
TRIGGER:-PT$minutes
REPEAT:1
DURATION:PT$minutes
ACTION:DISPLAY
DESCRIPTION:Reminder
END:VALARM
EOA;
            }
            $created = date_timezone_set(clone $event->getCreated(), new \DateTimeZone('Z'));
            $created = $created->format('Ymd') . 'T' . $created->format('Hise');
            $modified = date_timezone_set(
                empty($event->getUpdated()) ? clone $event->getCreated() : clone $event->getUpdated(),
                new \DateTimeZone('Z')
            );
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
$alert
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
            foreach ($request->get('events') as $event) {
                if (!($event['type'] == 'p' || $event['type'] == 'sr' || $event['type'] == 'f')) {
                    $event['reoccurring'] = true;
                    self::updateEvent( $event, $schedule, $orm );
                }
            }
            self::createStudyEvents($schedule, $request->get('events'), $orm);
        }

        if(!empty($request->get('complete'))) {
            $user->setProperty('plan_complete', true);
        }
        else {
            $user->setProperty('plan_complete', false);
        }
        /** @var $userManager UserManager */
        $userManager = $this->get('fos_user.user_manager');
        $userManager->updateUser($user);

        return $this->forward('StudySauceBundle:Plan:index', ['_format' => 'tab']);
    }

    /**
     * @param Schedule $schedule
     * @param $eventInfo
     * @param EntityManager $orm
     */
    public static function createStudyEvents(Schedule $schedule, $eventInfo, EntityManager $orm)
    {
        $used = [];
        $courses = [];
        // create new events
        foreach ($eventInfo as $event) {
            $isNew = false;
            if (!($event['type'] == 'p' || $event['type'] == 'sr' || $event['type'] == 'f')) {
                if ($event['type'] == 'c') {
                    $event['reoccurring'] = true;
                    self::updateEvent($event, $schedule, $orm);
                }
                continue;
            }

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

                // find an existing event of the same type to update
                /** @var Event $newEvent */
                if (empty($newEvent = $course->getEvents()->filter(
                    function (Event $e) use ($event, $used) {
                        return $e->getType() == $event['type']
                        && !in_array($e->getId(), $used)
                        && (empty($e->getRecurrence())
                            || strpos('RECURRENCE-ID:', $e->getRecurrence()[0]) === false);
                    }
                )->first())
                ) {
                    $newEvent = new Event();
                    $isNew = true;
                    $newEvent->setSchedule($schedule);
                    $schedule->addEvent($newEvent);
                    $newEvent->setCourse($course);
                    $course->addEvent($newEvent);
                }
                $newEvent->setName($course->getName());
                $firstDay = $course->getStartTime()->getTimestamp();
                $lastDay = $course->getEndTime()->getTimestamp();
            } elseif ($event['type'] == 'f') {
                if (empty($newEvent = $schedule->getEvents()->filter(
                    function (Event $e) use ($event, $used) {
                        return $e->getType() == $event['type']
                        && !in_array($e->getId(), $used)
                        && (empty($e->getRecurrence())
                            || strpos('RECURRENCE-ID:', $e->getRecurrence()[0]) === false);
                    }
                )->first())
                ) {
                    $newEvent = new Event();
                    $isNew = true;
                    $newEvent->setSchedule($schedule);
                    $schedule->addEvent($newEvent);
                    $newEvent->setType($event['type']);
                }
                $newEvent->setName('Free study');
                $firstDay = min(
                    array_map(
                        function (Course $c) {
                            return $c->getStartTime()->getTimestamp();
                        },
                        $schedule->getClasses()->toArray()
                    )
                );
                $lastDay = max(
                    array_map(
                        function (Course $c) {
                            return $c->getEndTime()->getTimestamp();
                        },
                        $schedule->getClasses()->toArray()
                    )
                );
            } else {
                continue;
            }

            // delete child events to reset all times to default
            foreach ($schedule->getEvents()->toArray() as $e) {
                /** @var Event $e */
                if (!empty($e->getRecurrence()) && strpos(
                        $e->getRecurrence()[0],
                        'RECURRENCE-ID:' . $newEvent->getId() . '_'
                    ) !== false
                ) {
                    $orm->remove($e);
                    if (!empty($e->getCourse())) {
                        $e->getCourse()->removeEvent($e);
                    }
                    $schedule->removeEvent($e);
                }
            }
            // set alert and locations
            if (isset($event['title'])) {
                $newEvent->setName($event['title']);
            }
            if (isset($event['location'])) {
                $newEvent->setLocation($event['location']);
            }
            if (isset($event['alert'])) {
                $newEvent->setAlert($event['alert']);
            }
            $newEvent->setType($event['type']);
            $startTime = date_timezone_set(
                new \DateTime($event['start']),
                new \DateTimeZone(date_default_timezone_get())
            );
            $endTime = date_timezone_set(new \DateTime($event['end']), new \DateTimeZone(date_default_timezone_get()));
            $diff = array_values(self::$weekConversion)[$startTime->format('w')];
            $first = date_timestamp_set(
                new \DateTime(),
                strtotime('last Sunday ' . $startTime->format('H:i:s'), $firstDay)
            );
            $last = date_timestamp_set(
                new \DateTime(),
                strtotime('last Sunday ' . $startTime->format('H:i:s'), $lastDay)
            );
            $start = ($next = $first->getTimestamp() + $diff) < ($firstDay - 86400)
                ? date_timestamp_set(new \DateTime(), $next + 604800)
                : date_timestamp_set(new \DateTime(), $next);
            $newEvent->setStart($start);
            $length = $endTime->getTimestamp() - $startTime->getTimestamp();
            $newEvent->setEnd(date_add(clone $newEvent->getStart(), new \DateInterval('PT' . $length . 'S')));
            // TODO: add exceptions for holidays
            $end = ($next = $last->getTimestamp() + $diff + 604800) > ($lastDay + 86400)
                ? date_timestamp_set(new \DateTime(), $next - 604800)
                : date_timestamp_set(new \DateTime(), $next);
            $tomorrow = date_timestamp_set(new \DateTime(), strtotime('tomorrow', $end->getTimestamp()));
            $newEvent->setRecurrence(
                [
                    'RRULE:FREQ=WEEKLY' .
                    ';UNTIL=' . $tomorrow->format('Ymd') . 'T000000Z' .
                    ';BYDAY=' . strtoupper(substr($start->format('D'), 0, 2))
                ]
            );
            if ($isNew) {
                $orm->persist($newEvent);
            } else {
                $newEvent->setUpdated(new \DateTime());
                $orm->merge($newEvent);
            }
            $orm->flush();
            $used[] = $newEvent->getId();
            if (!empty($newEvent->getCourse())) {
                $courses[] = $newEvent->getCourse();
            }
        }

        // remove unused
        foreach ($schedule->getEvents()->toArray() as $e) {
            if (($e->getType() == 'sr' || $e->getType() == 'p' || $e->getType() == 'f')
                && !in_array($e->getId(), $used) && (in_array($e->getCourse(), $courses) || $e->getType() == 'f')
            ) {
                $orm->remove($e);
                if (!empty($e->getCourse())) {
                    $e->getCourse()->removeEvent($e);
                }
                $schedule->removeEvent($e);
            }
        }
        $orm->flush();
    }

    /**
     * @param Course $course
     * @param EntityManager $orm
     */
    public static function createCourseEvents(Course $course, EntityManager $orm)
    {
        $days = ['SU' => 'Su', 'MO' => 'M', 'TU' => 'Tu', 'WE' => 'W', 'TH' => 'Th', 'FR' => 'F', 'SA' => 'Sa'];

        $isNew = false;
        if (empty($event = $course->getEvents()->filter(
            function (Event $e) use ($course) {
                return $e->getType() == $course->getType();
            }
        )->first())
        ) {
            $isNew = true;
            $event = new Event();
            $event->setType($course->getType());
            $event->setCourse($course);
            $course->addEvent($event);
            $event->setSchedule($course->getSchedule());
            $course->getSchedule()->addEvent($event);
        }

        $event->setName($course->getName());
        $firstDay = min(
            array_map(
                function ($d) use ($course) {
                    return ($next = $course->getStartTime()->getTimestamp()
                        - array_values(self::$weekConversion)[$course->getStartTime()->format('w')]
                        + self::$weekConversion[$d]) < $course->getStartTime()->getTimestamp()
                        ? $next + 604800
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
        if ($isNew) {
            $orm->persist($event);
        } else {
            $event->setUpdated(new \DateTime());
            $orm->merge($event);
        }
        $orm->flush();
    }

    public static function getInstances(\DateTime $start, $rrules)
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
            $local = date_timezone_set(clone $start, new \DateTimeZone(date_default_timezone_get()));
            if (isset($until) && isset($dotw) && isset($freq)) {
                for ($t = $start->getTimestamp(); $t <= $until->getTimestamp(); $t += $freq) {
                    $sunday = $t - array_values(self::$weekConversion)[$local->format('w')];
                    foreach ($dotw as $d) {
                        $newDate = date_timestamp_set(
                            new \DateTime('now', $start->getTimezone()),
                            $sunday + array_values(self::$weekConversion)[$days[$d]]
                        );
                        if ($newDate >= $start && $newDate <= $until && !in_array($newDate, $instances)) {
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

        self::updateEvent(
            [
                'start' => $request->get('start'),
                'end' => $request->get('end'),
                'eventId' => $request->get('eventId'),
                'reoccurring' => $request->get('reoccurring'),
                'location' => $request->get('location'),
                'alert' => $request->get('alert'),
                'title' => $request->get('title'),
            ],
            $schedule,
            $orm
        );

        return $this->forward('StudySauceBundle:Plan:index', ['_format' => 'tab']);
    }

    public static function updateEvent($eventInfo, Schedule $schedule, EntityManager $orm)
    {
        /** @var Event $parent */
        if (!empty($schedule)) {
            $parent = $schedule->getEvents()->filter(
                function (Event $e) use ($eventInfo) {
                    return $e->getId() == explode('_', $eventInfo['eventId'])[0];
                }
            )->first();
        }

        if (empty($parent)) {
            return;
        }

        $isNew = false;
        $original = new \DateTime(explode('_', $eventInfo['eventId'])[1]);

        if ($eventInfo['reoccurring'] !== 'false') {
            if (!empty($eventInfo['start']) && !empty($eventInfo['end'])) {
                $newStart = date_timezone_set(
                    new \DateTime($eventInfo['start']),
                    new \DateTimeZone(date_default_timezone_get())
                );
                $newEnd = date_timezone_set(
                    new \DateTime($eventInfo['end']),
                    new \DateTimeZone(date_default_timezone_get())
                );

                // get original instance day and change days
                if (!empty($newRecurrence = $parent->getRecurrence())) {
                    // change original day to new day of the week,
                    // unless current event was moved before make that the new time
                    preg_match('/BYDAY=[a-z\,]*/i', $parent->getRecurrence()[0], $matches);
                    $newDays = str_replace(
                        strtoupper(
                            substr(
                                date_timezone_set(
                                    clone $original,
                                    new \DateTimeZone(date_default_timezone_get())
                                )->format('D'),
                                0,
                                2
                            )
                        ),
                        strtoupper(substr($newStart->format('D'), 0, 2)),
                        $matches[0]
                    );
                    // TODO: exclude days
                    $newRecurrence[0] = preg_replace('/BYDAY=[a-z\,]*/i', $newDays, $parent->getRecurrence()[0]);
                    $parent->setRecurrence($newRecurrence);
                }

                // select the earliest date, either the original day with new time or the new day and time
                if ($newStart < $parent->getStart()) {
                    $parent->setStart($newStart);
                    $parent->setEnd($newEnd);
                } else {
                    // only change times
                    $diff = new \DateInterval('PT' . abs($newStart->getTimestamp() - $original->getTimestamp()) . 'S');
                    if ($newStart->getTimestamp() - $original->getTimestamp() < 0) {
                        $diff->invert = true;
                    }
                    $parent->setStart(date_add(clone $parent->getStart(), $diff));
                    $parent->setEnd(
                        date_add(
                            clone $parent->getStart(),
                            new \DateInterval('PT' . ($newEnd->getTimestamp() - $newStart->getTimestamp()) . 'S')
                        )
                    );
                }
                // remove out of series children
                // TODO: does this only work if we are changing days?
                $children = $schedule->getEvents()->filter(
                    function (Event $e) use ($parent) {
                        !empty($e->getRecurrence()) && strpos(
                            $e->getRecurrence()[0],
                            'RECURRENCE-ID:' . $parent->getId() . '_'
                        ) !== false;
                    }
                )->toArray();
                foreach ($children as $e) {
                    /** @var Event $e */
                    $orm->remove($e);
                    $e->getSchedule()->removeEvent($e);
                }
            }
            $event = $parent;
        } else {
            // add event out of series
            /** @var Event $event */
            $event = $schedule->getEvents()->filter(
                function (Event $e) use ($eventInfo) {
                    return !empty($e->getRecurrence()) && strpos(
                        $e->getRecurrence()[0],
                        $eventInfo['eventId']
                    ) !== false;
                }
            )->first();
            if (empty($event)) {
                $isNew = true;
                $event = new Event();
                $event->setCourse($parent->getCourse());
                if (!empty($parent->getCourse())) {
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
            if (!empty($eventInfo['start']) && !empty($eventInfo['end'])) {
                $newStart = date_timezone_set(
                    new \DateTime($eventInfo['start']),
                    new \DateTimeZone(date_default_timezone_get())
                );
                $newEnd = date_timezone_set(
                    new \DateTime($eventInfo['end']),
                    new \DateTimeZone(date_default_timezone_get())
                );

                $event->setStart($newStart);
                $event->setEnd($newEnd);
            } else {
                $event->setStart(date_timezone_set(clone $original, new \DateTimeZone(date_default_timezone_get())));
                $event->setEnd(
                    date_add(
                        clone $event->getStart(),
                        new \DateInterval(
                            'PT' . ($parent->getEnd()->getTimestamp() - $parent->getStart()->getTimestamp()) . 'S'
                        )
                    )
                );
            }
        }
        if ($eventInfo['location'] !== null) {
            $event->setLocation($eventInfo['location']);
        }
        if ($eventInfo['alert'] !== null) {
            $event->setAlert($eventInfo['alert']);
        }
        if ($eventInfo['title'] !== null) {
            $event->setName($eventInfo['title']);
        }

        $event->setUpdated(new \DateTime());

        if ($isNew) {
            $orm->persist($event);
        } else {
            $orm->merge($event);
        }
        $orm->flush();
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
                return $e->getId() == explode('_', $request->get('eventId'))[0];
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

    public function pdfAction(Request $request, User $user = null)
    {
        if ($request->server->get('REMOTE_ADDR') == '127.0.0.1' && !empty($user)) {
            if(empty($user))
                $user = $this->getUser();
            $schedule = $user->getSchedules()->first();
            return $this->render('StudySauceBundle:Plan:pdf.html.php', ['schedule' => $schedule]);
        } else {
            /** @var User $user */
            if(empty($user))
                $user = $this->getUser();

            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="Study Sauce Plan.pdf"');

            $command = 'wkhtmltopdf -O landscape https://' . $_SERVER['HTTP_HOST'] . '/plan/pdf/' . $user->getId() . ' - ';
            passthru($command);
            exit();
        }
    }

}



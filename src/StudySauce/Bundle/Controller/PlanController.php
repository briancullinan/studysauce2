<?php

namespace StudySauce\Bundle\Controller;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use StudySauce\Bundle\Entity\ActiveStrategy;
use StudySauce\Bundle\Entity\Course;
use StudySauce\Bundle\Entity\Deadline;
use StudySauce\Bundle\Entity\Event;
use StudySauce\Bundle\Entity\File;
use StudySauce\Bundle\Entity\OtherStrategy;
use StudySauce\Bundle\Entity\PreworkStrategy;
use StudySauce\Bundle\Entity\Schedule;
use StudySauce\Bundle\Entity\SpacedStrategy;
use StudySauce\Bundle\Entity\TeachStrategy;
use StudySauce\Bundle\Entity\User;
use StudySauce\Bundle\Entity\Week;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
    private static $holidays;
    private static $unresolved;

    /**
     *
     */
    public function __construct()
    {
        self::$holidays = [
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
    }

    /**
     * @param User $_user
     * @param null $_week
     * @param array $template
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(User $_user = null, $_week = null, $template = ['Plan', 'tab'])
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        /** @var $_user \StudySauce\Bundle\Entity\User */
        if(empty($_user))
            $_user = $this->getUser();

        /** @var $schedule \StudySauce\Bundle\Entity\Schedule */
        $schedule = $_user->getSchedules()->first();

        // get demo schedule instead
        $showPlanIntro = false; // TODO: false in production
        if (empty($schedule) || empty($schedule->getCourses()) || !$_user->hasRole('ROLE_PAID')) {
            /** @var $userManager UserManager */
            $userManager = $this->get('fos_user.user_manager');
            $schedule = ScheduleController::getDemoSchedule($userManager, $orm);
        }
        // show intro for paid users
        elseif(empty($_user->getProperty('seen_plan_intro'))) {
            $showPlanIntro = true;
            /** @var $userManager UserManager */
            $userManager = $this->get('fos_user.user_manager');
            $_user->setProperty('seen_plan_intro', true);
            $userManager->updateUser($_user);
        }

        if($_week !== 0 && empty($_week)) {
            $_week = strtotime('last Sunday');
            if ($_week + 604800 == strtotime('today')) {
                $_week += 604800;
            }
        }
        elseif(is_numeric($_week)) {
            $_week = (new \DateTime('January 1'))->getTimestamp() + intval($_week) * 604800;
        }
        elseif(is_string($_week)) {
            $_week = strtotime('last Sunday', strtotime($_week)) + 604800;
        }

        // get events for current week
        $emails = new EmailsController();
        $emails->setContainer($this->container);
        $events = self::rebuildSchedule($schedule, $schedule->getCourses(), $_user->getDeadlines(), $_week, $orm, $emails);
        $courses = $schedule->getCourses()->filter(function (Course $c) {return $c->getType() == 'c';})->toArray();
        return $this->render('StudySauceBundle:' . $template[0] . ':' . $template[1] . '.html.php', [
                'events' => $events,
                'courses' => array_values($courses),
                'jsonEvents' =>  self::getJsonEvents($events, array_values($courses)),
                'user' => $_user,
                'strategies' => self::getStrategies($schedule),
                'week' => $_week,
                'showPlanIntro' => $showPlanIntro
            ]);
    }

    /**
     * @param $_user
     * @param $_week
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function partnerAction(User $_user, $_week = null)
    {
        return $this->indexAction($_user, $_week, ['Partner', 'plan']);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function widgetAction()
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        /** @var $user \StudySauce\Bundle\Entity\User */
        $user = $this->getUser();
        /** @var $schedule \StudySauce\Bundle\Entity\Schedule */
        $schedule = $user->getSchedules()->first();

        // get events for current week
        if (empty($schedule) || empty($schedule->getCourses())) {
            /** @var $userManager UserManager */
            $userManager = $this->get('fos_user.user_manager');

            $schedule = ScheduleController::getDemoSchedule($userManager, $orm);
        }

        $week = strtotime('last Sunday');
        if ($week + 604800 == strtotime('today')) {
            $week += 604800;
        }

        // TODO: get demo Deadlines?
        $emails = new EmailsController();
        $emails->setContainer($this->container);

        $events = self::rebuildSchedule($schedule, $schedule->getCourses(), $user->getDeadlines(), $week, $orm, $emails);

        $courses = $schedule->getCourses()->filter(function (Course $c) {return $c->getType() == 'c';})->toArray();
        return $this->render(
            'StudySauceBundle:Plan:widget.html.php',
            [
                'events' => $events,
                'user' => $user,
                'classes' => array_map(function (Course $c) {return $c->getName();}, array_values($courses))
            ]
        );
    }

    /**
     * @param $events
     * @param $courses
     * @return array
     */
    public static function getJsonEvents($events, $courses)
    {
        $classes = array_map(function (Course $c) {return $c->getId();}, $courses);
        $jsEvents = [];
        foreach ($events as $i => $x) {
            /** @var Event $x */
            // skip data entry events
            if ($x->getDeleted()) {
                continue;
            }

            if (!empty($x->getCourse()))
                $classI = array_search($x->getCourse()->getId(), $classes);
            else
                $classI = '';

            $label = '';
            $skip = false;
            switch($x->getType()) {
                case 'c':
                    $label = 'C';
                    break;
                case 'sr':
                case 'f':
                    $label = 'S';
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
                'className' => 'event-type-' . $x->getType() . ' ' . ($classI !== '' ? ('class' . $classI) : ''),
                // all day for deadlines, reminders, and holidays
                'allDay' => $x->getType() == 'd' || $x->getType() == 'h' ||
                    $x->getType() == 'r',
                'editable' => ($x->getType() == 'sr' || $x->getType() == 'f' || $x->getType() == 'p'),
                'dates' => isset($dates) ? $dates : null
            ];
        }
        return $jsEvents;
    }

    /**
     * @param Schedule $schedule
     * @param Collection $courses
     * @param Collection $deadlines
     * @param $week
     * @param EntityManager $orm
     * @param EmailsController $emails
     * @return array
     */
    public static function rebuildSchedule(Schedule $schedule, Collection $courses, Collection $deadlines, $week, EntityManager $orm, EmailsController $emails)
    {
        self::$unresolved = [];
        $events = [];

        $buckets = new \stdClass();
        $buckets->buckets = [];
        $buckets->studyTotals = 0;
        $buckets->classTotals = 0;
        $buckets->timeSlots = [
            16 => ($schedule->getSharp4pm9pm() ?: 2) / 5.0 * 4,
            21 => ($schedule->getSharp11am4pm() ?: 2) / 5.0 * 4,
            11 => ($schedule->getSharp9pm2am() ?: 2) / 5.0 * 4,
            6 => ($schedule->getSharp6am11am() ?: 2) / 5.0 * 4
        ];
        arsort($buckets->timeSlots);

        $w = new \DateTime();
        $w->setTimestamp($week);

        // TODO: get holidays and deadlines

        // add reoccurring events
        $classes = $courses->filter(function (Course $c) {return $c->getType() == 'c';})->toArray();
        $reoccurring = [];
        foreach ($classes as $i => $course) {
            /** @var Course $course */
            $reoccurring = array_merge($reoccurring, self::getReoccurring($course, $week));
        }
        $others = $courses->filter(function (Course $c) {return $c->getType() == 'o';})->toArray();
        foreach ($others as $i => $other) {
            /** @var Course $other */
            $reoccurring = array_merge($reoccurring, self::getReoccurring($other, $week));
        }

        // bucket all classes
        self::bucketEvents($reoccurring, $buckets);

        // add meals just for proper spacing
        $meals = self::getMeals($week, $buckets);

        // order classes by most days to least days
        $labsLast = [];
        foreach ($classes as $i => $course) {
            /** @var Course $course */
            $difficulty = 4;
            if ($course->getStudyDifficulty() == 'tough') {
                $difficulty = 6;
            }
            if ($course->getStudyDifficulty() == 'easy') {
                $difficulty = 2;
            }

            $priority = (count($labsLast) / 2.0) - count($course->getDotw()) + $difficulty;
            $labsLast[] = ['sr', $course, $priority];
        }
        foreach ($classes as $i => $course) {
            /** @var Course $course */
            $difficulty = 4;
            if ($course->getStudyDifficulty() == 'tough') {
                $difficulty = 6;
            }
            if ($course->getStudyDifficulty() == 'easy') {
                $difficulty = 2;
            }

            $priority = (count($labsLast) / 2.0) - count($course->getDotw()) + $difficulty;
            $labsLast[] = ['p', $course, $priority];
        }
        usort($labsLast, function ($a, $b) {return $a[2] - $b[2];});

        $studySessions = [];
        foreach ($labsLast as $i => $count) {
            // set up pre-work
            if ($count[0] == 'p') {
                /** @var Course $course */
                $course = $count[1];
                $studySessions = array_merge($studySessions, self::getPrework($course, $week));
            } // set up sr-sessions
            elseif ($count[0] == 'sr') {
                $course = $count[1];
                $studySessions = array_merge($studySessions, self::getStudy($course, $week));
            }
        }

        // get totals for study events in current position
        self::bucketEvents($studySessions, $buckets);

        // get free events
        $freeStudy = self::getFree($buckets, $week, $schedule);

        // merge events
        $events = array_merge($events, $reoccurring);
        $events = array_merge($events, $meals);
        $events = array_merge($events, $studySessions);
        $events = array_merge($events, $freeStudy);

        // hash list of events in their default order and schedule settings
        $weekHash = md5(implode('', array_map(function (array $e) {
                        /** @var Course $c */
                        $c = isset($e['course']) ? $e['course'] : null;
                        /** @var Deadline $d */
                        $d = isset($e['deadline']) ? $e['deadline'] : null;
                        return $e['name'] . $e['type'] . (!empty($c) ? $c->getId() : '') . (!empty($d) ? $d->getId() : '');
                    }, $events)) .
            // add configuration used to build plan so it updates when that changes
            $schedule->getGrades() . $schedule->getWeekends() . $schedule->getSharp11am4pm() .
            $schedule->getSharp4pm9pm() . $schedule->getSharp9pm2am() . $schedule->getSharp6am11am() .
            implode('', $courses->map(function (Course $c) {return $c->getStudyDifficulty() . $c->getStudyType();})->toArray()));

        // check to see if there is a matching list of events for another week
        if (($eventWeek = $schedule->getWeeks()->filter(
                function (Week $w) use ($weekHash, $events) {
                    return $w->getHash() == $weekHash && $w->getEvents()->count() == count($events);
                }
            )->first()) != null
        ) {
            /** @var Week $eventWeek */
            // if the event hash matches use final arrangement from week cache
            $matchingEvents = $eventWeek->getEvents()->toArray();
            $diff = (intval($w->format('W')) - $eventWeek->getWeek()) * 86400 * 7;
            $interval = new \DateInterval('PT' . abs($diff) . 'S');
            if($diff < 0)
                $interval->invert = 1;
            $events = array_map(function (Event $e) use ($interval) {
                return [
                    'deadline' => $e->getDeadline(),
                    'course' => $e->getCourse(),
                    'type' => $e->getType(),
                    'name' => $e->getName(),
                    // shift times to new week
                    'start' => date_add(clone $e->getStart(), $interval),
                    'end' => date_add(clone $e->getEnd(), $interval)
                ];
            }, $matchingEvents);
        }
        else {
            // TODO: reset buckets
            $buckets->buckets = [];
            $buckets->studyTotals = 0;
            $buckets->classTotals = 0;
            self::bucketEvents($reoccurring, $buckets);
            self::sortEvents($reoccurring);
            self::sortEvents($meals);
            self::sortEvents($studySessions);
            // remove overlaps on all events
            $workingEvents = array_values($reoccurring);
            foreach($meals as $i => $event)
            {
                list($notBefore, $notAfter) = self::getBoundaries(
                    $event,
                    $workingEvents,
                    $week,
                    $schedule);
                $currentEvents = self::getWorkingEvents($workingEvents, $i, $notBefore, $notAfter);
                self::removeOverlaps($currentEvents, $event, $buckets, $notBefore, $notAfter);
                $workingEvents[] = $event;
            }
            foreach($studySessions as $i => $event)
            {
                list($notBefore, $notAfter) = self::getBoundaries(
                    $event,
                    $workingEvents,
                    $week,
                    $schedule);
                $currentEvents = self::getWorkingEvents($workingEvents, $i, $notBefore, $notAfter);
                self::removeOverlaps($currentEvents, $event, $buckets, $notBefore, $notAfter);
                $workingEvents[] = $event;
                self::bucketEvents([$event], $buckets);
            }
            foreach($freeStudy as $i => $event)
            {
                list($notBefore, $notAfter) = self::getBoundaries(
                    $event,
                    $workingEvents,
                    $week,
                    $schedule);
                $currentEvents = self::getWorkingEvents($workingEvents, $i, $notBefore, $notAfter);
                self::removeOverlaps($currentEvents, $event, $buckets, $notBefore, $notAfter);
                $workingEvents[] = $event;
                self::bucketEvents([$event], $buckets);
            }
            $events = $workingEvents;
        }

        $events = array_merge($events, self::getAllDay($deadlines, $week));

        // get the current week
        /** @var Week $currentWeek */
        $currentWeek = $schedule->getWeeks()->filter(
            function (Week $week) use ($weekHash, $w) {
                return $week->getWeek() == intval($w->format('W')) &&
                $week->getYear() == $w->format('Y');
            })->first();
        if($currentWeek == null) {
            // create a new week entity to cache the events
            $currentWeek = new Week();
            $currentWeek->setStart($w);
            $currentWeek->setHash('');
            $currentWeek->setWeek($w->format('W'));
            $currentWeek->setSchedule($schedule);
            $currentWeek->setYear($w->format('Y'));
            $schedule->addWeek($currentWeek);
            $orm->persist($currentWeek);
            $orm->flush();
        }

        // if week has changed, update the events
        if($currentWeek->getHash() != $weekHash || $currentWeek->getEvents()->count() != count($events)) {
            $currentWeek->setHash($weekHash);
            $orm->merge($currentWeek);
            $orm->flush();

            self::sortEvents($events);
            self::mergeSaved($schedule, $currentWeek, $events, $orm);
        }
        else {
            // no change
            $events = $currentWeek->getEvents();
        }

        // send unresolved email to administrator
        if(!empty(self::$unresolved))
        {
            $unresolvedEmail = ['student' => $schedule->getUser()->getEmail()];
            foreach(self::$unresolved as $i => $e)
            {
                /** @var Event $e */
                $unresolvedEmail[$e->getStart()->format('y-m-d H:i:s') . ' ' . $e->getType()] = $e->getName();
            }

            $emails->administratorAction(null, $unresolvedEmail);
        }

        return $events;
    }

    /**
     * @param $deadlines
     * @param $week
     * @return array
     */
    private static function getAllDay($deadlines, $week)
    {
        $events = [];

        // add deadlines and holidays
        foreach ($deadlines as $did => $d) {
            /** @var Deadline $d */
            $classT = clone $d->getDueDate();
            $classT->setTime(0, 0, 0);

            if($classT->getTimestamp() > $week && $classT->getTimestamp() < $week + 604800) {
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

            if(empty($d->getReminder()))
                continue;

            foreach ($d->getReminder() as $i => $r) {
                $classR = clone $classT;
                $classR->sub(new \DateInterval('PT' . $r . 'S'));

                if($classT->getTimestamp() > $week && $classT->getTimestamp() < $week + 604800) {
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
        }

        foreach (self::$holidays as $k => $h) {
            $classT = new \DateTime($k);

            if($classT->getTimestamp() > $week && $classT->getTimestamp() < $week + 604800) {
                $holiday = [
                    'name' => $h,
                    'type' => 'h',
                    'start' => $classT,
                    'end' => date_add(clone $classT, new \DateInterval('PT86399S'))
                ];
                $events[] = $holiday;
            }
        }

        return $events;
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
     * @param array $event
     * @return array
     */
    private static function getMealBoundaries(array $event)
    {
        /** @var \DateTime $s */
        $s = $event['start'];
        switch($event['name'])
        {
            case 'Breakfast':
                return [$s->getTimestamp() - 3600 * 3, $s->getTimestamp() + 3600 * 2];
                break;
            case 'Lunch':
                return [$s->getTimestamp() - 3600 * 2, $s->getTimestamp() + 3600 * 3];
                break;
            case 'Dinner':
                return [$s->getTimestamp() - 3600 * 2.5, $s->getTimestamp() + 3600 * 5.5];
                break;
        }
        throw new Exception('Un-recognized meal');
    }

    /**
     * @param array $event
     * @param $workingEvents
     * @param $week
     * @param Schedule $schedule
     * @return array
     */
    private static function getBoundaries(array $event, $workingEvents, $week, Schedule $schedule)
    {
        switch($event['type'])
        {
            case 'p':
                return self::getPreworkBoundaries($event, $workingEvents);
                break;
            case 'm':
                return self::getMealBoundaries($event);
                break;
            case 'sr':
                return self::getStudyBoundaries($event, $workingEvents);
                break;
            case 'z':
                return self::getSleepBoundaries($event);
                break;
            case 'f':
                return self::getFreeBoundaries($week, $schedule);
                break;
        }
        throw new Exception('Unrecognized event type');
    }

    /**
     * @param $week
     * @param Schedule $schedule
     * @return array
     */
    private static function getFreeBoundaries($week, Schedule $schedule)
    {
        // don't place free study on weekends
        $weekends = ($schedule->getWeekends() == 'hit-hard');
        // TODO: unless it is the only option?
        if(!$weekends)
        {
            $notBefore = new \DateTime();
            $notBefore->setTimestamp($week);
            $notAfter = clone $notBefore;
            $notAfter->add(new \DateInterval('P7D'));
        }
        else
        {
            $notBefore = new \DateTime();
            $notBefore->setTimestamp($week);
            $notAfter = clone $notBefore;
            $notAfter->add(new \DateInterval('P7D'));
        }
        return [$notBefore->getTimestamp(), $notAfter->getTimestamp()];
    }

    /**
     * @param array $event
     * @param $workingEvents
     * @return array
     */
    private static function getPreworkBoundaries(array $event, $workingEvents)
    {
        /** @var \DateTime $e */
        $e = $event['end'];
        /** @var \DateTime $s */
        $s = $event['start'];
        // the boundary for prework is the previous and next class
        // count forwards to matching type
        for($j = 0; $j < count($workingEvents); $j++)
        {
            $w = $workingEvents[$j];
            /** @var \DateTime $eW */
            $eW = $event['end'];
            /** @var \DateTime $sW */
            $sW = $event['start'];
            // stop after we pass current events time
            if($e->getTimestamp() - $sW->getTimestamp() < 0)
                break;
            if($w['course'] == $event['course'])
                return [$eW->getTimestamp(), $s->getTimestamp() + 86400];
        }
        return [$s->getTimestamp() - 86400, $s->getTimestamp() + 86400];
    }

    /**
     * @param array $event
     * @param $workingEvents
     * @return array
     */
    private static function getStudyBoundaries(array $event, $workingEvents)
    {
        /** @var \DateTime $e */
        $e = $event['end'];
        /** @var \DateTime $s */
        $s = $event['start'];
        // the boundary for study is the previous and next class
        // count backwards to matching type
        for($j = count($workingEvents) - 1; $j >= 0 ; $j--)
        {
            $w = $workingEvents[$j];
            /** @var \DateTime $eW */
            $eW = $event['end'];
            /** @var \DateTime $sW */
            $sW = $event['start'];
            // stop after we pass the current events time
            if($sW->getTimestamp() - $e->getTimestamp() < 0)
                break;
            if($w['course'] == $event['course'])
                return [$s->getTimestamp(), $eW->getTimestamp()];
        }
        return [$s->getTimestamp(), $s->getTimestamp() + 86400 * 2];
    }

    /**
     * @param array $event
     * @return array
     */
    private static function getSleepBoundaries(array $event)
    {
        /** @var \DateTime $e */
        $e = $event['end'];
        /** @var \DateTime $s */
        $s = $event['start'];
        $length = $e->getTimestamp() - $s->getTimestamp();
        $mealTimestamp = $s->getTimestamp();
        $notBefore = $mealTimestamp - 3600 * ($length / 3600 + 2);
        $notAfter = $mealTimestamp + 3600 * (2 + ($length / 3600 - 6)); // allow 8 AM start time on weekends
        return [$notBefore, $notAfter];
    }

    /**
     * @param $events
     * @param $index
     * @param \DateTime $notBefore
     * @param \DateTime $notAfter
     * @return array
     */
    private static function getWorkingEvents($events, $index, $notBefore, $notAfter)
    {
        // get a list of events within a 24 hour time range to work with, we should never move more than that
        $beforeDistances = [];
        $afterDistances = [];
        foreach ($events as $i => $event)
        {
            /** @var array $event */
            if($i == $index ||
                // ignore all day events
                $event['type'] == 'd' || $event['type'] == 'h' ||
                $event['type'] == 'r')
                continue;

            /** @var \DateTime $s */
            $s = $event['start'];
            /** @var \DateTime $e */
            $e = $event['end'];
            $startDistance = $s->getTimestamp() - $notBefore;
            $endDistance = $notAfter - $e->getTimestamp();
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

        return array_values($workingEvents);
    }

    /**
     * @param $workingEvents
     * @param array $event
     * @param $buckets
     * @param $notBefore
     * @param $notAfter
     */
    private static function removeOverlaps($workingEvents, array &$event, $buckets, $notBefore, $notAfter)
    {
        // find gaps
        if(empty($workingEvents)) {
            return;
        }
        /** @var \DateTime $endFirst */
        $endFirst = $event['end'];
        /** @var \DateTime $startFirst */
        $startFirst = $event['start'];
        $length = $endFirst->getTimestamp() - $startFirst->getTimestamp();
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

        // get the closest opening to the original time
        // TODO: store list of gaps by adjusting the ends when the new event is added
        $closest = [];
        foreach ($gaps as $c => $d) {
            /** @var \DateTime $d */
            // reset the time so we pick out the closest day,
            //    this levels the playing field for all daily preferred times
            /** @var \DateTime $startDay */
            $startDay = clone $event['start'];
            if (intval($startDay->format('H')) < 5) {
                $startDay->sub(new \DateInterval('P1D'));
            }
            $startDay->setTime(0, 0, 0);

            // compare start time of gap to preferred day
            $gapStart = clone $d;
            if (intval($gapStart->format('H')) < 5) {
                $gapStart->sub(new \DateInterval('P1D'));
            }
            $gapStart->setTime(0, 0, 0);
            $closest[$c] = abs($gapStart->getTimestamp() - $startDay->getTimestamp());

            // check if ending of gap is closer to preferred day
            /** @var array $e */
            $e = $workingEvents[intval(substr($c, 1))];
            $gapEnd = clone $e['start'];
            // if after midnight subtract 1 day
            if (intval($gapEnd->format('H')) < 5) {
                $gapEnd->sub(new \DateInterval('P1D'));
            }
            // reset the time so we pick out the closest day, this levels the playing field for all daily preferred times
            $gapEnd->setTime(0,0,0);
            $gapEnd->setTimestamp(min($gapEnd->getTimestamp(), $notAfter));
            $end = abs($gapEnd->getTimestamp() - $startDay->getTimestamp());
            if ($end < $closest[$c]) {
                $closest[$c] = $end;
            }
        }


        // find the closest time to the preferred time
        $distance = [];
        $ends = [];
        foreach ($closest as $c => $d) {
            // find the best bucket to put this event into
            /** @var \DateTime $start */
            $start = clone $gaps[$c];
            /** @var \DateTime $bucket */
            $bucket = clone $start;
            $bucket->setTime(0, 0, 0);
            $b = $bucket->format('Y/m/d');
            // if it starts before 6 AM put it in the previous nights bucket
            if (intval($start->format('H')) < 6) {
                $bucket->sub(new \DateInterval('P1D'));
                $b = $bucket->format('Y/m/d');
            }

            // if we are dealing with a mean the preferred time is when the meal is originally set
            // TODO: move this in to add_meals somehow?
            if ($event['type'] == 'p') {
                $preferred = clone $start;
                $preferred->setTime(6, 0, 0);
            } elseif ($event['type'] == 'm' || $event['type'] == 'z' || (
                    isset($buckets->buckets[$b]) && array_sum(array_values($buckets->buckets[$b])) >= array_sum(
                        array_values($buckets->timeSlots)))
            ) {
                $preferred = clone $event['start'];
            } else {
                $preferred = clone self::getPreferredTime($buckets, $start, $start->getTimestamp() - 86400, $start->getTimestamp() + 86400);
            }

            $distance[$c] = $closest[$c] + (abs($start->getTimestamp() - $preferred->getTimestamp()));
            /** @var array $e */
            $e = $workingEvents[intval(substr($c, 1))];
            $gapEnd = clone $e['start'];
            $gapEnd->setTimestamp(min($gapEnd->getTimestamp(), $notAfter));
            $end = $closest[$c] + (abs($gapEnd->getTimestamp() - $length - $preferred->getTimestamp()));
            if ($end < $distance[$c]) {
                $distance[$c] = $end;
                $ends[] = $c;
            }
        }

        array_multisort($closest, SORT_NUMERIC, SORT_ASC, $distance, SORT_NUMERIC, SORT_ASC);

        reset($distance);
        $top = key($distance);
        if ($top > -1) {
            if (in_array($top, $ends)) {
                /** @var array $start */
                $start = $workingEvents[intval(substr($top, 1))];
                $gapEnd = clone $start['start'];
                $gapEnd->setTimestamp(min($gapEnd->getTimestamp(), $notAfter));
                $gapEnd->sub(new \DateInterval('PT' . ($length + self::OVERLAP_INCREMENT) . 'S'));
                $event['start'] = $gapEnd;
                $event['end'] = date_add(clone $gapEnd, new \DateInterval('PT' . $length . 'S'));
            } else {
                /** @var \DateTime $d */
                $d = clone $gaps[$top];
                $d->add(new \DateInterval('PT' . self::OVERLAP_INCREMENT . 'S'));
                $event['start'] = $d;
                $event['end'] = date_add(clone $d, new \DateInterval('PT' . $length . 'S'));
            }
        }

        // send assertion email when we fail to find a spot to place the event
        if (!empty($workingEvents) && $event['type'] != 'm' && $event['type'] != 'z' && $top == -1) {
            self::$unresolved[] = $event;
        }
    }

    /**
     * @param Schedule $schedule
     * @param Week $week
     * @param array $events
     * @param EntityManager $orm
     * @return array
     */
    private static function mergeSaved(Schedule $schedule, Week $week, array &$events, EntityManager $orm)
    {
        // find matching saved events
        $saved = $week->getEvents();
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
                        $e->getCourse()
                        == (!empty($event['course']) ? $event['course'] : null) && $e->getDeadline()
                        == (!empty($event['deadline']) ? $event['deadline'] : null);
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
            // TODO: check if in strategies
            /** @var Event $save */
            if(!empty($save->getActive()) || !empty($save->getCompleted()) || !empty($save->getOther()) ||
                !empty($save->getPrework()) || !empty($save->getTeach()) || !empty($save->getSpaced()))
                continue;

            $schedule->removeEvent($save);
            $week->removeEvent($save);
            $orm->remove($save);
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
                $newEvent->setWeek($week);
                $schedule->addEvent($newEvent);
                $week->addEvent($newEvent);
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
     * @param $buckets
     * @param $week
     * @param Schedule $schedule
     * @return array
     */
    private static function getFree($buckets, $week, Schedule $schedule)
    {
        $events = [];
        // no free study before first or after last classes
        if($week < min($schedule->getCourses()->map(function (Course $c) {return $c->getStartTime()->getTimestamp();})->toArray()) ||
            $week > max($schedule->getCourses()->map(function (Course $c) {return $c->getEndTime()->getTimestamp();})->toArray()))
            return $events;
        // add free study to every
        $totalLength = $buckets->classTotals;
        $studyLength = isset($buckets->studyTotals) ? $buckets->studyTotals : 0;
        // use weekends setting to determine if the first free study should fall on a sunday
        $weekends = ($schedule->getWeekends() == 'hit-hard');

        // TODO: adjust study factor based on nothing but A's preference, only affect free time, can we do more?
        $studyFactor = 2;
        if ($schedule->getGrades() == 'as-only') {
            $studyFactor = 2.6;
        }

        // there are 16 usable hours, 4 * (5 - 1) = 16 * 7 days = 112 usable hours in a week
        $shouldStudy = min(
            $totalLength * $studyFactor,
            ($weekends ? 112 : 80) * 3600 - $totalLength
        ); // a student should study 3 [2.4] times outside of class
        $remainingStudy = min(
            14 * 3600, // turns out filling up the entire week is too much
            max($shouldStudy - $studyLength, 3600 * 5)
        ); // subtract the hours already accounted for by schedules study sessions
        $freeHours = floor($remainingStudy / 3600);
        for ($j = 0; $j < $freeHours; $j++) {

            // get the buckets for each day this week and figure out which day have the least obligations
            $bucketSums = [];
            // if no weekends subtract 3 days, don't schedule free study on Fri-Sun, we know they won't study on Fridays
            for ($i = ($weekends ? 0 : 1); $i < ($weekends ? 7 : 6); $i++) {
                $d = date('Y/m/d', $week + $i * 86400);
                $bucketSums[$d] = isset($buckets->buckets[$d]) ? array_sum($buckets->buckets[$d]) : 0;
            }
            asort($bucketSums);

            // get the day with the least obligations
            $classT = new \DateTime(key($bucketSums));
            $classT->setTime(12, 0, 0);
            $event = [
                'name' => 'Free study',
                'type' => 'f',
                'start' => $classT,
                'end' => date_add(clone $classT, new \DateInterval('PT3600S'))
            ];
            $events[] = $event;
            self::bucketEvents([$event], $buckets);
        }
        return $events;
    }

    /**
     * @param Course $course
     * @param $week
     * @return array
     */
    private static function getPrework(Course $course, $week)
    {
        $events = [];
        $classStart = $course->getStartTime();
        $classEnd = $course->getEndTime();

        $length = strtotime($classEnd->format('1/1/1970 H:i:s')) - strtotime($classStart->format('1/1/1970 H:i:s'));
        if ($length <= 0) {
            $length += 86400;
        }

        // if class is easy only study for 30 minutes
        if ($course->getStudyDifficulty() == 'easy') {
            $length /= 2;
        } elseif ($course->getStudyDifficulty() == 'tough') {
            $length *= 1.5;
        }

        // max out length of session to 1.5 hours
        if ($length > 3600) {
            $length = 3600;
        }

        foreach ($course->getDotw() as $j => $d) {
            if (!isset(self::$weekConversion[$d])) {
                continue;
            }

            $t = $week + self::$weekConversion[$d];
            if ($t < $classStart->getTimestamp() || $t > $classEnd->getTimestamp()) {
                continue;
            }

            $classT = new \DateTime();
            $classT->setTimestamp($t);
            $classT->setTime($classStart->format('H'), $classStart->format('i'), $classStart->format('s'));
            $classT->sub(new \DateInterval('P1D'));

            $event = [
                'course' => $course,
                'name' => $course->getName(),
                'type' => 'p',
                'start' => $classT,
                'end' => date_add(clone $classT, new \DateInterval('PT' . $length . 'S'))
            ];
            $events[] = $event;
        }

        return $events;
    }

    /**
     * @param Course $course
     * @param $week
     * @return array
     */
    private static function getStudy(Course $course, $week)
    {
        $events = [];
        $classStart = $course->getStartTime();
        $classEnd = $course->getEndTime();

        $length = strtotime($classEnd->format('1/1/1970 H:i:s')) - strtotime($classStart->format('1/1/1970 H:i:s'));
        if ($length <= 0) {
            $length += 86400;
        }
        $fullLength = $length;

        // if class is easy only study for 30 minutes
        if ($course->getStudyDifficulty() == 'easy') {
            $length /= 2;
        } elseif ($course->getStudyDifficulty() == 'tough') {
            $length *= 1.5;
        }

        // max out length of session to 1.5 hours
        if ($length > 5400) {
            $length = 5400;
        }

        // SR sessions should go a month after the last class
        $isSr = $course->getStudyType() == 'memorization';

        foreach ($course->getDotw() as $j => $d) {
            if (!isset(self::$weekConversion[$d])) {
                continue;
            }

            $t = $week + self::$weekConversion[$d];
            if ($t < $classStart->getTimestamp() || $t > $classEnd->getTimestamp() + ($isSr ? 2419200 : 0)) {
                continue;
            }

            $classT = new \DateTime();
            $classT->setTimestamp($t);
            $classT->setTime($classStart->format('H'), $classStart->format('i'), $classStart->format('s'));
            $classT->add(new \DateInterval('PT' . $fullLength . 'S'));

            $event = [
                'course' => $course,
                'name' => $course->getName(),
                'type' => 'sr',
                'start' => $classT,
                'end' => date_add(clone $classT, new \DateInterval('PT' . $length . 'S'))
            ];
            $events[] = $event;
        }

        return $events;
    }

    /**
     * @param $week
     * @param \stdClass $buckets
     * @return array
     */
    private static function getMeals($week, \stdClass $buckets)
    {
        $events = [];

        // add meals to every day of the week
        for ($j = -1; $j <= 7; $j++) {
            $day = new \DateTime(date('Y/m/d', $week + $j * 86400));

            // breakfast
            $classB = clone $day;
            $classB->setTime(8, 0, 0);

            $breakfast = [
                'name' => 'Breakfast',
                'type' => 'm',
                'start' => $classB,
                'end' => date_add(clone $classB, new \DateInterval('PT1800S'))
            ];

            $events[] = $breakfast;

            // lunch
            $classL = clone $day;
            $classL->setTime(12, 30, 0);

            $lunch = [
                'name' => 'Lunch',
                'type' => 'm',
                'start' => $classL,
                'end' => date_add(clone $classL, new \DateInterval('PT1800S'))
            ];
            $events[] = $lunch;

            // dinner
            $classD = clone $day;
            $classD->setTime(19, 0, 0);

            $dinner = [
                'name' => 'Dinner',
                'type' => 'm',
                'start' => $classD,
                'end' => date_add(clone $classD, new \DateInterval('PT2700S'))
            ];
            $events[] = $dinner;

            // longer sleep time on weekends, fri, sat, sun
            $length = 21600;
            //if($j == 0 || $j == 1 || $j == 6)
            //    $length = 28800;

            // sleep
            $classZ = clone $day;
            $classZ->setTime(19, 0, 0);
            // adjust time based on preferred time
            $classZ = self::getPreferredTime(
                $buckets,
                $classZ,
                $classZ->getTimestamp() - 86400,
                $classZ->getTimestamp() + 86400
            );
            if (intval($classZ->format('H')) == 6) {
                $classZ->setTime(4, 0, 0);
            } else {
                $classZ->setTime(6, 0, 0);
            }
            $sleep = [
                'name' => 'Dinner',
                'type' => 'm',
                'start' => $classZ,
                'end' => date_add(clone $classZ, new \DateInterval('PT' . $length . 'S'))
            ];
            $events[] = $sleep;
        }

        return $events;
    }

    /**
     * @param Course $course
     * @param $week
     * @return array
     */
    private static function getReoccurring(Course $course, $week)
    {
        $events = [];
        $once = false;
        // TODO: uncomment this to bring back singly occuring events
        // if ($course->getType() == 'o' && !in_array('Weekly', $course->getDotw())) {
        //    $once = true;
        //}

        $classStart = $course->getStartTime();
        $classEnd = $course->getEndTime();

        $length = strtotime($classEnd->format('1/1/1970 H:i:s')) - strtotime($classStart->format('1/1/1970 H:i:s'));
        if ($length <= 0) {
            $length += 86400;
        }
        foreach ($course->getDotw() as $j => $d) {
            if (!isset(self::$weekConversion[$d])) {
                continue;
            }

            $t = $week + self::$weekConversion[$d];
            if ($t < $classStart->getTimestamp() || $t > $classEnd->getTimestamp()) {
                continue;
            }

            $classT = new \DateTime();
            $classT->setTimestamp($t);
            // skip class on holidays
            if ($course->getType() == 'c' && isset(self::$holidays[$classT->format('Y/m/d')])) {
                continue;
            }
            $classT->setTime($classStart->format('H'), $classStart->format('i'), $classStart->format('s'));

            $event = [
                'course' => $course,
                'name' => $course->getName(),
                'type' => $course->getType(),
                'start' => $classT,
                'end' => $once
                        ? date_timestamp_set(clone $classT, min(date_add(clone $classT, new \DateInterval('PT86400S'))->getTimestamp(), $classEnd->getTimestamp()))
                        : date_add(clone $classT, new \DateInterval('PT' . $length . 'S'))
            ];
            $events[] = $event;
        }

        return $events;
    }

    /**
     * @param \stdClass $buckets
     * @param \DateTime $start
     * @param bool $notBefore
     * @param bool $notAfter
     * @return \DateTime
     */
    private static function getPreferredTime(
        \stdClass $buckets,
        \DateTime $start,
        $notBefore = false,
        $notAfter = false
    ) {
        // find the best bucket to put this event into
        $bucket = clone $start;
        $bucket->setTime(0, 0, 0);
        $b = $bucket->format('Y/m/d');
        // if it starts before 6 AM put it in the previous nights bucket
        if (intval($start->format('H')) < 6) {
            $bucket->sub(new \DateInterval('P1D'));
            $b = $bucket->format('Y/m/d');
        }

        $s = $start->getTimestamp();
        $bucketStart = $bucket->getTimestamp();

        // we are over the amount for the day
        $overfilled = false;
        if (isset($buckets->buckets[$b]) && array_sum(array_values($buckets->buckets[$b])) >= array_sum(
                array_values($buckets->timeSlots)
            )
        ) {
            // add to the bucket with the least?
            $overfilled = true;
            $tmpTimeSlots = [];

            // get time slots that are not filled yet
            foreach ($buckets->timeSlots as $t => $c) {
                if (!isset($buckets->buckets[$b][$t]) || $buckets->buckets[$b][$t] < 4) {
                    $tmpTimeSlots[$t] = $c;
                }
            }

            // get time slots in order of least filled
            if (empty($tmpTimeSlots)) {
                foreach ($buckets->timeSlots as $t => $c) {
                    $tmpTimeSlots[$t] = isset($buckets->buckets[$b][$t]) ? $buckets->buckets[$b][$t] : 0;
                }
                asort($tmpTimeSlots);
            }
        } else {
            $tmpTimeSlots = $buckets->timeSlots;
        }

        foreach ($tmpTimeSlots as $h => $count) {

            // check if the time slot is before the class, study sessions cannot be moved before classes
            if ((!empty($notBefore) && $bucketStart + $h * 3600 < $notBefore) ||
                (!empty($notAfter) && $bucketStart + $h * 3600 > $notAfter)
            ) {
                continue;
            }

            if (!isset($buckets->buckets[$b][$h]) || $buckets->buckets[$b][$h] < $count || $overfilled) {
                // calculate how far the event has to move to be in the bucket accounting for change in timezone
                $diff = $bucketStart + $h * 3600 - $s;
                $newTime = new \DateTime();
                $newTime->setTimestamp($start->getTimestamp() + $diff);
                return $newTime;
            }
        }

        return $start;
    }

    /**
     * @param $events
     * @param \stdClass $buckets
     */
    private static function bucketEvents($events, \stdClass $buckets)
    {
        foreach ($events as $i => $event) {
            // don't count all day events
            if ($event['type'] == 'h' || $event['type'] == 'd' ||
                $event['type'] == 'r' || $event['type'] == 'm' ||
                $event['type'] == 'z'
            ) {
                continue;
            }

            /** @var \DateTime $start */
            $start = $event['start'];
            /** @var \DateTime $end */
            $end = $event['end'];

            // if the event is before 4 am put in the bucket from the previous day
            $bucket = clone $start;
            $bucket->setTime(0, 0, 0);
            $b = $bucket->format('Y/m/d');
            $h = 0;
            if (intval($start->format('H')) < 6) {
                $bucket->sub(new \DateInterval('P1D'));
                $b = $bucket->format('Y/m/d');
                $h = 21;
            } elseif (intval($start->format('H')) < 11) {
                $h = 6;
            } elseif (intval($start->format('H')) < 16) {
                $h = 11;
            } elseif (intval($start->format('H')) < 21) {
                $h = 16;
            } elseif (intval($start->format('H')) < 24) {
                $h = 21;
            }

            $s = $start->getTimestamp();
            $e = $end->getTimestamp();
            $length = $e - $s;
            // initialize from start to finish plus
            $bucketStart = $bucket->getTimestamp();
            // iterate over buckets until length is used up
            while ($bucketStart + $h * 3600 < $e) {
                // TODO: the night time bucket is actually 9 hours because we don't want to over-pack before 6 AM
                // TODO: may need to adjust this to match new Sleep event type
                $time = min($bucketStart + $h * 3600 + ($h == 21 ? (3600 * 9) : (3600 * 5)), $e) - max(
                        $bucketStart + $h * 3600,
                        $s
                    );
                $buckets->buckets[$b][$h] = (isset($buckets->buckets[$b][$h]) ? $buckets->buckets[$b][$h] : 0) + $time / 3600.0;

                // skip sleepy time and increment the $b bucket index 1 day
                if ($h == 21) {
                    $h = 6;
                    $bucket->add(new \DateInterval('P1D'));
                    $b = $bucket->format('Y/m/d');
                    $bucketStart += 86400;
                } else {
                    $h += 5;
                }
            }
            if ($event['type'] == 'c') {
                $buckets->classTotals = (isset($buckets->classTotals) ? $buckets->classTotals : 0) + ($length + 600);
            } elseif ($event['type'] == 'sr' || $event['type'] == 'p') {
                $buckets->studyTotals = (isset($buckets->studyTotals) ? $buckets->studyTotals : 0) + ($length + 600);
            }
        }
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateStrategyAction(Request $request)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var User $user */
        $user = $this->getUser();

        /** @var Schedule $schedule */
        $schedule = $user->getSchedules()->first();

        $strategies = $request->get('strategies');
        foreach($strategies as $i => $s)
        {
            /** @var Event $event */
            $event = $schedule->getEvents()->filter(function (Event $e)use($s) {return $e->getId() == $s['eventId'];})->first();
            if(empty($event))
                continue;

            if($s['type'] == 'active')
            {
                $new = false;
                if(($active = $event->getActive()) == null) {
                    $active = new ActiveStrategy();
                    $event->setActive($active);
                    $active->setEvent($event);
                    $active->setSchedule($schedule);
                    $new = true;
                }
                $active->setIsDefault($request->get('default') == 'active');
                $active->setSkim($s['skim']);
                $active->setWhy($s['why']);
                $active->setSummarize($s['summarize']);
                $active->setQuestions($s['questions']);
                $active->setExam($s['exam']);
                if($new)
                    $orm->persist($active);
                else
                    $orm->merge($active);
            }
            elseif($s['type'] == 'prework')
            {
                $new = false;
                if(($prework = $event->getPrework()) == null) {
                    $prework = new PreworkStrategy();
                    $event->setPrework($prework);
                    $prework->setEvent($event);
                    $prework->setSchedule($schedule);
                    $new = true;
                }
                $prework->setIsDefault($request->get('default') == 'prework');
                $prework->setNotes($s['notes']);
                $prework->setPrepared(explode(',', $s['prepared']));
                if($new)
                    $orm->persist($prework);
                else
                    $orm->merge($prework);
            }
            elseif($s['type'] == 'teach')
            {
                $new = false;
                if(($teach = $event->getTeach()) == null) {
                    $teach = new TeachStrategy();
                    $event->setTeach($teach);
                    $teach->setEvent($event);
                    $teach->setSchedule($schedule);
                    $new = true;
                }
                $teach->setIsDefault($request->get('default') == 'teach');
                $teach->setNotes($s['notes']);
                $teach->setTitle($s['title']);
                $teach->setTeaching($user->getFiles()->filter(function (File $f)use($s) {return $f->getId() == $s['fid'];})->first());
                if($new)
                    $orm->persist($teach);
                else
                    $orm->merge($teach);
            }
            elseif($s['type'] == 'other')
            {
                $new = false;
                if(($other = $event->getOther()) == null) {
                    $other = new OtherStrategy();
                    $event->setOther($other);
                    $other->setEvent($event);
                    $other->setSchedule($schedule);
                    $new = true;
                }
                $other->setIsDefault($request->get('default') == 'other');
                $other->setNotes($s['notes']);
                if($new)
                    $orm->persist($other);
                else
                    $orm->merge($other);
            }
            elseif($s['type'] == 'spaced')
            {
                $new = false;
                if(($spaced = $event->getSpaced()) == null) {
                    $spaced = new SpacedStrategy();
                    $event->setSpaced($spaced);
                    $spaced->setEvent($event);
                    $spaced->setSchedule($schedule);
                    $new = true;
                }
                $spaced->setIsDefault($request->get('default') == 'spaced');
                $spaced->setNotes($s['notes']);
                $spaced->setReview(explode(',', $s['review']));
                if($new)
                    $orm->persist($spaced);
                else
                    $orm->merge($spaced);
            }
            $orm->merge($event);
        }
        $orm->flush();
        return new JsonResponse(true);
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

    /**
     * @param Schedule $schedule
     * @return array
     */
    private static function getStrategies(Schedule $schedule)
    {
        $result = [];
        foreach($schedule->getActive()->toArray() as $active)
        {
            /** @var ActiveStrategy $active */
            $eventId = $active->getEvent()->getId();
            $result[$eventId]['active']['default'] = $active->getIsDefault();
            $result[$eventId]['active']['skim'] = $active->getSkim();
            $result[$eventId]['active']['why'] = $active->getWhy();
            $result[$eventId]['active']['questions'] = $active->getQuestions();
            $result[$eventId]['active']['summarize'] = $active->getSummarize();
            $result[$eventId]['active']['exam'] = $active->getExam();
        }
        foreach($schedule->getOther()->toArray() as $other)
        {
            /** @var OtherStrategy $other */
            $eventId = $other->getEvent()->getId();
            $result[$eventId]['other']['default'] = $other->getIsDefault();
            $result[$eventId]['other']['notes'] = $other->getNotes();
        }
        foreach($schedule->getTeach()->toArray() as $teach)
        {
            /** @var TeachStrategy $teach */
            $eventId = $teach->getEvent()->getId();
            $result[$eventId]['teach']['default'] = $teach->getIsDefault();
            $result[$eventId]['teach']['title'] = $teach->getTitle();
            $result[$eventId]['teach']['notes'] = $teach->getNotes();
            $result[$eventId]['teach']['url'] = !empty($teach->getTeaching()) ? $teach->getTeaching()->getUrl() : null;
            $result[$eventId]['teach']['fid'] = !empty($teach->getTeaching()) ? $teach->getTeaching()->getId() : null;
        }
        foreach($schedule->getSpaced()->toArray() as $spaced)
        {
            /** @var SpacedStrategy $spaced */
            $eventId = $spaced->getEvent()->getId();
            $result[$eventId]['spaced']['default'] = $spaced->getIsDefault();
            $result[$eventId]['spaced']['notes'] = $spaced->getNotes();
            $result[$eventId]['spaced']['review'] = implode(',', $spaced->getReview());
        }
        foreach($schedule->getPrework()->toArray() as $prework)
        {
            /** @var PreworkStrategy $prework */
            $eventId = $prework->getEvent()->getId();
            $result[$eventId]['prework']['default'] = $prework->getIsDefault();
            $result[$eventId]['prework']['notes'] = $prework->getNotes();
            $result[$eventId]['prework']['prepared'] = implode(',', $prework->getPrepared());
        }
        return $result;
    }
}



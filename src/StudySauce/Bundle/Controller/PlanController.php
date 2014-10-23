<?php

namespace StudySauce\Bundle\Controller;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use StudySauce\Bundle\Entity\Course;
use StudySauce\Bundle\Entity\Event;
use StudySauce\Bundle\Entity\Schedule;
use StudySauce\Bundle\Entity\Week;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;

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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
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

        $events = self::rebuildSchedule($schedule, $schedule->getCourses(), strtotime('last Sunday'), $orm);

        return $this->render('StudySauceBundle:Plan:tab.html.php', [
                'events' =>  self::getJsonEvents($events, $schedule->getCourses()->filter(function (Course $c) {
                            return $c->getType() == 'c';
                        })->toArray())
            ]);
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
        if (!empty($schedule) && !empty($schedule->getCourses())) {
            $events = self::rebuildSchedule($schedule, $schedule->getCourses(), strtotime('last Sunday'), $orm);
        } else {
            /** @var $userManager UserManager */
            $userManager = $this->get('fos_user.user_manager');

            $demo = ScheduleController::getDemoSchedule($userManager, $orm);

            $events = self::rebuildSchedule($demo, $demo->getCourses(), strtotime('last Sunday'), $orm);
        }

        return $this->render(
            'StudySauceBundle:Plan:widget.html.php',
            [
                'events' => $events,
                'classes' => array_map(function (Course $c) {return $c->getName();},
                    $schedule->getCourses()->filter(function (Course $c) {
                            return $c->getType() == 'c';
                        })->toArray())
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
        $classes = array_map(function (Course $c) {return $c->getName();}, $courses);
        $jsEvents = [];
        foreach ($events as $i => $x) {
            /** @var Event $x */
            // skip data entry events
            if ($x->getDeleted()) {
                continue;
            }

            $classI = array_search($x->getName(), $classes);
            if ($classI === false)
                $classI = '';
            else
                /** @var Course $c */
                $c = $courses[$classI];

            $label = '';
            $skip = false;
            switch($x->getType()) {
                case 'c':
                    $label = 'CLASS';
                    break;
                case 'sr':
                case 'f':
                    $label = 'STUDY';
                    break;
                case 'p':
                    $label = 'PRE-WORK';
                    break;
                case 'o':
                    $label = 'OTHER';
                    break;
                case 'd':
                    $label = 'DEADLINE';
                    break;
                case 'h':
                    $label = 'HOLIDAY';
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
            if (isset($c) && $x->getType() == 'sr') {
                $startDay = $c->getStartTime()->getTimestamp();
                $endDay = $c->getEndTime()->getTimestamp();
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
                'cid' => $i,
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
     * @param $week
     * @param EntityManager $orm
     * @return array
     */
    public static function rebuildSchedule(Schedule $schedule, Collection $courses, $week, EntityManager $orm)
    {
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
        $classes = $courses->filter(
            function (Course $c) {
                return $c->getType() == 'c';
            }
        )->toArray();
        $reoccurring = [];
        foreach ($classes as $i => $course) {
            /** @var Course $course */
            $reoccurring = array_merge($reoccurring, self::getReoccurring($course, $week));
        }
        $others = $courses->filter(
            function (Course $c) {
                return $c->getType() == 'o';
            }
        )->toArray();
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

        // cache and compare list of events in their default positions
        $weekHash = md5(serialize($events));
        if (($eventWeek = $schedule->getWeeks()->filter(
                function (Week $w) use ($weekHash, $events) {
                    return $w->getHash() == $weekHash && $w->getEvents()->count() == count($events);
                }
            )->first()) != null
        ) {
            /** @var Week $eventWeek */
            // if the event hash matches use final arrangement from week cache
            $matchingEvents = $eventWeek->getEvents()->toArray();
            $events = array_map(
                function (Event $e, Event $_) {
                    $e->setType($_->getType());
                    $e->setName($_->getName());
                    $e->setStart(clone $_->getStart());
                    $e->setEnd(clone $_->getEnd());
                    return $e;
                },
                $events,
                $matchingEvents
            );
        }

        // get the current week
        if (($currentWeek = $schedule->getWeeks()->filter(
                function (Week $week) use ($weekHash, $w) {
                    return $week->getWeek() == $w->format('W') &&
                    $week->getYear() == $w->format('Y');
                }
            )->first()) == null
        ) {
            // create a new week entity to cache the events
            $currentWeek = new Week();
            $currentWeek->setStart($w);
            $currentWeek->setWeek($w->format('W'));
            $currentWeek->setHash($weekHash);
            $currentWeek->setSchedule($schedule);
            $currentWeek->setYear($w->format('Y'));
            $schedule->addWeek($currentWeek);
            $orm->persist($currentWeek);
            $orm->flush();
        } else {
            $currentWeek->setHash($weekHash);
            $orm->merge($currentWeek);
            $orm->flush();
        }

        if ($eventWeek == null) {
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
        }

        self::mergeSaved($schedule, $currentWeek, $events, $orm);

        return $events;
    }

    /**
     * @param $events
     */
    private static function sortEvents(&$events)
    {
        usort($events, function (Event $a, Event $b) use ($events) {
                return $a->getStart()->getTimestamp() - $b->getStart()->getTimestamp();
            });
    }

    /**
     * @param Event $event
     * @return array
     */
    private static function getMealBoundaries(Event $event)
    {
        switch($event->getName())
        {
            case 'Breakfast':
                return [$event->getStart()->getTimestamp() - 3600 * 3, $event->getStart()->getTimestamp() + 3600 * 2];
                break;
            case 'Lunch':
                return [$event->getStart()->getTimestamp() - 3600 * 2, $event->getStart()->getTimestamp() + 3600 * 3];
                break;
            case 'Dinner':
                return [$event->getStart()->getTimestamp() - 3600 * 2.5, $event->getStart()->getTimestamp() + 3600 * 5.5];
                break;
        }
        throw new Exception('Un-recognized meal');
    }

    /**
     * @param Event $event
     * @param $workingEvents
     * @param $week
     * @param Schedule $schedule
     * @return array
     */
    private static function getBoundaries(Event $event, $workingEvents, $week, Schedule $schedule)
    {
        switch($event->getType())
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
     * @param Event $event
     * @param $workingEvents
     * @return array
     */
    private static function getPreworkBoundaries(Event $event, $workingEvents)
    {
        // the boundary for prework is the previous and next class
        // count forwards to matching type
        for($j = 0; $j < count($workingEvents); $j++)
        {
            /** @var Event $e */
            $e = $workingEvents[$j];
            // stop after we pass current events time
            if($event->getEnd()->getTimestamp() - $e->getStart()->getTimestamp() < 0)
                break;
            if($e->getName() == $event->getName())
                return [$e->getEnd()->getTimestamp(), $event->getStart()->getTimestamp() + 86400];
        }
        return [$event->getStart()->getTimestamp() - 86400, $event->getStart()->getTimestamp() + 86400];
    }

    /**
     * @param Event $event
     * @param $workingEvents
     * @return array
     */
    private static function getStudyBoundaries(Event $event, $workingEvents)
    {
        // the boundary for study is the previous and next class
        // count backwards to matching type
        for($j = count($workingEvents) - 1; $j >= 0 ; $j--)
        {
            /** @var Event $e */
            $e = $workingEvents[$j];
            // stop after we pass the current events time
            if($e->getStart()->getTimestamp() - $event->getEnd()->getTimestamp() < 0)
                break;
            if($e->getName() == $event->getName())
                return [$event->getStart()->getTimestamp(), $e->getStart()->getTimestamp()];
        }
        return [$event->getStart()->getTimestamp(), $event->getStart()->getTimestamp() + 86400 * 2];
    }

    /**
     * @param Event $event
     * @return array
     */
    private static function getSleepBoundaries(Event $event)
    {
        $length = $event->getEnd()->getTimestamp() - $event->getStart()->getTimestamp();
        $mealTimestamp = $event->getStart()->getTimestamp();
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
            /** @var Event $event */
            if($i == $index ||
                // ignore all day events
                $event->getType() == 'd' || $event->getType() == 'h' ||
                $event->getType() == 'r')
                continue;

            $startDistance = $event->getStart()->getTimestamp() - $notBefore;
            $endDistance = $notAfter - $event->getEnd()->getTimestamp();
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
     * @param Event $event
     * @param $buckets
     * @param $notBefore
     * @param $notAfter
     */
    private static function removeOverlaps($workingEvents, Event $event, $buckets, $notBefore, $notAfter)
    {
        // find gaps
        if(empty($workingEvents)) {
            return;
        }
        $length = $event->getEnd()->getTimestamp() - $event->getStart()->getTimestamp();
        $gapStart = new \DateTime();
        $gapStart->setTimestamp($notBefore);
        $last = $gapStart->getTimestamp();
        $gaps = [];
        foreach ($workingEvents as $c => $e) {
            /** @var Event $e */
            $gapEnd = clone $e->getStart();
            $gapEnd->setTimestamp(min($gapEnd->getTimestamp(), $notAfter));
            $gap = $gapEnd->getTimestamp() - $gapStart->getTimestamp();

            // make sure gap is large enough for the event
            if ($gap >= $length + 2 * self::OVERLAP_INCREMENT &&
                $gapEnd->getTimestamp() > $notBefore &&
                $gapStart->getTimestamp() < $notAfter
            ) {
                $gaps['i'.$c] = clone $gapStart;
            }

            $gapStart = clone $e->getEnd();
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
            $workingEvents['empty'] = new \stdClass();
            $workingEvents['empty']->getStart = function () use ($gapEnd) {return clone $gapEnd;};
        }

        // get the closest opening to the original time
        // TODO: store list of gaps by adjusting the ends when the new event is added
        $closest = [];
        foreach ($gaps as $c => $d) {
            /** @var \DateTime $d */
            // reset the time so we pick out the closest day,
            //    this levels the playing field for all daily preferred times
            $startDay = clone $event->getStart();
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
            /** @var Event $e */
            $e = $workingEvents[intval(substr($c, 1))];
            $gapEnd = clone $e->getStart();
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
            if ($event->getType() == 'p') {
                $preferred = clone $start;
                $preferred->setTime(6, 0, 0);
            } elseif ($event->getType() == 'm' || $event->getType() == 'z' || (
                    isset($buckets->buckets[$b]) && array_sum(array_values($buckets->buckets[$b])) >= array_sum(
                        array_values($buckets->timeSlots)))
            ) {
                $preferred = clone $event->getStart();
            } else {
                $preferred = clone self::getPreferredTime($buckets, $start, $start->getTimestamp() - 86400, $start->getTimestamp() + 86400);
            }

            $distance[$c] = $closest[$c] + (abs($start->getTimestamp() - $preferred->getTimestamp()));
            /** @var Event $e */
            $e = $workingEvents[intval(substr($c, 1))];
            $gapEnd = clone $e->getStart();
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
                /** @var Event $start */
                $start = $workingEvents[intval(substr($top, 1))];
                $gapEnd = clone $start->getStart();
                $gapEnd->setTimestamp(min($gapEnd->getTimestamp(), $notAfter));
                $gapEnd->sub(new \DateInterval('PT' . ($length + self::OVERLAP_INCREMENT) . 'S'));
                $event->setStart($gapEnd);
                $event->setEnd(date_add(clone $gapEnd, new \DateInterval('PT' . $length . 'S')));
            } else {
                /** @var \DateTime $d */
                $d = clone $gaps[$top];
                $d->add(new \DateInterval('PT' . self::OVERLAP_INCREMENT . 'S'));
                $event->setStart($d);
                $event->setEnd(date_add(clone $d, new \DateInterval('PT' . $length . 'S')));
            }
        }

        // TODO: send assertion email when we fail to find a spot
        if (!empty($workingEvents) && $event->getType() != 'm' && $event->getType() != 'z') {
            $unresolved[] = $event;
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
            /** @var Event $event */
            // check if we have a saved event around the same time
            $lastEvent = null;
            if (!($event->getType() == 'm' || $event->getType() == 'z' ||
                $event->getType() == 'c' || $event->getType() == 'd' || $event->getType() == 'h')
            ) {
                $lastDistance = 172800;

                foreach ($saved->filter(
                    function (Event $e) use ($event) {
                        return $e->getType() == $event->getType() && $e->getName() == $event->getName();
                    }
                )->toArray() as $j => $e) {
                    /** @var Event $e */
                    // if saved is 24 hours before current event, skip and never go back because we are ordered by time
                    $distance = abs($event->getStart()->getTimestamp() - $e->getStart()->getTimestamp());
                    if ($distance < $lastDistance) {
                        $lastEvent = $e;
                        $lastDistance = $distance;
                    } elseif (!empty($lastEvent)) {
                        break;
                    }
                }
            }

            if ($lastEvent) {
                $lastEvent->setStart($event->getStart());
                $lastEvent->setEnd($event->getEnd());
                $events[$i] = $lastEvent;
                $saved->removeElement($lastEvent);
            }
        }

        // remove unused saved events
        $remove = $saved->toArray();
        foreach ($remove as $i => $event) {
            // TODO: check if in strategies
            $schedule->removeEvent($event);
            $orm->remove($event);
        }

        $orm->flush();

        foreach ($events as $i => $event) {
            if($event->getWeek() == null)
            {
                $event->setSchedule($schedule);
                $event->setWeek($week);
                $schedule->addEvent($event);
                $orm->persist($event);
            }
            else
            {
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

            $event = new Event();
            $event->setName('Free study');
            $event->setType('f');
            $event->setStart($classT);
            $event->setEnd(date_add(clone $classT, new \DateInterval('PT3600S')));
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

            $event = new Event();
            $event->setName($course->getName());
            $event->setType('p');
            $event->setStart($classT);
            $event->setEnd(date_add(clone $classT, new \DateInterval('PT' . $length . 'S')));
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

            $event = new Event();
            $event->setName($course->getName());
            $event->setType('sr');
            $event->setStart($classT);
            $event->setEnd(date_add(clone $classT, new \DateInterval('PT' . $length . 'S')));
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
            $breakfast = new Event();
            $classB = clone $day;
            $classB->setTime(8, 0, 0);

            $breakfast->setName('Breakfast');
            $breakfast->setType('m');
            $breakfast->setStart($classB);
            $breakfast->setEnd(date_add(clone $classB, new \DateInterval('PT1800S')));
            $events[] = $breakfast;

            // lunch
            $lunch = new Event();
            $classL = clone $day;
            $classL->setTime(12, 30, 0);

            $lunch->setName('Lunch');
            $lunch->setType('m');
            $lunch->setStart($classL);
            $lunch->setEnd(date_add(clone $classL, new \DateInterval('PT1800S')));
            $events[] = $lunch;

            // dinner
            $dinner = new Event();
            $classD = clone $day;
            $classD->setTime(19, 0, 0);

            $dinner->setName('Dinner');
            $dinner->setType('m');
            $dinner->setStart($classD);
            $dinner->setEnd(date_add(clone $classD, new \DateInterval('PT2700S')));
            $events[] = $dinner;

            // longer sleep time on weekends, fri, sat, sun
            $length = 21600;
            //if($j == 0 || $j == 1 || $j == 6)
            //    $length = 28800;

            // sleep
            $sleep = new Event();
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
            $sleep->setName('Sleep');
            $sleep->setType('z');
            $sleep->setStart($classZ);
            $sleep->setEnd(date_add(clone $classZ, new \DateInterval('PT' . $length . 'S')));
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
        if ($course->getType() == 'o' && !in_array('Weekly', $course->getDotw())) {
            $once = true;
        }

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

            $event = new Event();
            $event->setName($course->getName());
            $event->setType($course->getType());
            $event->setStart($classT);
            $event->setEnd(
                $once
                    ? date_timestamp_set(
                    clone $classT,
                    min(date_add($classT->getTimestamp() + 86400, $classEnd->getTimestamp()))
                )
                    : date_add(clone $classT, new \DateInterval('PT' . $length . 'S'))
            );
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
            /** @var Event $event */
            // don't count all day events
            if ($event->getType() == 'h' || $event->getType() == 'd' ||
                $event->getType() == 'r' || $event->getType() == 'm' ||
                $event->getType() == 'z'
            ) {
                continue;
            }

            $start = $event->getStart();
            $end = $event->getEnd();

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
            if ($event->getType() == 'c') {
                $buckets->classTotals = (isset($buckets->classTotals) ? $buckets->classTotals : 0) + ($length + 600);
            } elseif ($event->getType() == 'sr' || $event->getType() == 'p') {
                $buckets->studyTotals = (isset($buckets->studyTotals) ? $buckets->studyTotals : 0) + ($length + 600);
            }
        }
    }
}



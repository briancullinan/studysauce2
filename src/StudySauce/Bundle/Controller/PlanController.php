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

/**
 * Class PlanController
 * @package StudySauce\Bundle\Controller
 */
class PlanController extends Controller
{
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
        return $this->render('StudySauceBundle:Plan:tab.html.php');
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
            ScheduleController::getDemoCourses($demo, $orm);
            ScheduleController::getDemoOthers($demo, $orm);

            $events = self::rebuildSchedule($demo, $demo->getCourses(), strtotime('last Sunday'), $orm);
        }

        return $this->render(
            'StudySauceBundle:Plan:widget.html.php',
            [
                'events' => $events
            ]
        );
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
        $buckets->studyTotals = [];
        $buckets->classTotals = [];
        $buckets->timeSlots = [
            16 => ($schedule->getSharp4pm9pm() ?: 2) / 5.0 * 4,
            21 => ($schedule->getSharp11am4pm() ?: 2) / 5.0 * 4,
            11 => ($schedule->getSharp9pm2am() ?: 2) / 5.0 * 4,
            6 => ($schedule->getSharp6am11am() ?: 2) / 5.0 * 4
        ];
        arsort($buckets->timeSlots);

        $w = new \DateTime('@' . $week);

        // TODO: get holidays and deadlines

        // add reoccurring events
        $classes = $courses->filter(
            function (Course $c) {
                return $c->getType() == 'c';
            }
        )->toArray();
        foreach ($classes as $i => $course) {
            /** @var Course $course */
            $events = array_merge($events, self::getReoccurring($course, $week, $buckets));
        }
        $others = $courses->filter(
            function (Course $c) {
                return $c->getType() == 'o';
            }
        )->toArray();
        foreach ($others as $i => $other) {
            /** @var Course $other */
            $events = array_merge($events, self::getReoccurring($other, $week, $buckets));
        }

        // add meals just for proper spacing
        $events = array_merge($events, self::getMeals($week, $buckets));

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
        uasort(
            $labsLast,
            function ($a, $b) {
                return $a[2] - $b[2];
            }
        );

        foreach ($labsLast as $i => $count) {
            // set up pre-work
            if ($count[0] == 'p') {
                /** @var Course $course */
                $course = $count[1];
                $events = array_merge($events, self::getPrework($course, $week));
            } // set up sr-sessions
            elseif ($count[0] == 'sr') {
                $course = $count[1];
                $events = array_merge($events, self::getStudy($course, $week));
            }
        }


        // get totals for events in current position
        self::bucketEvents($events, $week, $buckets);

        // get free events
        $events = array_merge($events, self::getFree($buckets, $schedule));

        // cache and compare list of events in their default positions
        $weekHash = md5(implode('', array_map(function (Event $event) {return $event->getType() . $event->getName();}, $events)));
        if(($eventWeek = $schedule->getWeeks()->filter(function (Week $w) use ($weekHash) {
                return $w->getHash() == $weekHash;
            })->first()) != null)
        {
            /** @var Week $eventWeek */
            // if the event hash matches use final arrangement from week cache
            $events = array_map(function (Event $e, Event $_) {
                    $e->setStart($_->getStart());
                    $e->setEnd($_->getEnd());
                    return $e;
                }, $events, $eventWeek->getEvents()->toArray());
        }

        // get the current week
        if(($currentWeek = $schedule->getWeeks()->filter(function (Week $week) use ($weekHash, $w) {
                return $week->getWeek() == $w->format('W') &&
                $week->getYear() == $w->format('Y');
            })->first()) == null)
        {
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
        }
        else
        {
            $currentWeek->setHash($weekHash);
            $orm->merge($currentWeek);
            $orm->flush();
        }

        if($eventWeek == null)
        {
            // TODO: remove overlaps on all events

        }

        self::mergeSaved($schedule, $currentWeek, $events, $orm);

        $orm->flush();

        return $events;
    }

    /**
     * @param Schedule $schedule
     * @param Week $week
     * @param array $events
     * @param EntityManager $orm
     */
    private static function mergeSaved(Schedule $schedule, Week $week, array $events, EntityManager $orm)
    {
        // find matching saved events
        foreach($events as $i => $event)
        {
            /** @var Event $event */

            // check if we have a saved event around the same time
            $lastEvent = null;
            $lastDistance = 172800;

            foreach ($week->getEvents()->filter(function (Event $e) use ($event) {
                    return $e->getType() == $event->getType();
                }) as $j => $e) {
                /** @var Event $e */
                // if saved is 24 hours before current event, skip and never go back because we are ordered by time
                $distance = abs($event->getStart()->getTimestamp() - $e->getStart()->getTimestamp());
                if ($distance < $lastDistance) {
                    $lastEvent = $e;
                    $lastDistance = $distance;
                } elseif (!empty($lastEvent))
                    break;
            }

            if ($lastEvent) {
                $lastEvent->setStart($event->getStart());
                $lastEvent->setEnd($event->getEnd());
                $events[$i] = $lastEvent;
                $orm->merge($lastEvent);
            }
            else {
                $event->setSchedule($schedule);
                $event->setWeek($week);
                $schedule->addEvent($event);
                $orm->persist($event);
            }
        }
    }

    /**
     * @param $buckets
     * @param Schedule $schedule
     * @return array
     */
    private static function getFree($buckets, Schedule $schedule)
    {
        $events = [];
        // add free study to every
        foreach ($buckets->classTotals as $w => $totalLength) {
            $studyLength = isset($studyTotals[$w]) ? $studyTotals[$w] : 0;
            // use weekends setting to determine if the first free study should fall on a sunday
            $weekends = ($schedule->getWeekends() == 'hit_hard');

            // TODO: adjust study factor based on nothing but A's preference, only affect free time, can we do more?
            $studyFactor = 2;
            if ($schedule->getGrades() == 'as_only')
                $studyFactor = 2.6;

            // there are 16 usable hours, 4 * (5 - 1) = 16 * 7 days = 112 usable hours in a week
            $shouldStudy = min($totalLength * $studyFactor, ($weekends ? 112 : 80) * 3600 - $totalLength); // a student should study 3 [2.4] times outside of class
            $remainingStudy = min(14 * 3600, // turns out filling up the entire week is too much
                max($shouldStudy - $studyLength, 3600 * 5)); // subtract the hours already accounted for by schedules study sessions
            $freeHours = floor($remainingStudy / 3600);
            for ($j = 0; $j < $freeHours; $j++) {

                // get the buckets for each day this week and figure out which day have the least obligations
                $bucketSums = [];
                // if no weekends subtract 3 days, don't schedule free study on Fri-Sun, we know they won't study on Fridays
                for ($i = ($weekends ? 0 : 1); $i < ($weekends ? 7 : 6); $i++)
                {
                    $d = date('Y/m/d', $w + $i * 86400);
                    $bucketSums[$d] = isset($buckets[$d]) ? array_sum($buckets[$d]) : 0;
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
            }
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
        for ($j = 0; $j < 7; $j++) {
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

            // check if we have a saved event around the same time
            /* TODO: this later after the events are loaded, repair them
            $lastEid = 0;
            $lastDistance = 172800;
            // TODO: use each so we save our location
            foreach ($saved as $eid => $event) {
                // if saved is 24 hours before current event, skip and never go back because we are ordered by time
                $distance = abs(strtotime($event->field_time['und'][0]['value']) - $classT->getTimestamp());
                if ($distance < $lastDistance) {
                    $lastEid = $eid;
                    $lastDistance = $distance;
                } elseif ($lastEid > 0)
                    break;
            }

            if ($lastEid) {
                $events[$lastEid] = $saved[$lastEid];
                unset($saved[$lastEid]);
                $events[$lastEid]->field_time[LANGUAGE_NONE][0] = array(
                    'value' => $classT->format('Y/m/d H:i:s'),
                    'value2' => date_add($classT, new DateInterval('PT' . $length . 'S'))->format('Y/m/d H:i:s')
                );

                studysauce_bucket_event($node, $events, $lastEid);
            } else {
*/
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

                return new \DateTime('@' . ($start->getTimestamp() + $diff));
            }
        }

        return $start;
    }

    /**
     * @param $events
     * @param $week
     * @param \stdClass $buckets
     */
    private static function bucketEvents($events, $week, \stdClass $buckets)
    {
        foreach ($events as $i => $event) {
            /** @var Event $event */
            // don't count all day events
            if ($event->getType() == 'h' || $event->getType() == 'd' ||
                $event->getType() == 'r' || $event->getType() == 'm' ||
                $event->getType() == 'z'
            ) {
                return;
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
                $buckets->classTotals[$week] = ($buckets->classTotals[$week] ?: 0) + ($event->getEnd()->getTimestamp() - $event->getStart()->getTimestamp() + 600);
            } elseif ($event->getType() == 'sr' || $event->getType() == 'p') {
                $buckets->studyTotals[$week] = ($buckets->studyTotals[$week] ?: 0) + ($event->getEnd()->getTimestamp() - $event->getStart()->getTimestamp() + 600);
            }
        }
    }
}



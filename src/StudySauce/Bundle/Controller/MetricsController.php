<?php

namespace StudySauce\Bundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use StudySauce\Bundle\Entity\Checkin;
use StudySauce\Bundle\Entity\Course;
use StudySauce\Bundle\Entity\Goal;
use StudySauce\Bundle\Entity\Schedule;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class MetricsController
 * @package StudySauce\Bundle\Controller
 */
class MetricsController extends Controller
{
    /**
     * @param User $user
     * @param array $template
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(User $user = null, $template = ['Metrics', 'tab'])
    {
        /** @var $user User */
        if(empty($user))
            $user = $this->getUser();

        /** @var $schedule Schedule */
        $schedule = $user->getSchedules()->first();
        if(!empty($schedule))
            $courses = $schedule->getClasses()->toArray();

        $isDemo = false;
        if(!empty($schedule) && !empty($courses)) {
            list($checkins, $checkouts) = self::cleanCheckins($courses);
        }
        if(empty($checkins)) {
            $isDemo = true;
            $demo = ScheduleController::getDemoSchedule($this->container);
            $courses = $demo->getClasses()->toArray();
            list($checkins, $checkouts) = self::getDemoCheckins($this->container);
        }

        list($times, $total) = self::getTimes($checkins, $checkouts, $courses);

        /** @var $goal Goal */
        $goal = $user->getGoals()->filter(function (Goal $x) {return $x->getType() == 'behavior';})->first();
        return $this->render('StudySauceBundle:' . $template[0] . ':' . $template[1] . '.html.php', [
                'hours' => !empty($goal) ? $goal->getGoal() : '',
                'total' => $total,
                'courses' => $courses,
                'checkins' => $checkins,
                'checkouts' => $checkouts,
                'times' => $times,
                'user' => $user,
                'isDemo' => $isDemo
            ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function widgetAction()
    {
        /** @var $user User */
        $user = $this->getUser();

        /** @var $schedule Schedule */
        $schedule = $user->getSchedules()->first();
        if(!empty($schedule))
            $courses = $schedule->getClasses()->toArray();
        else
            $courses = [];

        list($checkins, $checkouts) = self::cleanCheckins($courses);
        list($times, $total) = self::getTimes($checkins, $checkouts, $courses);

        /** @var $goal Goal */
        $goal = $user->getGoals()->filter(function (Goal $x) {return $x->getType() == 'behavior';})->first();
        return $this->render('StudySauceBundle:Metrics:widget.html.php', [
                'hours' => !empty($goal) ? $goal->getGoal() : '',
                'total' => $total,
                'courses' => array_values($courses),
                'checkins' => $checkins,
                'checkouts' => $checkouts,
                'times' => $times
            ]);
    }

    /**
     * @param $checkins
     * @param $checkouts
     * @param $courses
     * @return array
     */
    public static function getTimes($checkins, $checkouts, $courses)
    {

        $times = [];
        $timeGroups = [];
        $total = 0;
        foreach($checkins as $t => $c)
        {
            /** @var $c Checkin */
            list($k, $l) = each($checkouts);

            // since we are already in order by time, sum up the other lengths on this day
            $courseId = $c->getCourse()->getId();

            $times[] = [
                'time' => $c->getCheckin()->getTimestamp(),
                'length' => $l,
                'class' => $c->getCourse()->getName(),
                'courseId' => $courseId
            ];

            $total += $l;
        }

        $total = round($total / 3600, 1);

        self::sortByClassThenTime($times, array_map(function (Course $c) {return $c->getId(); }, $courses));

        foreach($times as $i => $t)
        {
            $courseId = $t['courseId'];
            $date = new \DateTime();
            $date->setTimestamp($t['time']);
            $date->setTime(0, 0, 0);
            $date = $date->add(new \DateInterval('P1D'));
            $g = $date->format('W');
            if(!isset($timeGroups[$g][$courseId][0]))
            {
                $length0 = 0;
            }
            else
            {
                $length0 = array_sum($timeGroups[$g][$courseId]);
            }
            $timeGroups[$g][$courseId][] = $t['length'];
            $times[$i]['length0'] = $length0;
        }

        return [$times, $total];
    }

    /**
     * @param $times
     * @param $ids
     */
    public static function sortByClassThenTime(&$times, $ids)
    {
        // sort by class then time
        $classes = array_map(function ($x) use ($ids) {
                return array_search($x['courseId'], array_values($ids));
            }, (array)$times);
        $ts = array_map(function ($x) {
                $x = (array)$x;
                return $x['time'];
            }, (array)$times);
        array_multisort($classes, SORT_NUMERIC, SORT_ASC, $ts, SORT_NUMERIC, SORT_ASC, $times);
    }

    private static $randomLengths = [30,45,50,60];

    /**
     * @param ContainerInterface $container
     * @return ArrayCollection
     */
    public static function getDemoCheckins($container)
    {
        /** @var $orm EntityManager */
        $orm = $container->get('doctrine')->getManager();
        $demo = ScheduleController::getDemoSchedule($container);
        $courses = array_values($demo->getCourses()->toArray());
        $_week = strtotime('last Sunday');
        if ($_week + 604800 == strtotime('today')) {
            $_week += 604800;
        }

        // automatically generate 20 checkins per week, for the last 5 weeks
        for($w = -4; $w < 1; $w++)
        {
            $dailyCount = 0;
            foreach ($courses as $i => $c) {
                /** @var Course $c */
                $dailyCount += $c->getCheckins()->filter(function (Checkin $ch) use ($_week, $w) {
                        return $ch->getCheckin()->getTimestamp() > ($_week + 604800 * $w) &&
                            $ch->getCheckin()->getTimestamp() < ($_week + 604800 * $w + 604800);
                    })->count();
            }
            if($dailyCount == 0)
            {
                for($j = 0; $j < 20; $j++)
                {
                    // choose course at random
                    $c = $courses[array_rand($courses, 1)];
                    $l = self::$randomLengths[array_rand(self::$randomLengths, 1)];
                    $checkin = new Checkin();
                    $checkin->setCourse($c);
                    $c->addCheckin($checkin);
                    $t = clone $c->getEndTime();
                    $t->setTimestamp($_week + 604800 * $w);
                    $t->setTime(intval($c->getEndTime()->format('H')), 0, 0);
                    $checkin->setCheckin($t);
                    $checkin->setUtcCheckin(date_timezone_set(clone $t, new \DateTimeZone('UTC')));
                    $checkin->setCheckout(date_add(clone $t, new \DateInterval('PT' . $l . 'M')));
                    $checkin->setUtcCheckout(date_timezone_set(clone $t, new \DateTimeZone('UTC')));
                    $orm->persist($checkin);
                }
            }
        }
        $orm->flush();

        return new ArrayCollection(self::cleanCheckins($courses));
    }

    /**
     * @param $courses
     * @return array
     */
    public static function cleanCheckins($courses)
    {
        $checkins = [];
        $checkouts = [];
        $resultCheckouts = [];
        foreach($courses as $i => $c) {
            /** @var $c Course */
            if ($c->getType() == 'c') {
                $ch = $c->getCheckins()->toArray();
                foreach ($ch as $j => $checkin) {

                    /** @var $checkin Checkin */

                    // Create a list of valid checkin times for all classes
                    if (empty($checkin->getCheckin())) {
                        continue;
                    }

                    $checkins[$checkin->getUtcCheckin()->getTimestamp()] = $checkin;

                    // add checkout times
                    if (!empty($checkin->getCheckout())) {
                        $length = min(3600, $checkin->getCheckout()->getTimestamp() - $checkin->getCheckin()->getTimestamp());
                    }
                    else {
                        $length = min(time() - $checkin->getCheckin()->getTimestamp(), 3600);
                    }
                    $checkouts[$checkin->getUtcCheckin()->getTimestamp() + $length] = $checkin;
                }
            }
        }

        $times = array_map(function (Checkin $a) {
            return $a->getCheckin()->getTimestamp();
        }, $checkins);
        $times2 = array_map(function (Checkin $a) {
            return $a->getUtcCheckin()->getTimestamp();
        }, $checkins);
        $keys = array_keys($checkins);
        array_multisort($times, SORT_NUMERIC, SORT_DESC, $times2, SORT_NUMERIC, SORT_DESC, $checkins, $keys);
        $checkins = array_combine($keys, $checkins);
        // if the checkin time is before the last checkout time then change the checkout time to match checkin time,
        //    they switched classes in the middle of the session
        $all = $checkins + $checkouts;
        foreach($checkins as $i => $class)
        {
            /** @var Checkin $class */
            if($class->getUtcCheckout() != null && $class->getUtcCheckin() == $class->getUtcCheckout()) {
                $length = $class->getCheckout()->getTimestamp() - $class->getCheckin()->getTimestamp();
            }
            else {
                $diffs = [];
                foreach ($all as $k => $c) {
                    $diffs[$k] = $k - $i;
                }

                asort($diffs);

                foreach ($diffs as $t => $length) {
                    if ($length > 0) {
                        break;
                    }
                }
            }

            if(isset($length)) {
                if ($length < 60) {
                    $length = 60;
                }
                if ($length > 3600) {
                    $length = 3600;
                }

                $resultCheckouts[] = $length;
            }
            else {
                $hit = ';';
            }
        }

        reset($checkouts);
        return [$checkins, $resultCheckouts];
    }

}


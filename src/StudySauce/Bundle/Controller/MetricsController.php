<?php

namespace StudySauce\Bundle\Controller;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use StudySauce\Bundle\Entity\Checkin;
use StudySauce\Bundle\Entity\Course;
use StudySauce\Bundle\Entity\Goal;
use StudySauce\Bundle\Entity\Schedule;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var $userManager UserManager */
        $userManager = $this->get('fos_user.user_manager');

        /** @var $user User */
        if(empty($user))
            $user = $this->getUser();

        /** @var $schedule Schedule */
        $schedule = $user->getSchedules()->first();
        if(!empty($schedule))
            $courses = $schedule->getCourses()->filter(function (Course $b) {return $b->getType() == 'c';})->toArray();

        $isDemo = false;
        if(empty($schedule) || empty($courses)) {
            $isDemo = true;
            $schedule = ScheduleController::getDemoSchedule($userManager, $orm);
            $courses = $schedule->getCourses()->filter(function (Course $b) {return $b->getType() == 'c';})->toArray();
            self::demoCheckins($courses, $orm);
        }

        list($checkins, $checkouts) = self::cleanCheckins($courses);
        list($times, $total) = self::getTimes($checkins, $checkouts, $courses);

        /** @var $goal Goal */
        $goal = $user->getGoals()->filter(function (Goal $x) {return $x->getType() == 'behavior';})->first();
        return $this->render('StudySauceBundle:' . $template[0] . ':' . $template[1] . '.html.php', [
                'hours' => !empty($goal) ? $goal->getGoal() : '',
                'total' => $total,
                'courses' => array_values($courses),
                'checkins' => $checkins,
                'checkouts' => $checkouts,
                'times' => $times,
                'user' => $user,
                'isDemo' => $isDemo
            ]);
    }

    /**
     * @param $_user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function partnerAction($_user)
    {
        /** @var $userManager UserManager */
        $userManager = $this->get('fos_user.user_manager');

        /** @var $user User */
        $user = $userManager->findUserBy(['id' => intval($_user)]);

        return $this->indexAction($user, ['Partner', 'metrics']);
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
            $courses = $schedule->getCourses()->filter(function (Course $b) {return $b->getType() == 'c';})->toArray();
        else
            $courses = [];

        list($checkins, $checkouts) = self::cleanCheckins($courses);
        list($times, $total) = self::getTimes($checkins, $checkouts, $courses);

        /** @var $goal Goal */
        $goal = $user->getGoals()->filter(function (Goal $x) {return $x->getType() == 'behavior';})->first();
        return $this->render('StudySauceBundle:Metrics:widget.html.php', [
                'hours' => !empty($goal) ? $goal->getGoal() : '',
                'total' => $total,
                'courses' => $courses,
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
            list($k) = each($checkouts);

            $length = $k - $t;
            if($length <= 60)
                $length = 60;

            // since we are already in order by time, sum up the other lengths on this day
            $courseId = $c->getCourse()->getId();

            $times[] = [
                'time' => $t,
                'length' => $length,
                'class' => $c->getCourse()->getName(),
                'courseId' => $courseId
            ];

            $total += $length;
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
     * @param $courses
     * @param EntityManager $orm
     */
    public static function demoCheckins($courses, EntityManager $orm)
    {
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
                    $checkin->setUtcCheckin(new \DateTime());
                    $checkin->setCheckout(date_add(clone $t, new \DateInterval('PT' . $l . 'M')));
                    $checkin->setUtcCheckout(new \DateTime());
                    $orm->persist($checkin);
                }
            }
        }
        $orm->flush();
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
                foreach ($c->getCheckins() as $j => $checkin) {

                    /** @var $checkin Checkin */

                    // Create a list of valid checkin times for all classes
                    if (empty($checkin->getCheckin()->getTimestamp())) {
                        continue;
                    }

                    $checkins[$checkin->getCheckin()->getTimestamp()] = $checkin;

                    // add checkout times
                    if (!empty($checkin->getCheckout())) {
                        $checkouts[$checkin->getCheckout()->getTimestamp()] = $checkin;
                    }
                    $checkouts[min(time(), $checkin->getCheckin()->getTimestamp() + 3600)] = $checkin;
                }
            }
        }

        krsort($checkins);

        // if the checkin time is before the last checkout time then change the checkout time to match checkin time,
        //    they switched classes in the middle of the session
        foreach($checkins as $i => $class)
        {
            $diffs = [];
            foreach(($checkins + $checkouts) as $k => $c)
                $diffs[$k] = $k - $i;

            asort($diffs);

            foreach($diffs as $t => $length)
                if($length > 0)
                {
                    if($length < 60)
                        $length = 60;

                    $resultCheckouts[$t] = $length;
                    break;
                }
        }

        reset($checkouts);
            return [$checkins, $resultCheckouts];

    }

}


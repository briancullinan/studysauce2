<?php

namespace StudySauce\Bundle\Controller;

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
        /** @var $user User */
        if(empty($user))
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
        return $this->render('StudySauceBundle:' . $template[0] . ':' . $template[1] . '.html.php', [
                'hours' => !empty($goal) ? $goal->getGoal() : '',
                'total' => $total,
                'courses' => $courses,
                'checkins' => $checkins,
                'checkouts' => $checkouts,
                'times' => $times,
                'user' => $user
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
            $cid = $c->getCourse()->getId();

            $times[] = [
                'time' => $t,
                'length' => $length,
                'class' => $c->getCourse()->getName(),
                'cid' => $cid
            ];

            $total += $length;
        }

        $total = round($total / 3600, 1);

        self::sortByClassThenTime($times, array_map(function (Course $c) {return $c->getId(); }, $courses));

        foreach($times as $i => $t)
        {
            $cid = $t['cid'];
            $date = new \DateTime();
            $date->setTimestamp($t['time']);
            $date->setTime(0, 0, 0);
            $date = $date->add(new \DateInterval('P1D'));
            $g = $date->format('W');
            if(!isset($timeGroups[$g][$cid][0]))
            {
                $length0 = 0;
            }
            else
            {
                $length0 = array_sum($timeGroups[$g][$cid]);
            }
            $timeGroups[$g][$cid][] = $t['length'];
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
                return array_search($x['cid'], array_values($ids));
            }, (array)$times);
        $ts = array_map(function ($x) {
                $x = (array)$x;
                return $x['time'];
            }, (array)$times);
        array_multisort($classes, SORT_NUMERIC, SORT_ASC, $ts, SORT_NUMERIC, SORT_ASC, $times);
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


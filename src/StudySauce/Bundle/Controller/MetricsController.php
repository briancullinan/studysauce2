<?php

namespace StudySauce\Bundle\Controller;

use Doctrine\ORM\EntityManager;
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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
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

        $times = [];
        $timeGroups = [];
        $rows = [];
        $total = 0;
        foreach($checkins as $t => $c)
        {
            /** @var $c Checkin */
            list($k) = each($checkouts);

            $length = $k - $t;
            if($length <= 60)
                $length = 60;

            // since we are already in order by time, sum up the other lengths on this day
            $date = new \DateTime();
            $date->setTimestamp($t);
            $date->setTime(0, 0, 0);
            $date = $date->add(new \DateInterval('P1D'));
            $g = $date->format('W');
            $cid = $c->getCourse()->getId();
            if(!isset($timeGroups[$g][$cid][0]))
            {
                $length0 = 0;
            }
            else
            {
                $length0 = array_sum($timeGroups[$g][$cid]);
            }
            $timeGroups[$g][$cid][] = $length;

            $times[] = [
                'time' => $t,
                'length' => $length,
                'length0' => $length0,
                'class' => $c->getCourse()->getName()
            ];

            $total += $length;
        }
        krsort($rows);
        $rowsOutput = '';
        foreach($rows as $row)
            $rowsOutput .= $row;

        $total = round($total / 3600, 1);

        /** @var $goal Goal */
        $goal = $user->getGoals()->filter(function (Goal $x) {return $x->getType() == 'behavior';})->first();
        return $this->render('StudySauceBundle:Metrics:tab.html.php', [
                'hours' => !empty($goal) ? $goal->getGoal() : '',
                'total' => $total,
                'courses' => $courses,
                'checkins' => $checkins,
                'checkouts' => $checkouts,
                'times' => $times
            ]);
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
                    if ($checkin->getCheckout()->getTimestamp() > 0) {
                        $checkouts[$checkin->getCheckout()->getTimestamp()] = $checkin;
                    }
                    $checkouts[min(time(), $checkin->getCheckin()->getTimestamp() + 3600)] = $checkin;
                }
            }
        }

        ksort($checkins);

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


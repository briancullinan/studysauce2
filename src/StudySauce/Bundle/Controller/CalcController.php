<?php

namespace StudySauce\Bundle\Controller;

use Doctrine\ORM\EntityManager;
use StudySauce\Bundle\Entity\Course;
use StudySauce\Bundle\Entity\Grade;
use StudySauce\Bundle\Entity\Schedule;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ScheduleController
 * @package StudySauce\Bundle\Controller
 */
class CalcController extends Controller
{
    public static $presets = [
        'A +/-' => [
            ['A+',100,97,4],
            ['A',96,93,4.0],
            ['A-',92,90,3.7],
            ['B+',89,87,3.3],
            ['B',86,83,3.0],
            ['B-',82,80,2.7],
            ['C+',79,77,2.3],
            ['C',76,73,2.0],
            ['C-',72,70,1.7],
            ['D+',69,67,1.3],
            ['D',66,63,1.0],
            ['D-',62,60,0.7],
            ['F',59,0,0.0]
        ],
        'A' => [
            ['A',100,90,4.0],
            ['B',89,80,3.0],
            ['C',79,70,2.0],
            ['D',69,60,1.0],
            ['F',59,0,0.0]
        ]
    ];


    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        /** @var User $user */
        $user = $this->getUser();

        $schedules = $user->getSchedules()->toArray();

        return $this->render('StudySauceBundle:Calc:tab.html.php', [
            'schedules' => $schedules,
            'scale' => !empty($schedules) ? reset($schedules)->getGradeScale() : [],
            'termGPA' => !empty($schedules) ? reset($schedules)->getGPA() : null,
            'overallGPA' => $this->getOverallGPA()
        ]);
    }

    /**
     * @param Schedule $s
     * @param $c
     * @param EntityManager $orm
     */
    private function saveCourseGrades(Schedule $s, $c, EntityManager $orm)
    {
        if(empty($c['className']) && empty($c['courseId']))
            return;

        $course = $s->getClasses()->filter(function (Course $x) use($c) {return $x->getId() == $c['courseId'];})->first();
        if(empty($course)) {
            $course = new Course();
            $course->setSchedule($s);
            $course->setName($c['className']);
            $course->setType('c');
            $s->addCourse($course);
            $orm->persist($course);
        }

        /** @var Course $course */
        $course->setCreditHours(intval($c['creditHours']));
        if(empty($c['grades']))
            return;
        foreach($c['grades'] as $g) {
            if(empty($g['gradeId']) && $g['remove'] == 'true')
                continue;
            if(empty($g['gradeId'])) {
                $grade = new Grade();
                $grade->setScore(intval($g['score']));
                $grade->setPercent(intval($g['percent']));
                $grade->setCourse($course);
                $grade->setAssignment($g['assignment']);
                $course->addGrade($grade);
                $orm->persist($grade);
            }
            else {
                $grade = $course->getGrades()->filter(function (Grade $x) use ($g) {return $x->getId() == $g['gradeId'];})->first();
                if(!empty($grade)) {
                    if($g['remove'] == 'true') {
                        $course->removeGrade($grade);
                        $orm->remove($grade);
                    }
                    else {
                        $grade->setScore(intval($g['score']));
                        $grade->setPercent(intval($g['percent']));
                        $grade->setAssignment($g['assignment']);
                        $orm->merge($grade);
                    }
                }
            }
        }

        $orm->flush();
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function updateAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        $first = true;
        foreach($request->get('terms') as $t)
        {
            // find the correct course to modify
            /** @var Schedule $s */
            $s = $user->getSchedules()->filter(function (Schedule $s) use ($t) {return $s->getId() == $t['scheduleId'];})->first();
            if(empty($s)) {
                $s = new Schedule();
                $s->setUser($user);
                $user->addSchedule($s);
                $orm->persist($s);
                $orm->flush();
            }
            // set the scale
            if($first) {
                $s->setGradeScale($request->get('scale'));
                $orm->merge($s);
                $first = false;
            }
            // save the course grades
            foreach($t['courses'] as $c) {
                $this->saveCourseGrades($s, $c, $orm);
            }
        }

        return $this->forward('StudySauceBundle:Calc:index', ['_format' => 'tab']);
    }

    /**
     * @return null|string
     */
    private function getOverallGPA()
    {
        /** @var User $user */
        $user = $this->getUser();

        $hours = 0;
        $score = array_sum($user->getSchedules()->map(function (Schedule $s) use (&$hours) {
            $hours += $s->getCreditHours();
            return $s->getGPA() * $s->getCreditHours();
        })->toArray());
        if(empty($hours))
            return null;
        return number_format($score / $hours, 2);
    }

    /**
     * @param $scale
     * @param $score
     * @return array
     */
    public static function convertToScale($scale, $score)
    {
        if(empty($scale) || !is_array($scale) || count($scale[0]) < 4)
            $scale = self::$presets['A +/-'];
        if($score === null)
            return [null, null];
        foreach($scale as $s) {
            if($score <= $s[1] && $score >= $s[2])
                return [$s[0], $s[3]];
        }
        return [null, null];
    }

}
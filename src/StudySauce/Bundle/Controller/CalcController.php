<?php

namespace StudySauce\Bundle\Controller;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use StudySauce\Bundle\Entity\Course;
use StudySauce\Bundle\Entity\Grade;
use StudySauce\Bundle\Entity\Schedule;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContext;

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
        $scale = !empty($schedules) ? reset($schedules)->getGradeScale() : [];
        if(empty($scale) || !is_array($scale) || count($scale[0]) < 4)
            $scale = CalcController::$presets['A +/-'];

        return $this->render('StudySauceBundle:Calc:tab.html.php', [
            'schedules' => $schedules,
            'scale' => $scale,
            'termGPA' => !empty($schedules) ? reset($schedules)->getGPA() : null,
            'overallGPA' => $this->getOverallGPA()
        ]);
    }

    /**
     * @param Schedule $s
     * @param $c
     * @param EntityManager $orm
     */
    private static function saveCourseGrades(Schedule $s, $c, EntityManager $orm)
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
        $course->setCreditHours(!empty(intval($c['creditHours'])) ? intval($c['creditHours']) : null);
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

        if($course->getGrades()->count() == 0 && empty($course->getName())) {
            $orm->remove($course);
            $s->removeCourse($course);
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
            /** @var Schedule $schedule */
            $schedule = $user->getSchedules()->filter(function (Schedule $s) use ($t) {return $s->getId() == $t['scheduleId'];})->first();
            if(empty($schedule)) {
                $schedule = new Schedule();
                $schedule->setUser($user);
                $user->addSchedule($schedule);
                $orm->persist($schedule);
            }

            // set the scale
            if($first) {
                $schedule->setGradeScale($request->get('scale'));
                $first = false;
            }

            $schedule->setTerm(empty($t['term']) ? null : date_create_from_format('!n/Y', $t['term']));
            $orm->merge($schedule);

            // save the course grades
            if(!empty($t['courses'])) {
                foreach ($t['courses'] as $c) {
                    self::saveCourseGrades($schedule, $c, $orm);
                }
            }

            if($schedule->getCourses()->count() == 0) {
                $orm->remove($schedule);
                $user->removeSchedule($schedule);
            }
        }
        $orm->flush();

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

    public static $examples = ['Exam', 'Paper', 'Essay'];

    /**
     * @return string
     */
    public static function getRandomAssignment()
    {
        return self::$examples[array_rand(self::$examples, 1)];
    }

    /**
     * @param ContainerInterface $container
     */
    public static function getDemoCalculations($container)
    {
        /** @var $orm EntityManager */
        $orm = $container->get('doctrine')->getManager();
        /** @var $userManager UserManager */
        $userManager = $container->get('fos_user.user_manager');
        /** @var SecurityContext $context */
        /** @var TokenInterface $token */
        /** @var User $user */
        /** @var User $guest */
        if(!empty($context = $container->get('security.context')) && !empty($token = $context->getToken()) &&
            !empty($user = $token->getUser()) && $user->hasRole('ROLE_DEMO')) {
            $guest = $user;

        }
        else {
            $guest = $userManager->findUserByUsername('guest');
        }
        $schedules = [];
        $demo = ScheduleController::getDemoSchedule($container);
        $schedules[] = $demo;
        $terms = [11, 6, 5, 1];
        $term = array_rand($terms, 1);
        $demo->setTerm(date_create_from_format('!n/Y', $terms[$term] . '/' . (intval(date('Y')))));

        // add some past schedules
        for($i = 0; $i < 3; $i++)
        {
            $schedule = new Schedule();
            $schedule->setUser($guest);
            $terms = [11, 8, 1, 6];
            $term = array_rand($terms, 1);
            $schedule->setTerm(date_create_from_format('!n/Y', $terms[$term] . '/' . (intval(date('Y')) - $i - 1)));
            $guest->addSchedule($schedule);
            $orm->persist($schedule);
            $orm->flush();
            $schedules[] = $schedule;
        }

        foreach($schedules as $demo)
        {
            /** @var Schedule $demo */
            $courses = $demo->getClasses()->toArray();
            for($k = 0; $k < max(5, count($courses)); $k++)
            {
                /** @var Course $course */
                if(isset($courses[$k]))
                    $course = $courses[$k];
                else
                    unset($course);
                if (isset($course) && $course->getGrades()->count() > 0) {
                    continue;
                }

                $typeCount = rand(1, 4);
                $grades = [];
                $assignmentCount = [];
                for ($i = 0; $i < $typeCount; $i++) {
                    $gradeCount = rand(1, 4);
                    $assignment = self::getRandomAssignment();
                    $assignmentCount[$assignment] = isset($assignmentCount[$assignment])
                        ? $assignmentCount[$assignment]
                        : 0;
                    for ($j = 0; $j < $gradeCount; $j++) {
                        $assignmentCount[$assignment]++;
                        $grades[] = [
                            'remove' => false,
                            'score' => rand(50, 100),
                            'percent' => 100 / $typeCount / $gradeCount,
                            'assignment' => $assignment . ' ' . $assignmentCount[$assignment]
                        ];
                    }
                }
                self::saveCourseGrades(
                    $demo,
                    [
                        'courseId' => isset($course) ? $course->getId() : '',
                        'creditHours' => isset($course) ? count($course->getDotw()) : 3,
                        'className' => isset($course) ? $course->getName() : ScheduleController::getRandomName(),
                            'grades' => $grades
                    ],
                    $orm
                );
            }
        }

        // TODO: add past schedules
    }

    /**
     * @param $scale
     * @param $score
     * @return array
     */
    public static function convertToScale($scale, $score)
    {
        if($score === null)
            return [null, null];
        $score = round($score);
        if(empty($scale) || !is_array($scale) || count($scale[0]) < 4)
            $scale = self::$presets['A +/-'];
        foreach($scale as $s) {
            if($score <= $s[1] && $score >= $s[2])
                return [$s[0], $s[3]];
        }
        return [null, null];
    }

}
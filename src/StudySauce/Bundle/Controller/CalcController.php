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
            'scale_converter' => str_replace('public static', '', $this->_getMethodText(new \ReflectionMethod(get_class($this), 'convertToScale'))),
            'scale' => !empty($schedules) ? reset($schedules)->getGradeScale() : true,
            'termGPA' => !empty($schedules) ? reset($schedules)->getGPA() : null,
            'overallGPA' => $this->getOverallGPA()
        ]);
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

        $schedules = $user->getSchedules()->toArray();

        $first = true;
        foreach($request->get('courses') as $c)
        {
            // find the correct course to modify
            foreach($schedules as $s) {
                /** @var Schedule $s */
                $course = $s->getClasses()->filter(function (Course $x) use($c) {return $x->getId() == $c['courseId'];})->first();
                if(!empty($course)) {
                    // set the scale
                    if($first) {
                        $s->setGradeScale($request->get('scale') == 'true');
                        $orm->merge($s);
                    }
                    $first = false;
                    /** @var Course $course */
                    $course->setCreditHours(intval($c['creditHours']));
                    if(empty($c['grades']))
                        break;
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
                    break;
                }
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
            return $s->getGPA() * $hours;
        })->toArray());
        if(empty($hours))
            return null;
        return number_format($score / $hours, 2);
    }

    /**
     * @param $isExtended
     * @param $score
     * @return array
     */
    public static function convertToScale($isExtended, $score)
    {
        if($score === null)
            return [null, null];
        if($isExtended)
        {
            if($score >= 97) {
                return ['A+', '4.0'];
            }
            else if($score >= 93) {
                return ['A', '4.0'];
            }
            else if($score >= 90) {
                return ['A-', '3.7'];
            }
            else if($score >= 87) {
                return ['B+', '3.3'];
            }
            else if($score >= 83) {
                return ['B', '3.0'];
            }
            else if($score >= 80) {
                return ['B-', '2.7'];
            }
            else if($score >= 77) {
                return ['C+', '2.3'];
            }
            else if($score >= 73) {
                return ['C', '2.0'];
            }
            else if($score >= 70) {
                return ['C-', '1.7'];
            }
            else if($score >= 67) {
                return ['D+', '1.3'];
            }
            else if($score >= 63) {
                return ['D', '1.0'];
            }
            else if($score >= 60) {
                return ['D-', '0.7'];
            }
            else {
                return ['F', '0.0'];
            }
        }
        else
        {
            if($score >= 90) {
                return ['A', '4.0'];
            }
            else if($score >= 80) {
                return ['B', '3.0'];
            }
            else if($score >= 70) {
                return ['C', '2.0'];
            }
            else if($score >= 60) {
                return ['D', '1.0'];
            }
            else {
                return ['F', '0.0'];
            }
        }
    }

    /**
     * @param \ReflectionMethod $m
     * @return string
     */
    private function _getMethodText(\ReflectionMethod $m)
    {
        // check if current method has a reference to the template
        $line_start     = $m->getStartLine() - 1;
        $line_end       = $m->getEndLine();
        $line_count     = $line_end - $line_start;
        $line_array     = file($m->getFileName());
        $methodText = implode("", array_slice($line_array,$line_start,$line_count));
        return $methodText;
    }

}
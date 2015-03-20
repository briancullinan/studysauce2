<?php

namespace Course2\Bundle\Controller;

use Course2\Bundle\Entity\Course2;
use Course2\Bundle\Entity\StudyPlan;
use Doctrine\ORM\EntityManager;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class Course2Controller
 * @package Course2\Bundle\Controller
 */
class StudyPlanController extends Controller
{
    /**
     * @param int $_step
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function wizardAction($_step = 0)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var $user User */
        $user = $this->getUser();

        /** @var Course2 $course */
        $course = $user->getCourse2s()->first();

        if(empty($course)) {
            $course = new Course2();
            $course->setUser($user);
            $orm->persist($course);
            $user->addCourse2($course);
            $orm->flush();
        }
        switch($_step)
        {
            case 0:
                return $this->render('Course2Bundle:StudyPlan:tab.html.php');
                break;
            case 1:
                return $this->render('Course2Bundle:StudyPlan:video.html.php', ['course' => $course]);
                break;
            case 2:
                $csrfToken = $this->has('form.csrf_provider')
                    ? $this->get('form.csrf_provider')->generateCsrfToken('quiz2_update')
                    : null;

                return $this->render('Course2Bundle:StudyPlan:quiz.html.php', [
                        'quiz' => $course->getStudyPlan()->first() ?: new StudyPlan(),
                        'csrf_token' => $csrfToken
                    ]);
                break;
            case 3:
                return $this->render('Course2Bundle:StudyPlan:reward.html.php');
                break;
            case 4:
                return $this->render('Course2Bundle:StudyPlan:investment.html.php');
                break;
            default:
                throw new NotFoundHttpException();
        }

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updateAction(Request $request)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var $user User */
        $user = $this->getUser();

        /** @var Course2 $course */
        $course = $user->getCourse2s()->first();
        // store quiz results
        if(!empty($request->get('multiply')) && !empty($request->get('procrastination')) &&
            !empty($request->get('studySessions')) && !empty($request->get('stickPlan'))) {
            $quiz = new StudyPlan();
            $quiz->setCourse($course);
            $course->addStudyPlan($quiz);
            $quiz->setMultiply($request->get('multiply'));
            $quiz->setProcrastination($request->get('procrastination'));
            $quiz->setStudySessions($request->get('studySessions'));
            $quiz->setStickPlan($request->get('stickPlan'));
            $orm->persist($quiz);
        }
        $orm->flush();

        return $this->forward('Course2Bundle:StudyPlan:wizard', ['_step' => 2, '_format' => 'tab']);
    }
}


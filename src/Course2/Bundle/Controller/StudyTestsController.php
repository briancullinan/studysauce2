<?php

namespace Course2\Bundle\Controller;

use Course2\Bundle\Entity\Course2;
use Course2\Bundle\Entity\StudyTests;
use Doctrine\ORM\EntityManager;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class Course3Controller
 * @package Course2\Bundle\Controller
 */
class StudyTestsController extends Controller
{
    /**
     * @param $_step
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
                return $this->render('Course2Bundle:StudyTests:tab.html.php');
                break;
            case 1:
                return $this->render('Course2Bundle:StudyTests:video.html.php');
                break;
            case 2:
                $csrfToken = $this->has('form.csrf_provider')
                    ? $this->get('form.csrf_provider')->generateCsrfToken('quiz4_update')
                    : null;

                return $this->render('Course2Bundle:StudyTests:quiz.html.php', [
                        'quiz' => $course->getStudyTests()->first() ?: new StudyTests(),
                        'csrf_token' => $csrfToken
                    ]);
                break;
            case 3:
                return $this->render('Course2Bundle:StudyTests:reward.html.php');
                break;
            case 4:
                return $this->render('Course2Bundle:StudyTests:investment.html.php', [
                        'course' => $course
                    ]);
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

        if(!empty($request->get('testTypes'))) {
            $course->setTestTypes($request->get('testTypes'));
            $orm->merge($course);
        }
        if(!empty($request->get('typesTests')) && !empty($request->get('mostImportant')) &&
            !empty($request->get('openTips1')) && !empty($request->get('openTips2'))) {
            // store quiz results
            $quiz = new StudyTests();
            $quiz->setCourse($course);
            $course->addStudyTest($quiz);
            $quiz->setTypesTests(explode(',', $request->get('typesTests')));
            $quiz->setMostImportant($request->get('mostImportant'));
            $quiz->setOpenTips1($request->get('openTips1'));
            $quiz->setOpenTips2($request->get('openTips2'));
            $orm->persist($quiz);
        }
        $orm->flush();

        return $this->forward('Course2Bundle:StudyTests:wizard', ['_step' => 2, '_format' => 'tab']);
    }
}


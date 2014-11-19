<?php

namespace Course1\Bundle\Controller;

use Course1\Bundle\Entity\Course1;
use Course1\Bundle\Entity\Quiz5;
use Doctrine\ORM\EntityManager;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class Course2Controller
 * @package Course1\Bundle\Controller
 */
class EnvironmentController extends Controller
{
    /**
     * @param int $_step
     */
    public function wizardAction($_step = 0)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var $user User */
        $user = $this->getUser();

        /** @var Course1 $course */
        $course = $user->getCourse1s()->first();

        if(empty($course)) {
            $course = new Course1();
            $course->setUser($user);
            $orm->persist($course);
            $user->addCourse1($course);
            $orm->flush();
        }
        switch($_step)
        {
            case 0:
                return $this->render('Course1Bundle:Environment:tab.html.php');
                break;
            case 1:
                return $this->render('Course1Bundle:Environment:video.html.php');
                break;
            case 2:
                $csrfToken = $this->has('form.csrf_provider')
                    ? $this->get('form.csrf_provider')->generateCsrfToken('quiz5_update')
                    : null;

                return $this->render('Course1Bundle:Environment:quiz.html.php', [
                        'quiz' => $course->getQuiz5s()->first() ?: new Quiz5(),
                        'csrf_token' => $csrfToken
                    ]);
                break;
            case 3:
                return $this->render('Course1Bundle:Environment:reward.html.php');
                break;
            case 4:
                return $this->render('Course1Bundle:Environment:investment.html.php');
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

        /** @var Course1 $course */
        $course = $user->getCourse1s()->first();
        // store quiz results
        $quiz = new Quiz5();
        $quiz->setCourse($course);
        $course->addQuiz5($quiz);
        $quiz->setBed($request->get('bed'));
        $quiz->setMozart($request->get('mozart'));
        $quiz->setNature($request->get('nature'));
        $quiz->setBreaks($request->get('breaks'));

        $orm->persist($quiz);
        $orm->flush();

        return $this->forward('Course1Bundle:Environment:wizard', ['_step' => 2, '_format' => 'tab']);
    }
}


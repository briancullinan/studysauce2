<?php

namespace Course2\Bundle\Controller;

use Course2\Bundle\Entity\Course2;
use Course2\Bundle\Entity\StudyMetrics;
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
class StudyMetricsController extends Controller
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
                return $this->render('Course2Bundle:StudyMetrics:tab.html.php');
                break;
            case 1:
                return $this->render('Course2Bundle:StudyMetrics:video.html.php');
                break;
            case 2:
                $csrfToken = $this->has('form.csrf_provider')
                    ? $this->get('form.csrf_provider')->generateCsrfToken('quiz5_update')
                    : null;

                return $this->render('Course2Bundle:StudyMetrics:quiz.html.php', [
                        'quiz' => $course->getStudyMetrics()->first() ?: new StudyMetrics(),
                        'csrf_token' => $csrfToken
                    ]);
                break;
            case 3:
                return $this->render('Course2Bundle:StudyMetrics:reward.html.php');
                break;
            case 4:
                return $this->render('Course2Bundle:StudyMetrics:investment.html.php');
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
        $quiz = new StudyMetrics();
        $quiz->setCourse($course);
        $quiz->setTrackHours(explode(',', $request->get('trackHours')));
        $quiz->setDoingWell($request->get('doingWell'));
        $quiz->setAllTogether($request->get('allTogether'));
        $course->addStudyMetric($quiz);
        $orm->persist($quiz);
        $orm->flush();

        return $this->forward('Course2Bundle:StudyMetrics:wizard', ['_step' => 2, '_format' => 'tab']);
    }
}


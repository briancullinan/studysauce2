<?php

namespace Course3\Bundle\Controller;

use Course3\Bundle\Entity\Course3;
use Course3\Bundle\Entity\ActiveReading;
use Doctrine\ORM\EntityManager;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class Course3Controller
 * @package Course3\Bundle\Controller
 */
class ActiveReadingController extends Controller
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

        /** @var Course3 $course */
        $course = $user->getCourse3s()->first();

        if(empty($course)) {
            $course = new Course3();
            $course->setUser($user);
            $orm->persist($course);
            $user->addCourse3($course);
            $orm->flush();
        }
        switch($_step)
        {
            case 0:
                return $this->render('Course3Bundle:ActiveReading:tab.html.php');
                break;
            case 1:
                return $this->render('Course3Bundle:ActiveReading:video.html.php', ['course' => $course]);
                break;
            case 2:
                $csrfToken = $this->has('form.csrf_provider')
                    ? $this->get('form.csrf_provider')->generateCsrfToken('quiz3_update')
                    : null;

                return $this->render('Course3Bundle:ActiveReading:quiz.html.php', [
                        'quiz' => $course->getActiveReading()->first() ?: new ActiveReading(),
                        'csrf_token' => $csrfToken
                    ]);
                break;
            case 3:
                return $this->render('Course3Bundle:ActiveReading:reward.html.php');
                break;
            case 4:
                return $this->render('Course3Bundle:ActiveReading:investment.html.php');
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

        /** @var Course3 $course */
        $course = $user->getCourse3s()->first();

        // store quiz results
        if(!empty($request->get('whatReading')) && $request->get('selfExplanation') !== null &&
            $request->get('skimming') !== null && $request->get('highlighting') !== null) {
            $quiz = new ActiveReading();
            $quiz->setCourse($course);
            $course->addActiveReading($quiz);
            $quiz->setWhatReading($request->get('whatReading'));
            $quiz->setSelfExplanation($request->get('selfExplanation'));
            $quiz->setSkimming($request->get('skimming'));
            $quiz->setHighlighting($request->get('highlighting'));
            $orm->persist($quiz);
        }
        $orm->flush();

        return $this->forward('Course3Bundle:ActiveReading:wizard', ['_step' => 2, '_format' => 'tab']);
    }
}


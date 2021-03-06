<?php

namespace Course2\Bundle\Controller;

use Course2\Bundle\Entity\Course2;
use Course2\Bundle\Entity\TestTaking;
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
class TestTakingController extends Controller
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
                return $this->render('Course2Bundle:TestTaking:tab.html.php');
                break;
            case 1:
                return $this->render('Course2Bundle:TestTaking:video.html.php', ['course' => $course]);
                break;
            case 2:
                $csrfToken = $this->has('form.csrf_provider')
                    ? $this->get('form.csrf_provider')->generateCsrfToken('quiz3_update')
                    : null;

                return $this->render('Course2Bundle:TestTaking:quiz.html.php', [
                        'quiz' => $course->getTestTaking()->first() ?: new TestTaking(),
                        'csrf_token' => $csrfToken
                    ]);
                break;
            case 3:
                return $this->render('Course2Bundle:TestTaking:reward.html.php');
                break;
            case 4:
                return $this->render('Course2Bundle:TestTaking:investment.html.php');
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
        if(!empty($request->get('ideaCram')) && !empty($request->get('breathing')) &&
            !empty($request->get('skimming'))) {
            $quiz = new TestTaking();
            $quiz->setCourse($course);
            $course->addTestTaking($quiz);
            $quiz->setIdeaCram($request->get('ideaCram'));
            $quiz->setBreathing($request->get('breathing'));
            $quiz->setSkimming($request->get('skimming'));
            $orm->persist($quiz);
        }
        $orm->flush();

        return $this->forward('Course2Bundle:TestTaking:wizard', ['_step' => 2, '_format' => 'tab']);
    }
}


<?php

namespace Course1\Bundle\Controller;

use Course1\Bundle\Entity\Course1;
use Course1\Bundle\Entity\Quiz1;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class Course1Controller
 * @package Course1\Bundle\Controller
 */
class IntroductionController extends Controller
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
                if(empty($user->getProperty('first_time')))
                {
                    /** @var $userManager UserManager */
                    $userManager = $this->get('fos_user.user_manager');
                    $user->setProperty('first_time', true);
                    $userManager->updateUser($user);
                }
                return $this->render('Course1Bundle:Introduction:tab.html.php');
                break;
            case 1:
                return $this->render('Course1Bundle:Introduction:video.html.php');
                break;
            case 2:
                $csrfToken = $this->has('form.csrf_provider')
                    ? $this->get('form.csrf_provider')->generateCsrfToken('quiz1_update')
                    : null;

                return $this->render('Course1Bundle:Introduction:quiz.html.php', [
                        'quiz' => $course->getQuiz1s()->first() ?: new Quiz1(),
                        'csrf_token' => $csrfToken
                    ]);
                break;
            case 3:
                return $this->render('Course1Bundle:Introduction:reward.html.php');
                break;
            case 4:
                return $this->render('Course1Bundle:Introduction:investment.html.php', [
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

        /** @var Course1 $course */
        $course = $user->getCourse1s()->first();

        // store quiz results
        $quiz = new Quiz1();
        $quiz->setCourse($course);
        $course->addQuiz1($quiz);
        if(!empty($request->get('whyStudy')))
            $course->setWhyStudy($request->get('whyStudy'));

        if(!empty($request->get('education')))
            $quiz->setEducation($request->get('education'));

        if(!empty($request->get('mindset')))
            $quiz->setMindset($request->get('mindset'));

        if(!empty($request->get('time')))
            $quiz->setTimeManagement($request->get('time'));

        if(!empty($request->get('devices')))
            $quiz->setDevices($request->get('devices'));

        if(!empty($request->get('study')))
            $quiz->setStudyMuch($request->get('study'));

        $orm->persist($quiz);
        $orm->flush();

        return $this->forward('Course1Bundle:Introduction:wizard', ['_step' => 2, '_format' => 'tab']);
    }
}


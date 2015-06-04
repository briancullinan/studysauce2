<?php

namespace Course1\Bundle\Controller;

use Course1\Bundle\Entity\Course1;
use Course1\Bundle\Entity\Quiz1;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use HWI\Bundle\OAuthBundle\Templating\Helper\OAuthHelper;
use StudySauce\Bundle\Entity\User;
use StudySauce\Bundle\Security\UserProvider;
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
     * @return \Symfony\Component\HttpFoundation\Response
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

                $services = [];
                if(empty($user->getProperty('seen_account_options')) && !$user->hasRole('ROLE_DEMO') &&
                    !$user->hasRole('ROLE_GUEST') && empty($user->getFacebookId()) && empty($user->getGoogleId())) {
                    $userManager = $this->get('fos_user.user_manager');
                    $user->setProperty('seen_account_options', true);
                    $userManager->updateUser($user);

                    // list oauth services
                    /** @var OAuthHelper $oauth */
                    $oauth = $this->get('hwi_oauth.templating.helper.oauth');
                    /** @var UserProvider $provider */
                    $provider =$this->get('my_user_provider');
                    foreach($oauth->getResourceOwners() as $o) {
                        if(!$provider->isConnectible($o))
                            continue;
                        $services[$o] = $oauth->getLoginUrl($o);
                    }
                }

                return $this->render('Course1Bundle:Introduction:tab.html.php', [
                    'services' => $services
                ]);
                break;
            case 1:
                return $this->render('Course1Bundle:Introduction:video.html.php', ['course' => $course]);
                break;
            case 2:
                $csrfToken = $this->has('form.csrf_provider')
                    ? $this->get('form.csrf_provider')->generateCsrfToken('quiz1_update')
                    : null;

                return $this->render('Course1Bundle:Introduction:quiz.html.php', [
                        'quiz' => $course->getQuiz1()->first() ?: new Quiz1(),
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

        if(!empty($request->get('whyStudy'))) {
            $course->setWhyStudy($request->get('whyStudy'));
            $orm->merge($course);
        }

        if(!empty($request->get('education')) && !empty($request->get('mindset')) && !empty($request->get('time')) &&
            !empty($request->get('devices')) && !empty($request->get('study'))) {

            // store quiz results
            $quiz = new Quiz1();
            $quiz->setCourse($course);
            $course->addQuiz1($quiz);
            $quiz->setEducation($request->get('education'));
            $quiz->setMindset($request->get('mindset'));
            $quiz->setTimeManagement($request->get('time'));
            $quiz->setDevices($request->get('devices'));
            $quiz->setStudyMuch($request->get('study'));
            $orm->persist($quiz);
        }
        $orm->flush();

        return $this->forward('Course1Bundle:Introduction:wizard', ['_step' => 2, '_format' => 'tab']);
    }
}


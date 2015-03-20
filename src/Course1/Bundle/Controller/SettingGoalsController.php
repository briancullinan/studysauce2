<?php

namespace Course1\Bundle\Controller;

use Course1\Bundle\Entity\Course1;
use Course1\Bundle\Entity\Quiz2;
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
class SettingGoalsController extends Controller
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
                return $this->render('Course1Bundle:SettingGoals:tab.html.php');
                break;
            case 1:
                return $this->render('Course1Bundle:SettingGoals:video.html.php', ['course' => $course]);
                break;
            case 2:
                $csrfToken = $this->has('form.csrf_provider')
                    ? $this->get('form.csrf_provider')->generateCsrfToken('quiz2_update')
                    : null;

                return $this->render('Course1Bundle:SettingGoals:quiz.html.php', [
                        'quiz' => $course->getQuiz2s()->first() ?: new Quiz2(),
                        'csrf_token' => $csrfToken
                    ]);
                break;
            case 3:
                return $this->render('Course1Bundle:SettingGoals:reward.html.php');
                break;
            case 4:
                return $this->render('Course1Bundle:SettingGoals:investment.html.php');
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
        if(!empty($request->get('performance')) && !empty($request->get('acronymS')) &&
            !empty($request->get('acronymM')) && !empty($request->get('acronymA')) &&
            !empty($request->get('acronymR')) && !empty($request->get('acronymT')) &&
            !empty($request->get('motivationI')) && !empty($request->get('motivationE'))) {
            $quiz = new Quiz2();
            $quiz->setCourse($course);
            $course->addQuiz2($quiz);
            $quiz->setGoalPerformance($request->get('performance'));
            $quiz->setSpecific($request->get('acronymS'));
            $quiz->setMeasurable($request->get('acronymM'));
            $quiz->setAchievable($request->get('acronymA'));
            $quiz->setRelevant($request->get('acronymR'));
            $quiz->setTimeBound($request->get('acronymT'));
            $quiz->setIntrinsic($request->get('motivationI'));
            $quiz->setExtrinsic($request->get('motivationE'));
            $orm->persist($quiz);
        }
        $orm->flush();

        return $this->forward('Course1Bundle:SettingGoals:wizard', ['_step' => 2, '_format' => 'tab']);
    }
}


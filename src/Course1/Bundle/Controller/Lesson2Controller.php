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
class Lesson2Controller extends Controller
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
                return $this->render('Course1Bundle:Lesson2:tab.html.php');
                break;
            case 1:
                return $this->render('Course1Bundle:Lesson2:video.html.php');
                break;
            case 2:
                $csrfToken = $this->has('form.csrf_provider')
                    ? $this->get('form.csrf_provider')->generateCsrfToken('quiz2_update')
                    : null;

                return $this->render('Course1Bundle:Lesson2:quiz.html.php', [
                        'quiz' => $course->getQuiz2s()->first() ?: new Quiz2(),
                        'csrf_token' => $csrfToken
                    ]);
                break;
            case 3:
                return $this->render('Course1Bundle:Lesson2:reward.html.php');
                break;
            case 4:
                return $this->render('Course1Bundle:Lesson2:investment.html.php');
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

        // store quiz results
        $quiz = new Quiz2();
        $quiz->setCourse($user->getCourse1s()->first());
        if(!empty($request->get('performance')))
            $quiz->setGoalPerformance($request->get('performance'));

        if(!empty($request->get('acronymS')))
            $quiz->setSpecific($request->get('acronymS'));

        if(!empty($request->get('acronymM')))
            $quiz->setMeasurable($request->get('acronymM'));

        if(!empty($request->get('acronymA')))
            $quiz->setAchievable($request->get('acronymA'));

        if(!empty($request->get('acronymR')))
            $quiz->setRelevant($request->get('acronymR'));

        if(!empty($request->get('acronymT')))
            $quiz->setTimeBound($request->get('acronymT'));

        if(!empty($request->get('motivationI')))
            $quiz->setIntrinsic($request->get('motivationI'));

        if(!empty($request->get('motivationE')))
            $quiz->setExtrinsic($request->get('motivationE'));

        $orm->persist($quiz);
        $orm->flush();

        return $this->forward('Course1Bundle:Lesson2:wizard', ['_step' => 2, '_format' => 'tab']);
    }
}


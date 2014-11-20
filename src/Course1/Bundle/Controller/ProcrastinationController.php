<?php

namespace Course1\Bundle\Controller;

use Course1\Bundle\Entity\Course1;
use Course1\Bundle\Entity\Quiz3;
use Doctrine\ORM\EntityManager;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class Course3Controller
 * @package Course1\Bundle\Controller
 */
class ProcrastinationController extends Controller
{
    /**
     * @param $_step
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
                return $this->render('Course1Bundle:Procrastination:tab.html.php');
                break;
            case 1:
                return $this->render('Course1Bundle:Procrastination:video.html.php');
                break;
            case 2:
                $csrfToken = $this->has('form.csrf_provider')
                    ? $this->get('form.csrf_provider')->generateCsrfToken('quiz3_update')
                    : null;

                return $this->render('Course1Bundle:Procrastination:quiz.html.php', [
                        'quiz' => $course->getQuiz3s()->first() ?: new Quiz3(),
                        'csrf_token' => $csrfToken
                    ]);
                break;
            case 3:
                return $this->render('Course1Bundle:Procrastination:reward.html.php');
                break;
            case 4:
                return $this->render('Course1Bundle:Procrastination:investment.html.php');
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
        $quiz = new Quiz3();
        $quiz->setCourse($course);
        $course->addQuiz3($quiz);
        if(!empty($request->get('memoryA')))
            $quiz->setActiveMemory($request->get('memoryA'));

        if(!empty($request->get('memoryR')))
            $quiz->setReferenceMemory($request->get('memoryR'));

        if(!empty($request->get('studyGoal')))
            $quiz->setStudyGoal($request->get('studyGoal'));

        if(!empty($request->get('procrastinating')))
            $quiz->setProcrastinating($request->get('procrastinating'));

        if(!empty($request->get('procrastinationD')))
            $quiz->setDeadlines($request->get('procrastinationD'));

        if(!empty($request->get('procrastinationP')))
            $quiz->setPlan($request->get('procrastinationP'));

        $orm->persist($quiz);
        $orm->flush();

        return $this->forward('Course1Bundle:Procrastination:wizard', ['_step' => 2, '_format' => 'tab']);
    }
}

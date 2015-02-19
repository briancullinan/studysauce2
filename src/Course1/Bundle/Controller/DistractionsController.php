<?php

namespace Course1\Bundle\Controller;

use Course1\Bundle\Entity\Course1;
use Course1\Bundle\Entity\Quiz4;
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
class DistractionsController extends Controller
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
                return $this->render('Course1Bundle:Distractions:tab.html.php');
                break;
            case 1:
                return $this->render('Course1Bundle:Distractions:video.html.php');
                break;
            case 2:
                $csrfToken = $this->has('form.csrf_provider')
                    ? $this->get('form.csrf_provider')->generateCsrfToken('quiz4_update')
                    : null;

                return $this->render('Course1Bundle:Distractions:quiz.html.php', [
                        'quiz' => $course->getQuiz4s()->first() ?: new Quiz4(),
                        'csrf_token' => $csrfToken
                    ]);
                break;
            case 3:
                return $this->render('Course1Bundle:Distractions:reward.html.php');
                break;
            case 4:
                return $this->render('Course1Bundle:Distractions:investment.html.php');
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
        if(!empty($request->get('multitask')) && !empty($request->get('downside')) &&
            !empty($request->get('lowerScore')) && !empty($request->get('distraction'))) {
            $quiz = new Quiz4();
            $quiz->setCourse($course);
            $course->addQuiz4($quiz);
            $quiz->setMultitask($request->get('multitask'));
            $quiz->setDownside($request->get('downside'));
            $quiz->setLowerScore($request->get('lowerScore'));
            $quiz->setDistraction($request->get('distraction'));
            $orm->persist($quiz);
        }
        $orm->flush();

        return $this->forward('Course1Bundle:Distractions:wizard', ['_step' => 2, '_format' => 'tab']);
    }
}


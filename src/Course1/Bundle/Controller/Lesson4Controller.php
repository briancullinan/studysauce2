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
class Lesson4Controller extends Controller
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
                return $this->render('Course1Bundle:Lesson4:tab.html.php');
                break;
            case 1:
                return $this->render('Course1Bundle:Lesson4:video.html.php');
                break;
            case 2:
                $csrfToken = $this->has('form.csrf_provider')
                    ? $this->get('form.csrf_provider')->generateCsrfToken('quiz4_update')
                    : null;

                return $this->render('Course1Bundle:Lesson4:quiz.html.php', [
                        'quiz' => $course->getQuiz4s()->first() ?: new Quiz4(),
                        'csrf_token' => $csrfToken
                    ]);
                break;
            case 3:
                return $this->render('Course1Bundle:Lesson4:reward.html.php');
                break;
            case 4:
                return $this->render('Course1Bundle:Lesson4:investment.html.php');
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
        $quiz = new Quiz4();
        $quiz->setCourse($course);
        $course->addQuiz4($quiz);
        if(!empty($request->get('multitask')))
            $quiz->setMultitask($request->get('multitask'));

        if(!empty($request->get('downside')))
            $quiz->setDownside($request->get('downside'));

        if(!empty($request->get('lowerScore')))
            $quiz->setLowerScore($request->get('lowerScore'));

        if(!empty($request->get('distraction')))
            $quiz->setDistraction($request->get('distraction'));

        $orm->persist($quiz);
        $orm->flush();

        return $this->forward('Course1Bundle:Lesson4:wizard', ['_step' => 2, '_format' => 'tab']);
    }
}


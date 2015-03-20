<?php

namespace Course2\Bundle\Controller;

use Course2\Bundle\Entity\Course2;
use Course2\Bundle\Entity\Interleaving;
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
class InterleavingController extends Controller
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
                return $this->render('Course2Bundle:Interleaving:tab.html.php');
                break;
            case 1:
                return $this->render('Course2Bundle:Interleaving:video.html.php', ['course' => $course]);
                break;
            case 2:
                $csrfToken = $this->has('form.csrf_provider')
                    ? $this->get('form.csrf_provider')->generateCsrfToken('quiz1_update')
                    : null;

                return $this->render('Course2Bundle:Interleaving:quiz.html.php', [
                        'quiz' => $course->getInterleaving()->first() ?: new Interleaving(),
                        'csrf_token' => $csrfToken
                    ]);
                break;
            case 3:
                return $this->render('Course2Bundle:Interleaving:reward.html.php');
                break;
            case 4:
                return $this->render('Course2Bundle:Interleaving:investment.html.php', [
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

        /** @var Course2 $course */
        $course = $user->getCourse2s()->first();

        if(!empty($request->get('multipleSessions')) && !empty($request->get('otherName')) &&
            !empty($request->get('typesCourses'))) {
            $quiz = new Interleaving();
            $quiz->setCourse($course);
            $quiz->setMultipleSessions($request->get('multipleSessions'));
            $quiz->setOtherName($request->get('otherName'));
            $quiz->setTypesCourses($request->get('typesCourses'));
            $course->addInterleaving($quiz);
            $orm->persist($quiz);
        }
        $orm->flush();

        return $this->forward('Course2Bundle:Interleaving:wizard', ['_step' => 2, '_format' => 'tab']);
    }
}


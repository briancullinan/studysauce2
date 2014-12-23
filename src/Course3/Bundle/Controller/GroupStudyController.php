<?php

namespace Course3\Bundle\Controller;

use Course3\Bundle\Entity\Course3;
use Course3\Bundle\Entity\GroupStudy;
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
class GroupStudyController extends Controller
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
                return $this->render('Course3Bundle:GroupStudy:tab.html.php');
                break;
            case 1:
                return $this->render('Course3Bundle:GroupStudy:video.html.php');
                break;
            case 2:
                $csrfToken = $this->has('form.csrf_provider')
                    ? $this->get('form.csrf_provider')->generateCsrfToken('quiz3_update')
                    : null;

                return $this->render('Course3Bundle:GroupStudy:quiz.html.php', [
                        'quiz' => $course->getGroupStudy()->first() ?: new GroupStudy(),
                        'csrf_token' => $csrfToken
                    ]);
                break;
            case 3:
                return $this->render('Course3Bundle:GroupStudy:reward.html.php');
                break;
            case 4:
                return $this->render('Course3Bundle:GroupStudy:investment.html.php', [
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

        /** @var Course3 $course */
        $course = $user->getCourse3s()->first();
        if(!empty($request->get('groupGoals'))) {
            $course->setGroupGoals($request->get('groupGoals'));
            $orm->merge($course);
        }
        else {
            // store quiz results
            $quiz = new GroupStudy();
            $quiz->setCourse($course);
            $course->addGroupStudy($quiz);
            $quiz->setBadTimes(explode(',', $request->get('badTimes')));
            $quiz->setBuilding($request->get('building'));
            $quiz->setGroupRole($request->get('groupRole'));
            $quiz->setGroupBreaks($request->get('groupBreaks'));
            $orm->persist($quiz);
        }
        $orm->flush();

        return $this->forward('Course3Bundle:GroupStudy:wizard', ['_step' => 2, '_format' => 'tab']);
    }
}


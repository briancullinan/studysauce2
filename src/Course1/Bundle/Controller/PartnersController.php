<?php

namespace Course1\Bundle\Controller;

use Course1\Bundle\Entity\Course1;
use Course1\Bundle\Entity\Quiz6;
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
class PartnersController extends Controller
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
                return $this->render('Course1Bundle:Partners:tab.html.php');
                break;
            case 1:
                return $this->render('Course1Bundle:Partners:video.html.php', ['course' => $course]);
                break;
            case 2:
                $csrfToken = $this->has('form.csrf_provider')
                    ? $this->get('form.csrf_provider')->generateCsrfToken('quiz2_update')
                    : null;

                return $this->render('Course1Bundle:Partners:quiz.html.php', [
                        'quiz' => $course->getQuiz6s()->first() ?: new Quiz6(),
                        'csrf_token' => $csrfToken
                    ]);
                break;
            case 3:
                return $this->render('Course1Bundle:Partners:reward.html.php');
                break;
            case 4:
                return $this->render('Course1Bundle:Partners:investment.html.php');
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
        if(!empty($request->get('help')) && !empty($request->get('attribute')) &&
            !empty($request->get('often')) && !empty($request->get('usage'))) {
            $quiz = new Quiz6();
            $quiz->setCourse($course);
            $course->addQuiz6($quiz);
            $quiz->setHelp(explode(',', $request->get('help')));
            $quiz->setAttribute($request->get('attribute'));
            $quiz->setOften($request->get('often'));
            $quiz->setUsage(explode(',', $request->get('usage')));
            $orm->persist($quiz);
        }
        $orm->flush();

        return $this->forward('Course1Bundle:Partners:wizard', ['_step' => 2, '_format' => 'tab']);
    }
}


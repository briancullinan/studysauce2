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
class UpgradeController extends Controller
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
                return $this->render('Course1Bundle:Upgrade:tab.html.php');
                break;
            case 1:
                return $this->render('Course1Bundle:Upgrade:video.html.php');
                break;
            case 2:
                $csrfToken = $this->has('form.csrf_provider')
                    ? $this->get('form.csrf_provider')->generateCsrfToken('quiz2_update')
                    : null;

                return $this->render('Course1Bundle:Upgrade:quiz.html.php', [
                        'course' => $course,
                        'csrf_token' => $csrfToken
                    ]);
                break;
            case 3:
                return $this->render('Course1Bundle:Upgrade:reward.html.php');
                break;
            case 4:
                return $this->render('Course1Bundle:Upgrade:investment.html.php');
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
        $course->setEnjoyed($request->get('enjoy'));

        $orm->merge($course);
        $orm->flush();

        return $this->forward('Course1Bundle:Upgrade:wizard', ['_step' => 2, '_format' => 'tab']);
    }
}


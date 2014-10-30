<?php

namespace Course1\Bundle\Controller;

use Course1\Bundle\Entity\Course1;
use Doctrine\ORM\EntityManager;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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

        if(empty($user->getCourse1s()->first())) {
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
                return $this->render('Course1Bundle:Lesson4:quiz.html.php');
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
}


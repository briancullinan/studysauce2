<?php

namespace StudySauce\Bundle\Controller;

use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class CourseController
 * @package StudySauce\Bundle\Controller
 */
class CourseController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('StudySauceBundle:Course:tab.html.php');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function menuAction()
    {
        /** @var User $user */
        $user = $this->getUser();
        $course = $user->getCourse1s()->first();
        return $this->render('Course1Bundle:Shared:menu.html.php', [
                'course' => $course
            ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function widgetAction()
    {
        /** @var User $user */
        $user = $this->getUser();
        $course = $user->getCourse1s()->first();
        return $this->render('StudySauceBundle:Course:widget.html.php', [
                'course' => $course
            ]);
    }
}


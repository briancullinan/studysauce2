<?php

namespace StudySauce\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class CourseController
 * @package StudySauce\Bundle\Controller
 */
class CourseController extends Controller
{
    /**
     * @param string $_format
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($_format = 'index')
    {
        //return $this->forward('Course1Bundle:Course1:index');
        return $this->render('StudySauceBundle:Course:tab.html.php');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function menuAction()
    {
        return $this->render('Course1Bundle:Shared:menu.html.php');
    }
}


<?php

namespace StudySauce\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CourseController extends Controller
{
    public function indexAction()
    {
        //return $this->forward('Course1Bundle:Course1:index');
        return $this->render('StudySauceBundle:Course:index.html.php');
    }

    public function menuAction()
    {
        return $this->render('Course1Bundle:Shared:menu.html.php');
    }
}


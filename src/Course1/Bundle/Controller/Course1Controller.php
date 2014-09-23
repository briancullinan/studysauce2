<?php

namespace Course1\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class Course1Controller extends Controller
{
    public function indexAction()
    {
        return $this->render('Course1Bundle:Course1:index.html.php');
    }

    public function wizardAction($format, $step)
    {
        return $this->render('Course1Bundle:Course1:index.html.php');
    }
}


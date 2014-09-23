<?php

namespace StudySauce\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DeadlinesController extends Controller
{
    public function indexAction()
    {
        return $this->render('StudySauceBundle:Deadlines:index.html.php');
    }
}


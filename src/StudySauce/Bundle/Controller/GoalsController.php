<?php

namespace StudySauce\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class GoalsController extends Controller
{
    public function indexAction()
    {
        return $this->render('StudySauceBundle:Goals:index.html.php');
    }
}


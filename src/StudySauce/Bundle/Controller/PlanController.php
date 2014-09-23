<?php

namespace StudySauce\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PlanController extends Controller
{
    public function indexAction()
    {
        return $this->render('StudySauceBundle:Plan:index.html.php');
    }
}


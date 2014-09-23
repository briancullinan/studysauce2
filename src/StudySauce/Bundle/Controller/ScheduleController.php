<?php

namespace StudySauce\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ScheduleController extends Controller
{
    public function indexAction()
    {
        return $this->render('StudySauceBundle:Schedule:index.html.php');
    }
}
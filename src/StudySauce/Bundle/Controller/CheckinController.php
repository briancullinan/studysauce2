<?php

namespace StudySauce\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CheckinController extends Controller
{
    public function indexAction()
    {
        return $this->render('StudySauceBundle:Checkin:index.html.php');
    }
}


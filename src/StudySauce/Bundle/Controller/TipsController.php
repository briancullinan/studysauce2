<?php

namespace StudySauce\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TipsController extends Controller
{
    public function indexAction()
    {
        return $this->render('StudySauceBundle:Tips:index.html.php');
    }
}


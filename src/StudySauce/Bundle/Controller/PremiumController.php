<?php

namespace StudySauce\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PremiumController extends Controller
{
    public function indexAction()
    {
        return $this->render('StudySauceBundle:Premium:index.html.php');
    }
}


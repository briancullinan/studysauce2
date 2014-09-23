<?php

namespace StudySauce\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PartnerController extends Controller
{
    public function indexAction()
    {
        return $this->render('StudySauceBundle:Partner:index.html.php');
    }
}


<?php

namespace StudySauce\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MetricsController extends Controller
{
    public function indexAction()
    {
        return $this->render('StudySauceBundle:Metrics:index.html.php');
    }
}


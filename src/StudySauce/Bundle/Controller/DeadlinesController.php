<?php

namespace StudySauce\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DeadlinesController extends Controller
{
    public function indexAction($_format = 'index')
    {
        if($_format == 'tab')
            return $this->render('StudySauceBundle:Deadlines:tab.html.php');
        return $this->render('StudySauceBundle:Deadlines:index.html.php');
    }
}


<?php

namespace StudySauce\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DialogsController extends Controller
{
    public function contactAction()
    {
        return $this->render('StudySauceBundle:Dialogs:contact.html.php');
    }

    public function buildingAction()
    {
        return $this->render('StudySauceBundle:Dialogs:building.html.php');
    }

    public function achievementAction()
    {
        return $this->render('StudySauceBundle:Dialogs:achievement.html.php');
    }

    public function partnerinviteAction()
    {
        return $this->render('StudySauceBundle:Dialogs:partnerinvite.html.php');
    }
}
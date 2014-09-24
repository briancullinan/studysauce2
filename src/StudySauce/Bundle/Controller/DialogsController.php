<?php

namespace StudySauce\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DialogsController extends Controller
{
    public function contactAction($id = '')
    {
        return $this->render('StudySauceBundle:Dialogs:contact.html.php', array('id' => $id));
    }

    public function buildingAction($id = '')
    {
        return $this->render('StudySauceBundle:Dialogs:building.html.php', array('id' => $id));
    }

    public function achievementAction($id = '')
    {
        return $this->render('StudySauceBundle:Dialogs:achievement.html.php', array('id' => $id));
    }

    public function partnerinviteAction($id = '')
    {
        return $this->render('StudySauceBundle:Dialogs:partnerinvite.html.php', array('id' => $id));
    }
}
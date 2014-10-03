<?php

namespace StudySauce\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class DialogsController
 * @package StudySauce\Bundle\Controller
 */
class DialogsController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function contactAction()
    {
        return $this->render('StudySauceBundle:Dialogs:contact.html.php', ['id' => 'contact-support']);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buildingAction()
    {
        return $this->render('StudySauceBundle:Dialogs:building.html.php', ['id' => 'building']);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function achievementAction()
    {
        return $this->render('StudySauceBundle:Dialogs:achievement.html.php', ['id' => 'claim']);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function partnerinviteAction()
    {
        return $this->render('StudySauceBundle:Dialogs:partnerinvite.html.php', ['id' => 'invite-sent']);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function checkinemptyAction()
    {
        return $this->render('StudySauceBundle:Dialogs:checkin-empty.html.php', ['id' => 'checkin-empty']);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function metricsemptyAction()
    {
        return $this->render('StudySauceBundle:Dialogs:metrics-empty.html.php', ['id' => 'metrics-empty']);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function planintro1Action()
    {
        return $this->render('StudySauceBundle:Dialogs:plan-intro-1.html.php', ['id' => 'plan-intro-1']);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function planintro2Action()
    {
        return $this->render('StudySauceBundle:Dialogs:plan-intro-2.html.php', ['id' => 'plan-intro-2']);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function planintro3Action()
    {
        return $this->render('StudySauceBundle:Dialogs:plan-intro-3.html.php', ['id' => 'plan-intro-3']);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function planintro4Action()
    {
        return $this->render('StudySauceBundle:Dialogs:plan-intro-4.html.php', ['id' => 'plan-intro-4']);
    }
}
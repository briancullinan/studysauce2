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
    public function partnerInviteAction()
    {
        return $this->render('StudySauceBundle:Dialogs:partner-invite.html.php', ['id' => 'invite-sent']);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function checkinEmptyAction()
    {
        return $this->render('StudySauceBundle:Dialogs:checkin-empty.html.php', ['id' => 'checkin-empty']);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function sdsMessagesAction()
    {
        return $this->render('StudySauceBundle:Dialogs:sds-messages.html.php', ['id' => 'sds-messages']);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function timerExpireAction()
    {
        return $this->render('StudySauceBundle:Dialogs:timer-expire.html.php', ['id' => 'timer-expire']);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function mozartAction()
    {
        return $this->render('StudySauceBundle:Dialogs:mozart.html.php', ['id' => 'mozart-effect']);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function checklistAction()
    {
        return $this->render('StudySauceBundle:Dialogs:checklist.html.php', ['id' => 'checklist']);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function metricsEmptyAction()
    {
        return $this->render('StudySauceBundle:Dialogs:metrics-empty.html.php', ['id' => 'metrics-empty']);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function planIntro1Action()
    {
        return $this->render('StudySauceBundle:Dialogs:plan-intro-1.html.php', ['id' => 'plan-intro-1']);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function planIntro2Action()
    {
        return $this->render('StudySauceBundle:Dialogs:plan-intro-2.html.php', ['id' => 'plan-intro-2']);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function planIntro3Action()
    {
        return $this->render('StudySauceBundle:Dialogs:plan-intro-3.html.php', ['id' => 'plan-intro-3']);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function planIntro4Action()
    {
        return $this->render('StudySauceBundle:Dialogs:plan-intro-4.html.php', ['id' => 'plan-intro-4']);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function scheduleDemoAction()
    {
        return $this->render('StudySauceBundle:Dialogs:schedule-demo.html.php', ['id' => 'schedule-demo']);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function billParents1Action()
    {
        return $this->render('StudySauceBundle:Dialogs:bill-parents.html.php', ['id' => 'bill-parents']);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function billParents2Action()
    {
        return $this->render('StudySauceBundle:Dialogs:bill-parents-confirm.html.php', ['id' => 'bill-parents-confirm']);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ccvInfoAction()
    {
        return $this->render('StudySauceBundle:Dialogs:ccv-info.html.php', ['id' => 'ccv-info']);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function partnerAdvice1Action()
    {
        return $this->render('StudySauceBundle:Dialogs:partner-advice-1.html.php', ['id' => 'partner-advice-1']);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function partnerAdvice2Action()
    {
        return $this->render('StudySauceBundle:Dialogs:partner-advice-2.html.php', ['id' => 'partner-advice-2']);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function partnerAdvice3Action()
    {
        return $this->render('StudySauceBundle:Dialogs:partner-advice-3.html.php', ['id' => 'partner-advice-3']);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function partnerAdvice4Action()
    {
        return $this->render('StudySauceBundle:Dialogs:partner-advice-4.html.php', ['id' => 'partner-advice-4']);
    }
}
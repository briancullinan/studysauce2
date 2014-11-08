<?php

namespace StudySauce\Bundle\Controller;

use StudySauce\Bundle\Entity\ContactMessage;
use StudySauce\Bundle\Entity\Course;
use StudySauce\Bundle\Entity\ParentInvite;
use StudySauce\Bundle\Entity\Schedule;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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
    public function cancelConfirmAction()
    {
        return $this->render('StudySauceBundle:Dialogs:cancel-confirm.html.php', ['id' => 'cancel-confirm']);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function contactSendAction(Request $request)
    {
        /** @var $user \StudySauce\Bundle\Entity\User */
        $user = $this->getUser();

        // save the invite
        $contact = new ContactMessage();
        $contact->setUser($user == 'anon.' || $user->hasRole('ROLE_GUEST') ? null : $user);
        $contact->setName($request->get('name'));
        $contact->setEmail($request->get('email'));
        $contact->setMessage($request->get('message'));

        $email = new EmailsController();
        $email->setContainer($this->container);
        $email->administratorAction($user, $contact);

        return new JsonResponse(true);
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
    public function partnerConfirmAction()
    {
        return $this->render('StudySauceBundle:Dialogs:partner-confirm.html.php', ['id' => 'partner-confirm']);
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
        // get the total number of checkins

        /** @var $user \StudySauce\Bundle\Entity\User */
        $user = $this->getUser();

        /** @var $schedule Schedule */
        $schedule = $user->getSchedules()->first() ?: new Schedule();

        $courses = $schedule->getCourses()->filter(function (Course $b) {return $b->getType() == 'c';})->toArray();
        $count = 0;
        foreach($courses as $i => $c)
        {
            /** @var Course $c */
            $count += $c->getCheckins()->count();
        }
        return $this->render('StudySauceBundle:Dialogs:sds-messages.html.php', ['id' => 'sds-messages', 'count' => $count]);
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
    public function planEmptyAction()
    {
        return $this->render('StudySauceBundle:Dialogs:plan-empty.html.php', ['id' => 'plan-empty']);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function planUpgradeAction()
    {
        return $this->render('StudySauceBundle:Dialogs:plan-upgrade.html.php', ['id' => 'plan-upgrade']);
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
     * @param Request $request
     * @return JsonResponse
     */
    public function billParentsSendAction(Request $request)
    {
        /** @var $user \StudySauce\Bundle\Entity\User */
        $user = $this->getUser();

        // save the invite
        $bill = new ParentInvite();
        $bill->setUser($user);
        $bill->setFirst($request->get('first'));
        $bill->setLast($request->get('last'));
        $bill->setEmail($request->get('email'));
        $bill->setCode(md5(microtime(true)));

        $email = new EmailsController();
        $email->setContainer($this->container);
        $email->parentPayAction($user, $bill);

        return new JsonResponse(true);
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
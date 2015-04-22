<?php

namespace StudySauce\Bundle\Controller;

use Doctrine\ORM\EntityManager;
use StudySauce\Bundle\Entity\ContactMessage;
use StudySauce\Bundle\Entity\Course;
use StudySauce\Bundle\Entity\ParentInvite;
use StudySauce\Bundle\Entity\Schedule;
use StudySauce\Bundle\Entity\StudentInvite;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use TorchAndLaurel\Bundle\Controller\EmailsController as TorchEmailsController;

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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function bookmarkAction()
    {
        return $this->render('StudySauceBundle:Dialogs:bookmark.html.php', ['id' => 'bookmark']);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function contactSendAction(Request $request)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var $user \StudySauce\Bundle\Entity\User */
        $user = $this->getUser();

        // save the invite
        $contact = new ContactMessage();
        if($user != 'anon.' && !$user->hasRole('ROLE_GUEST') && !$user->hasRole('ROLE_DEMO')) {
            $contact->setUser($user);
        }
        $contact->setName($request->get('name'));
        $contact->setEmail($request->get('email'));
        $contact->setMessage(str_replace(["\n"], ['<br />'], $request->get('message')));
        $orm->persist($contact);
        $orm->flush();

        $email = new EmailsController();
        $email->setContainer($this->container);
        $email->contactMessageAction($user, $contact);

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
    public function errorAction()
    {
        return $this->render('StudySauceBundle:Dialogs:error-dialog.html.php', ['id' => 'error-dialog']);
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
    public function deadlinesEmptyAction()
    {
        return $this->render('StudySauceBundle:Dialogs:deadlines-empty.html.php', ['id' => 'deadlines-empty']);
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

        $count = array_sum($schedule->getClasses()->map(function (Course $c) {return $c->getCheckins()->count();})->toArray());
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
        return $this->render('StudySauceBundle:Dialogs:plan-empty.html.php', ['id' => 'plan-empty', 'attributes' => 'data-backdrop="false"']);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function planEmptyScheduleAction()
    {
        return $this->render('StudySauceBundle:Dialogs:plan-empty-schedule.html.php', ['id' => 'plan-empty-schedule']);
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
    public function profileUpgradeAction()
    {
        return $this->render('StudySauceBundle:Dialogs:profile-upgrade.html.php', ['id' => 'profile-upgrade']);
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
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        /** @var $user \StudySauce\Bundle\Entity\User */
        $user = $this->getUser();
        $session = $request->getSession();

        // save the invite
        $bill = new ParentInvite();
        $bill->setUser($user);
        $bill->setFirst($request->get('first'));
        $bill->setLast($request->get('last'));
        $bill->setEmail($request->get('email'));
        $bill->setCode(md5(microtime()));
        if(!is_object($user) || $user->hasRole('ROLE_GUEST') || $user->hasRole('ROLE_DEMO')) {
            $bill->setFromFirst($request->get('yourFirst'));
            $bill->setFromLast($request->get('yourLast'));
            $bill->setFromEmail($request->get('yourEmail'));
            // temporary user for sending email
            $user = new User();
            $user->setFirst($request->get('yourFirst'));
            $user->setLast($request->get('yourLast'));
            $user->setEmail($request->get('yourEmail'));
            $session->set('invite', $bill->getCode());
        }
        else {
            $bill->setFromFirst($request->get('yourFirst'));
            $bill->setFromLast($request->get('yourLast'));
            $bill->setFromEmail($request->get('yourEmail'));
        }
        $orm->persist($bill);
        $orm->flush();
        // TODO: generalize this for other groups
        $group = $orm->getRepository('StudySauceBundle:GroupInvite')->findOneBy(['code' => $request->get('_code')]);
        if(!empty($group) && $group->getGroup()->getName() == 'Torch And Laurel' ||
            ($session->has('organization') && $session->get('organization') == 'Torch And Laurel') ||
            $user->hasGroup('Torch And Laurel')) {
            $email = new TorchEmailsController();
            $email->setContainer($this->container);
            $email->parentPayAction($user, $bill);
        }
        else {
            $email = new EmailsController();
            $email->setContainer($this->container);
            $email->parentPayAction($user, $bill);
        }

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
    public function studentInviteAction()
    {
        return $this->render('StudySauceBundle:Dialogs:student-invite.html.php', ['id' => 'student-invite']);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function inviteStudentSendAction(Request $request)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        /** @var $user \StudySauce\Bundle\Entity\User */
        $user = $this->getUser();
        $session = $request->getSession();

        // save the invite
        $student = new StudentInvite();
        $student->setUser($user);
        $student->setFirst($request->get('first'));
        $student->setLast($request->get('last'));
        $student->setEmail($request->get('email'));
        $student->setCode(md5(microtime()));
        if(!is_object($user) || $user->hasRole('ROLE_GUEST') || $user->hasRole('ROLE_DEMO')) {
            $student->setFromFirst($request->get('yourFirst'));
            $student->setFromLast($request->get('yourLast'));
            $student->setFromEmail($request->get('yourEmail'));
            // temporary user for sending email
            $user = new User();
            $user->setFirst($request->get('yourFirst'));
            $user->setLast($request->get('yourLast'));
            $user->setEmail($request->get('yourEmail'));
            $session->set('invite', $student->getCode());
        }
        else {
            $student->setFromFirst($user->getFirst());
            $student->setFromLast($user->getLast());
            $student->setFromEmail($user->getEmail());
        }
        $orm->persist($student);
        $orm->flush();
        // TODO: generalize this for other groups
        $group = $orm->getRepository('StudySauceBundle:GroupInvite')->findOneBy(['code' => $request->get('_code')]);
        if(!empty($group) && $group->getGroup()->getName() == 'Torch And Laurel' ||
            ($session->has('organization') && $session->get('organization') == 'Torch And Laurel') ||
            $user->hasGroup('Torch And Laurel')) {
            $email = new TorchEmailsController();
            $email->setContainer($this->container);
            $email->studentInviteAction($user, $student);
        }
        else {
            $email = new EmailsController();
            $email->setContainer($this->container);
            $email->studentInviteAction($user, $student);
        }

        return new JsonResponse(true);
    }
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function studentInviteConfirmAction()
    {
        return $this->render('StudySauceBundle:Dialogs:student-invite-confirm.html.php', ['id' => 'student-invite-confirm']);
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
    public function unsupportedAction()
    {
        return $this->render('StudySauceBundle:Dialogs:unsupported.html.php', ['id' => 'unsupported']);
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
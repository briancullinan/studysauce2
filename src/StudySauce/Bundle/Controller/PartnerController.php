<?php

namespace StudySauce\Bundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use StudySauce\Bundle\Entity\File;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\GroupInvite;
use StudySauce\Bundle\Entity\PartnerInvite;
use StudySauce\Bundle\Entity\User;
use StudySauce\Bundle\Entity\Visit;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class PartnerController
 * @package StudySauce\Bundle\Controller
 */
class PartnerController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        /** @var $user User */
        $user = $this->getUser();
        $csrfToken = $this->has('form.csrf_provider')
            ? $this->get('form.csrf_provider')->generateCsrfToken('partner_update')
            : null;

        $partner = $user->getPartnerOrAdviser();

        return $this->render('StudySauceBundle:Partner:tab.html.php', [
                'partner' => $partner,
                'isAdviser' => $partner instanceof User && $partner->hasRole('ROLE_ADVISER') ||
                    $partner->hasRole('ROLE_MASTER_ADVISER'),
                'csrf_token' => $csrfToken
            ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateAction(Request $request)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var $user User */
        $user = $this->getUser();

        /** @var $current PartnerInvite */
        $current = $user->getPartnerInvites()->first();
        $shouldSend = false;
        if(!empty($current) && $current->getEmail() == $request->get('email'))
        {
            // update the partner
            $partner = $current;
        }
        else
        {
            // check if they ever invited this partner
            $current = $user->getPartnerInvites()->filter(function (PartnerInvite $x) use ($request) {return $x->getEmail() == $request->get('email');})->first();
            if(!empty($current)) {
                // update created time so they become the current partner
                $partner = $current;
                $partner->setCreated(new \DateTime()); // reset created date if they change back to an existing invite
                $user->removePartnerInvite($partner);
                $user->addPartnerInvite($partner);
                $shouldSend = true;
            }
        }

        $isNew = false;
        if(empty($partner)) {
            $shouldSend = true;
            $isNew = true;
            $partner = new PartnerInvite();
            $partner->setUser($user);
            $partner->setCode(md5(microtime()));
            $partner->setEmail($request->get('email'));
            $user->addPartnerInvite($partner);
        }

        $photo = $user->getFiles()->filter(function (File $g) use($request) {return $g->getId() == $request->get('photo');})->first();
        $partner->setPhoto(empty($photo) ? null : $photo);
        $partner->setFirst($request->get('first'));
        $partner->setLast($request->get('last'));
        $partner->setPermissions(explode(',', $request->get('permissions')));

        // save the entity
        if($isNew)
            $orm->persist($partner);
        else
            $orm->merge($partner);
        $orm->flush();

        if($shouldSend)
        {
            $email = new EmailsController();
            $email->setContainer($this->container);
            $email->partnerInviteAction($user, $partner);
        }

        $csrfToken = $this->has('form.csrf_provider')
            ? $this->get('form.csrf_provider')->generateCsrfToken('partner_update')
            : null;
        return new JsonResponse(['csrf_token' => $csrfToken]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function userlistAction()
    {
        set_time_limit(0);
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var $user User */
        $user = $this->getUser();
        if(!$user->hasRole('ROLE_ADVISER') && !$user->hasRole('ROLE_MASTER_ADVISER') && !$user->hasRole('ROLE_PARTNER')) {
            throw new AccessDeniedHttpException();
        }
        elseif($user->hasRole('ROLE_ADVISER') || $user->hasRole('ROLE_MASTER_ADVISER')) {
            $users = [];
            foreach ($user->getGroups()->toArray() as $i => $g) {
                /** @var Group $g */
                $users = array_merge($users, $g->getUsers()->toArray());
            }
        }
        else {
            $users = [];
        }

        /** @var PartnerInvite $partner */
        $partner = $orm->getRepository('StudySauceBundle:PartnerInvite')->findBy(['partner' => $user->getId()]);
        foreach($partner as $j => $p)
        {
            /** @var PartnerInvite $p */
            $users[] = $p->getUser();
        }

        $users = array_unique($users);
        // show sessions
        $sessions = $orm->getRepository('StudySauceBundle:Visit')->createQueryBuilder('v')
            ->leftJoin('v.user', 'u')
            ->select(['v', 'u'])
            ->andWhere('v.user IN (\'' . implode('\',\'', array_map(function (User $u) {return $u->getId();}, $users)) . '\')')
            ->andWhere('v.path LIKE \'/schedule%\' OR v.path LIKE \'/metrics%\' OR v.path LIKE \'/goals%\' OR v.path LIKE \'/plan%\' OR v.path LIKE \'/course%\' OR v.path LIKE \'/account%\' OR v.path LIKE \'/premium%\' OR v.path LIKE \'/deadlines%\' OR v.path LIKE \'/checkin%\' OR v.path LIKE \'/home%\' OR v.path LIKE \'/partner%\' OR v.path LIKE \'/profile%\'')
            ->andWhere('u.roles NOT LIKE \'%ADVISER%\' AND u.roles NOT LIKE \'%ADMIN%\'')
            ->groupBy('v.session')
            ->orderBy('v.created', 'DESC')
            ->getQuery()
            ->getResult();

        // group sessions by day
        $groups = [];
        foreach($sessions as $v) {
            /** @var Visit $v */
            $groups[$v->getCreated()->format('Y-m-d')][$v->getUser()->getId()] = $v;
        }
        $sessions = call_user_func_array('array_merge', $groups);

        $showPartnerIntro = false;
        if(count($users) && empty($user->getProperty('seen_partner_intro'))) {
            $showPartnerIntro = true;
            /** @var $userManager UserManager */
            $userManager = $this->get('fos_user.user_manager');
            $user->setProperty('seen_partner_intro', true);
            $userManager->updateUser($user);
        }

        return $this->render('StudySauceBundle:Partner:userlist.html.php', [
            'sessions' => $sessions,
            'users' => $users,
            'showPartnerIntro' => $showPartnerIntro
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function importAction()
    {
        return $this->render('StudySauceBundle:Partner:import.html.php');
    }

    /**
     * @param Request $request
     * @param Group $group
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function importSaveAction(Request $request, Group $group = null)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        /** @var User $user */
        $user = $this->getUser();

        if($group == null)
            $group = $user->getGroups()->first();

        $users = $request->get('users');
        $existing = $user->getGroupInvites()->toArray();
        $emails = new EmailsController();
        $emails->setContainer($this->container);
        foreach($users as $i => $u)
        {
            // check if invite has already been sent
            foreach($existing as $j => $gi)
            {
                /** @var GroupInvite $gi */
                if(strtolower($gi->getEmail()) == $u['email']) {
                    $invite = $gi;
                    break;
                }
            }
            // save the invite
            if(!isset($invite) && !empty($group)) {
                $invite = new GroupInvite();
                $invite->setGroup($group);
                $invite->setUser($user);
                $invite->setFirst($u['first']);
                $invite->setLast($u['last']);
                $invite->setEmail($u['email']);
                $invite->setCode(md5(microtime()));
                $user->addGroupInvite($invite);
                $orm->persist($invite);
                $orm->flush();
                $emails->groupInviteAction($user, $invite);
            }
        }

        return $this->forward('StudySauceBundle:Partner:import', ['_format' => 'tab']);
    }

    /**
     * @param $_user
     * @param $_tab
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function partnerAction(User $_user, $_tab)
    {
        return $this->render('StudySauceBundle:Partner:partner.html.php', [
                'user' => $_user,
                'tab' => $_tab
            ]);
    }

    /**
     * @param $_user
     * @param $_tab
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function adviserAction(User $_user, $_tab)
    {
        $u = $this->getUser();

        if(!$u->hasRole('ROLE_PARTNER') && !$u->hasRole('ROLE_ADVISER') && !$u->hasRole('ROLE_MASTER_ADVISER') &&
            !$u->hasRole('ROLE_ADMIN'))
            throw new AccessDeniedHttpException();
        return $this->render('StudySauceBundle:Partner:adviser.html.php', [
                'user' => $_user,
                'tab' => $_tab
            ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateStatusAction(Request $request)
    {
        /** @var $userManager UserManager */
        $userManager = $this->get('fos_user.user_manager');

        /** @var $user User */
        $user = $userManager->findUserBy(['id' => intval($request->get('userId'))]);

        // TODO: check if partner and user is connected
        $user->setProperty('adviser_status', $request->get('status'));
        $userManager->updateUser($user);
        return new JsonResponse(true);
    }
}


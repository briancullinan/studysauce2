<?php

namespace StudySauce\Bundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use StudySauce\Bundle\Entity\File;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\PartnerInvite;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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

        return $this->render('StudySauceBundle:Partner:tab.html.php', [
                'partner' => $user->getPartnerInvites()->first(),
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
            // check if they every invited this partner
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
            $partner->setCode(md5(microtime(true)));
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
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var $user User */
        $user = $this->getUser();
        if($user->hasRole('ROLE_ADMIN')) {
            $groups = $orm->getRepository('StudySauceBundle:Group')->findAll();
            $users = $orm->getRepository('StudySauceBundle:User')->findAll();
        }
        else {
            $groups = $user->getGroups()->toArray();
            $users = [];
            foreach ($groups as $i => $g) {
                /** @var Group $g */
                $users = array_merge($users, $g->getUsers()->toArray());
            }
        }

        /** @var PartnerInvite $partner */
        $partner = $orm->getRepository('StudySauceBundle:PartnerInvite')->findBy(['partner' => $user->getId()]);
        foreach($partner as $j => $p)
        {
            /** @var PartnerInvite $p */
            $users[] = $p->getUser();
        }
        // show sessions
        $sessions = [];
        foreach($users as $i => $u) {
            /** @var User $u */
            if($u->hasRole('ROLE_ADVISER'))
                continue;

            $dql = 'SELECT v.session FROM StudySauceBundle:Visit v GROUP BY v.session ORDER BY v.created DESC';
            $query = $orm->createQuery($dql);
            $sids = $query->execute();
            $count = 0;
            foreach($sids as $j => $id)
            {
                if($count == 10)
                    break;
                /** @var ArrayCollection $visits */
                $visits = $u->getVisits();
                $criteria = Criteria::create()
                    ->where(Criteria::expr()->contains('path', '/metrics'))
                    ->orWhere(Criteria::expr()->contains('path', '/schedule'))
                    ->orWhere(Criteria::expr()->contains('path', '/account'))
                    ->orWhere(Criteria::expr()->contains('path', '/profile'))
                    ->orWhere(Criteria::expr()->contains('path', '/premium'))
                    ->orWhere(Criteria::expr()->contains('path', '/home'))
                    ->orWhere(Criteria::expr()->contains('path', '/partner'))
                    ->orWhere(Criteria::expr()->contains('path', '/goals'))
                    ->orWhere(Criteria::expr()->contains('path', '/plan'))
                    ->orWhere(Criteria::expr()->contains('path', '/deadlines'));
                $v = $visits
                    ->matching(Criteria::create()->where(Criteria::expr()->eq('session', $id)))
                    ->matching($criteria)->first();
                if(empty($v))
                {
                    $v = $visits
                        ->matching(Criteria::create()
                                ->where(Criteria::expr()->eq('session', $id)))->first();
                    if(!empty($v))
                        $v->setPath('/');
                }
                if(!empty($v))
                    $sessions[] = $v;
                $count++;
            }
        }

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
            'groups' => $groups,
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
     * @param $_user
     * @param $_tab
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function partnerAction($_user, $_tab)
    {
        /** @var $userManager UserManager */
        $userManager = $this->get('fos_user.user_manager');

        /** @var $user User */
        $user = $userManager->findUserBy(['id' => intval($_user)]);

        // TODO: check if partner and user is connected
        // if()

        return $this->render('StudySauceBundle:Partner:partner.html.php', [
                'user' => $user,
                'tab' => $_tab
            ]);
    }

    /**
     * @param $_user
     * @param $_tab
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function adviserAction($_user, $_tab)
    {
        /** @var $userManager UserManager */
        $userManager = $this->get('fos_user.user_manager');

        /** @var $user User */
        $user = $userManager->findUserBy(['id' => intval($_user)]);

        // TODO: check if partner and user is connected
        // if()

        return $this->render('StudySauceBundle:Partner:adviser.html.php', [
                'user' => $user,
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


<?php

namespace StudySauce\Bundle\Controller;

use Doctrine\ORM\EntityManager;
use StudySauce\Bundle\Entity\File;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Partner;
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
            ? $this->get('form.csrf_provider')->generateCsrfToken('checkin_update')
            : null;

        return $this->render('StudySauceBundle:Partner:tab.html.php', [
                'partner' => $user->getPartners()->first(),
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

        /** @var $current Partner */
        $current = $user->getPartners()->first();
        $shouldSend = false;
        if($current->getEmail() == $request->get('email'))
        {
            // update the partner
            $partner = $current;
        }
        else
        {
            // check if they every invited this partner
            $current = $user->getPartners()->filter(function (Partner $x) use ($request) {return $x->getEmail() == $request->get('email');})->first();
            if($current != null) {
                // update created time so they become the current partner
                $partner = $current;
                $partner->setCreated(new \DateTime()); // reset created date if they change back to an existing invite
                $user->removePartner($partner);
                $user->addPartner($partner);
                $shouldSend = true;
            }
        }

        $isNew = false;
        if(empty($partner)) {
            $shouldSend = true;
            $isNew = true;
            $partner = new Partner();
            $partner->setUser($user);
            $partner->setCode(md5(microtime(true)));
            $partner->setEmail($request->get('email'));
            $user->addPartner($partner);
        }

        $photo = $user->getFiles()->filter(function (File $g) use($request) {return $g->getId() == $request->get('photo');})->first();
        $partner->setPhoto(empty($photo) ? null : $photo);
        $partner->setFirst($request->get('first'));
        $partner->setLast($request->get('last'));
        $partner->setPermissions(explode(',', $request->get('permissions')));

        if($shouldSend)
        {
            $email = new EmailsController();
            $email->setContainer($this->container);
            $email->partnerInviteAction($user, $partner);
        }

        // save the entity
        if($isNew)
            $orm->persist($partner);
        else
            $orm->merge($partner);
        $orm->flush();

        $csrfToken = $this->has('form.csrf_provider')
            ? $this->get('form.csrf_provider')->generateCsrfToken('checkin_update')
            : null;
        return new JsonResponse(['csrf_token' => $csrfToken]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function userlistAction()
    {
        /** @var $user User */
        $user = $this->getUser();

        $groups = $user->getGroups()->toArray();
        $users = [];
        foreach($groups as $i => $g)
        {
            /** @var Group $g */
            $users = array_merge($users, $g->getUsers()->toArray());
        }

        return $this->render('StudySauceBundle:Partner:userlist.html.php', [
                'groups' => $groups,
                'users' => $users
            ]);
    }

    public function importAction()
    {
        return $this->render('StudySauceBundle:Partner:import.html.php');
    }
}


<?php

namespace StudySauce\Bundle\Controller;

use Doctrine\ORM\EntityManager;
use StudySauce\Bundle\Entity\File;
use StudySauce\Bundle\Entity\PartnerInvite;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContext;

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
                'isReadOnly' => $user->hasRole('ROLE_DEMO') || $partner instanceof User &&
                    ($partner->hasRole('ROLE_ADVISER') || $partner->hasRole('ROLE_MASTER_ADVISER')),
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
     * @param ContainerInterface $container
     */
    public static function getDemoPartner($container)
    {
        /** @var $orm EntityManager */
        $orm = $container->get('doctrine')->getManager();
        /** @var SecurityContext $context */
        /** @var TokenInterface $token */
        /** @var User $user */
        /** @var User $guest */
        if(!empty($context = $container->get('security.context')) && !empty($token = $context->getToken()) &&
            !empty($user = $token->getUser()) && $user->hasRole('ROLE_DEMO')) {
            $guest = $user;
        }

        $partner = new PartnerInvite();
        $partner->setUser($guest);
        $partner->setCode(md5(microtime()));
        $partner->setEmail('marketing@studysauce.com');
        $user->addPartnerInvite($partner);
        $partner->setFirst('Tom');
        $partner->setLast('Sage');
        $partner->setActivated(true);
        $partner->setPermissions([
            'goals',
            'metrics',
            'deadlines',
            'uploads',
            'plan',
            'profile'
        ]);
        $orm->persist($partner);
        $orm->flush();

    }

}


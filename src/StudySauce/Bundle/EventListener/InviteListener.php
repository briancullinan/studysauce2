<?php

namespace StudySauce\Bundle\EventListener;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use FOS\UserBundle\Security\LoginManager;
use StudySauce\Bundle\Entity\GroupInvite;
use StudySauce\Bundle\Entity\Invite;
use StudySauce\Bundle\Entity\ParentInvite;
use StudySauce\Bundle\Entity\PartnerInvite;
use StudySauce\Bundle\Entity\StudentInvite;
use StudySauce\Bundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * Class RedirectListener
 */
class InviteListener implements EventSubscriberInterface
{
    /** @var ContainerInterface $container */
    protected $container;

    protected $autoLogoutUser = [];


    /**
     * @param $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onInviteAccept', -100],
            KernelEvents::RESPONSE => ['onInviteResponse', -100]
        ];
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onInviteAccept(GetResponseEvent $event)
    {
        /** @var $userManager UserManager */
        $userManager = $this->container->get('fos_user.user_manager');
        /** @var Request $request */
        $request = $event->getRequest();
        if(!$request->request->has('_code'))
            return;
        /** @var Session $session */
        $session = $request->getSession();
        /** @var $orm EntityManager */
        $orm = $this->container->get('doctrine')->getManager();
        /** @var PartnerInvite $partner */
        $partner = $orm->getRepository('StudySauceBundle:PartnerInvite')->findOneBy(['code' => $request->get('_code')]);
        if(!empty($partner)) {
            $invite = $partner;
            $relationSetter = function (User $user) use ($partner, $orm) {
                $partner->setPartner($user);
                $orm->merge($partner);
            };
            $autoLogoutUser[$request->get('_code')] = function ($session) use ($request) {
                $session->set('partner', $request->get('_code'));
            };
        }
        /** @var ParentInvite $parent */
        $parent = $orm->getRepository('StudySauceBundle:ParentInvite')->findOneBy(['code' => $request->get('_code')]);
        if(!empty($parent)) {
            $invite = $parent;
            $relationSetter = function (User $user) use ($parent, $orm) {
                $parent->setParent($user);
                $orm->merge($parent);
            };
            $autoLogoutUser[$request->get('_code')] = function ($session) use ($request) {
                $session->set('parent', $request->get('_code'));
            };
        }
        /** @var GroupInvite $group */
        $group = $orm->getRepository('StudySauceBundle:GroupInvite')->findOneBy(['code' => $request->get('_code')]);
        if(!empty($group)) {
            $invite = $group;
            $relationSetter = function (User $user) use ($group, $orm) {
                $group->setStudent($user);
                $orm->merge($group);
            };
            $autoLogoutUser[$request->get('_code')] = function ($session) use ($request) {
                $session->set('group', $request->get('_code'));
            };
        }
        /** @var StudentInvite $student */
        $student = $orm->getRepository('StudySauceBundle:StudentInvite')->findOneBy(['code' => $request->get('_code')]);
        if(!empty($student)) {
            $invite = $student;
            $relationSetter = function (User $user) use ($student, $orm) {
                $student->setStudent($user);
                $orm->merge($student);
            };
            $autoLogoutUser[$request->get('_code')] = function ($session) use ($request) {
                $session->set('student', $request->get('_code'));
            };
        }
        /** @var Invite $invite */
        if(!empty($invite)) {
            $invite->setActivated(true);
            /** @var User $user */
            $user = $userManager->findUserByEmail($invite->getEmail());
            if(isset($relationSetter) && $user != null && !$user->hasRole('ROLE_GUEST'))
                $relationSetter($user);
            $orm->flush();
            /** @var SecurityContext $context */
            $context = $this->container->get('security.context');
            $context->setToken(null);
            $session->invalidate();
            /** @var EncoderFactory $encoder_service */
            $encoder_service = $this->container->get('security.encoder_factory');
            /** @var PasswordEncoderInterface $encoder */
            $user = $userManager->findUserByUsername('guest');
            $encoder = $encoder_service->getEncoder($user);
            $password = $encoder->encodePassword('guest', $user->getSalt());
            $context->setToken(new UsernamePasswordToken($user, $password, 'main', $user->getRoles()));
        }
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onInviteResponse(FilterResponseEvent $event)
    {
        /** @var $userManager UserManager */
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->findUserByUsername('guest');
        /** @var LoginManager $loginManager */
        $loginManager = $this->container->get('fos_user.security.login_manager');
        /** @var Response $response */
        $response = $event->getResponse();
        /** @var Request $request */
        $request = $event->getRequest();

        $loginManager->loginUser('main', $user, $response);
        $session = $request->getSession();
        if(isset($autoLogoutUser[$request->get('_code')]))
            $autoLogoutUser[$request->get('_code')]($session);
    }
}
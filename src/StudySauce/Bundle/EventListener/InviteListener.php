<?php

namespace StudySauce\Bundle\EventListener;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use FOS\UserBundle\Security\LoginManager;
use StudySauce\Bundle\Entity\Group;
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
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

/**
 * Class RedirectListener
 */
class InviteListener implements EventSubscriberInterface
{
    /** @var ContainerInterface $container */
    protected $container;

    protected static $autoLogoutUser = [];


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
            KernelEvents::RESPONSE => ['onInviteResponse', -100],
            SecurityEvents::INTERACTIVE_LOGIN => ['onLogin', -100],
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
        // TODO: only accept codes from landing pages?
        if(empty($request->get('_code')))
            return;
        /** @var Session $session */
        $session = $request->getSession();
        /** @var $orm EntityManager */
        $orm = $this->container->get('doctrine')->getManager();
        /** @var PartnerInvite $partner */
        // TODO: merge with getInvite?
        $partner = $orm->getRepository('StudySauceBundle:PartnerInvite')->findOneBy(['code' => $request->get('_code')]);
        if(!empty($partner)) {
            $invite = $partner;
            self::$autoLogoutUser[$request->get('_code')] = function (Session $session) use ($request) {
                $session->set('partner', $request->get('_code'));
            };
        }
        /** @var ParentInvite $parent */
        $parent = $orm->getRepository('StudySauceBundle:ParentInvite')->findOneBy(['code' => $request->get('_code')]);
        if(!empty($parent)) {
            $invite = $parent;
            self::$autoLogoutUser[$request->get('_code')] = function (Session $session) use ($request) {
                $session->set('parent', $request->get('_code'));
            };
        }
        /** @var GroupInvite $group */
        $group = $orm->getRepository('StudySauceBundle:GroupInvite')->findOneBy(['code' => $request->get('_code')]);
        if(!empty($group)) {
            $invite = $group;
            self::$autoLogoutUser[$request->get('_code')] = function (Session $session) use ($request) {
                $session->set('group', $request->get('_code'));
            };
        }
        /** @var StudentInvite $student */
        $student = $orm->getRepository('StudySauceBundle:StudentInvite')->findOneBy(['code' => $request->get('_code')]);
        if(!empty($student)) {
            $invite = $student;
            self::$autoLogoutUser[$request->get('_code')] = function (Session $session) use ($request) {
                $session->set('student', $request->get('_code'));
            };
        }
        /** @var Invite $invite */
        if(!empty($invite)) {
            $invite->setActivated(true);
            /** @var User $user */
            $user = $userManager->findUserByEmail($invite->getEmail());
            if($user != null && !$user->hasRole('ROLE_GUEST'))
                self::setInviteRelationship($orm, $request, $user);
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
     * @param EntityManager $orm
     * @param Request $request
     * @return \StudySauce\Bundle\Entity\GroupInvite|\StudySauce\Bundle\Entity\StudentInvite
     */
    public static function getInvite(EntityManager $orm, Request $request)
    {
        if(!empty($request->get('_code'))) {
            $code = $request->get('_code');
        }
        if(!empty($request->getSession()->get('partner'))) {
            $code = $request->getSession()->get('partner');
        }
        if(!empty($request->getSession()->get('parent'))) {
            $code = $request->getSession()->get('parent');
        }
        if(!empty($request->getSession()->get('student'))) {
            $code = $request->getSession()->get('student');
        }
        if(!empty($request->getSession()->get('group'))) {
            $code = $request->getSession()->get('group');
        }
        if(isset($code)) {
            /** @var PartnerInvite $partner */
            $partner = $orm->getRepository('StudySauceBundle:PartnerInvite')->findOneBy(['code' => $code]);
            if(!empty($partner)) return $partner;
            /** @var ParentInvite $parent */
            $parent = $orm->getRepository('StudySauceBundle:ParentInvite')->findOneBy(['code' => $code]);
            if(!empty($parent)) return $parent;
            /** @var GroupInvite $group */
            $group = $orm->getRepository('StudySauceBundle:GroupInvite')->findOneBy(['code' => $code]);
            if(!empty($group)) return $group;
            /** @var StudentInvite $student */
            $student = $orm->getRepository('StudySauceBundle:StudentInvite')->findOneBy(['code' => $code]);
            if(!empty($student)) return $student;
        }
        return null;
    }

    /**
     * @param EntityManager $orm
     * @param Request $request
     * @param User $user
     */
    public static function setInviteRelationship(EntityManager $orm, Request $request, User $user) {
        if(!empty($request->get('_code'))) {
            $code = $request->get('_code');
        }
        if(!empty($request->getSession()->get('partner'))) {
            $user->addRole('ROLE_PARTNER');
            $code = $request->getSession()->get('partner');
        }
        if(!empty($request->getSession()->get('parent'))) {
            $user->addRole('ROLE_PARENT');
            $code = $request->getSession()->get('parent');
        }
        if(!empty($request->getSession()->get('student'))) {
            $code = $request->getSession()->get('student');
        }
        if(!empty($request->getSession()->get('group'))) {
            $code = $request->getSession()->get('group');
        }

        if(isset($code)) {
            /** @var PartnerInvite $partner */
            $partner = $orm->getRepository('StudySauceBundle:PartnerInvite')->findOneBy(['code' => $code]);
            if (!empty($partner)) {
                $partner->setPartner($user);
                $user->addInvitedPartner($partner);
                $orm->merge($partner);
            }
            /** @var ParentInvite $parent */
            $parent = $orm->getRepository('StudySauceBundle:ParentInvite')->findOneBy(['code' => $code]);
            if (!empty($parent)) {
                $parent->setParent($user);
                $user->addInvitedParent($parent);
                $orm->merge($parent);
            }
            /** @var GroupInvite $group */
            $group = $orm->getRepository('StudySauceBundle:GroupInvite')->findOneBy(['code' => $code]);
            if (!empty($group)) {
                $group->setStudent($user);
                $user->addInvitedGroup($group);
                $user->addGroup($group->getGroup());
                $orm->merge($group);
            }
            /** @var StudentInvite $student */
            $student = $orm->getRepository('StudySauceBundle:StudentInvite')->findOneBy(['code' => $code]);
            if (!empty($student)) {
                $student->setStudent($user);
                $user->addInvitedStudent($student);
                if ($student->getUser()->hasRole('ROLE_PARENT') || $student->getUser()->hasRole('ROLE_PARTNER')) {
                    if ($student->getUser()->hasRole('ROLE_PAID')) {
                        $user->addRole('ROLE_PAID');
                    }
                }
                $orm->merge($student);
            }
        }
        // assign correct group to anonymous users
        if(!empty($request->getSession()->get('organization'))) {
            /** @var Group $group */
            $group = $orm->getRepository('StudySauceBundle:Group')->findOneBy(['name' => $request->getSession()->get('organization')]);
            $user->addGroup($group);
        }
    }

    /**
     * @param InteractiveLoginEvent $e
     */
    public function onLogin(InteractiveLoginEvent $e) {
        /** @var $orm EntityManager */
        $orm = $this->container->get('doctrine')->getManager();
        /** @var $request Request */
        $request = $e->getRequest();
        /** @var User $user */
        $user = $e->getAuthenticationToken()->getUser();


        if($user != null && !$user->hasRole('ROLE_GUEST'))
            self::setInviteRelationship($orm, $request, $user);

    }


    /**
     * @param FilterResponseEvent $event
     */
    public function onInviteResponse(FilterResponseEvent $event)
    {
        /** @var Response $response */
        $response = $event->getResponse();
        /** @var Request $request */
        $request = $event->getRequest();

        if(isset(self::$autoLogoutUser[$request->get('_code')])) {
            /** @var $userManager UserManager */
            $userManager = $this->container->get('fos_user.user_manager');
            $user = $userManager->findUserByUsername('guest');
            /** @var LoginManager $loginManager */
            $loginManager = $this->container->get('fos_user.security.login_manager');

            $loginManager->loginUser('main', $user, $response);
            $session = $request->getSession();
            $setter = self::$autoLogoutUser[$request->get('_code')];
            $setter($session);

            // only do this one per landing
            unset(self::$autoLogoutUser[$request->get('_code')]);
        }
    }
}
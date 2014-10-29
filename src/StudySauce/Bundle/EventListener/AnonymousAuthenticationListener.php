<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace StudySauce\Bundle\EventListener;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use StudySauce\Bundle\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\AuthenticationProviderManager;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * AnonymousAuthenticationListener automatically adds a Token if none is
 * already present.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class AnonymousAuthenticationListener implements EventSubscriberInterface
{
    /** @var $context \Symfony\Component\Security\Core\SecurityContext */
    private $context;

    private $logger;
    private $email;

    /** @var $userManager \FOS\UserBundle\Doctrine\UserManager */
    private $userManager;

    /** @var $orm \Doctrine\ORM\EntityManager */
    private $orm;

    /** @var $encoder \Symfony\Component\Security\Core\Encoder\EncoderFactory */
    private $encoder;

    /** @var $encoder \Symfony\Component\Security\Core\Authentication\AuthenticationProviderManager */
    private $authenticationManager;

    /** @var  $router */
    private $router;

    /**
     * @param SecurityContextInterface $context
     * @param LoggerInterface $logger
     * @param UserManager $userManager
     * @param $email
     * @param EntityManager $orm
     * @param EncoderFactory $encoder
     * @param AuthenticationProviderManager $authenticationManager
     */
    public function __construct(
        SecurityContextInterface $context,
        $key,
        LoggerInterface $logger = null,
        UserManager $userManager,
        $email,
        EntityManager $orm,
        EncoderFactory $encoder,
        AuthenticationProviderManager $authenticationManager,
        $router)
    {
        $this->context          = $context;
        $this->email            = $email;
        $this->logger           = $logger;
        $this->userManager      = $userManager;
        $this->orm              = $orm;
        $this->encoder          = $encoder;
        $this->authenticationManager = $authenticationManager;
        $this->router = $router;
    }

    /**
     * Handles anonymous authentication.
     *
     * @param GetResponseEvent $event A GetResponseEvent instance
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        /** @var User $user */
        /** @var MessageDigestPasswordEncoder $encoder */

        $request = $event->getRequest();
        $controller = $request->get('_controller');

        // only handle anonymous users with no context
        if (null !== ($token = $this->context->getToken()) && $token->isAuthenticated() && $token->getUser() != 'anon.') {
            // reset Guest User for oauth connections
            if(($controller == 'HWI\Bundle\OAuthBundle\Controller\ConnectController::connectServiceAction' ||
                    $controller == 'StudySauce\Bundle\Controller\AccountController::login' ||
                    $controller == 'StudySauce\Bundle\Controller\AccountController::register' ||
                    $controller == 'HWI\Bundle\OAuthBundle\Controller\ConnectController::redirectToServiceAction')&&
                ($user = $token->getUser()) !== null && $user->hasRole('ROLE_GUEST'))
            {
                $this->context->setToken(new AnonymousToken('main', 'anon.', []));
            }
            return;
        }

        $username = 'guest';
        $user = $this->userManager->findUserByUsername($username);
        if($user == null)
        {
            // generate a new guest user in the database
            $user = $this->userManager->createUser();
            $user->setUsername($username);
            $user->setUsernameCanonical($username);
            $encoder = $this->encoder->getEncoder($user);
            $password = $encoder->encodePassword($username, $user->getSalt());
            $user->setPassword($password);
            $user->setEmail($this->email);
            $user->addRole('ROLE_GUEST');
            $user->setEnabled(true);
            $user->setFirstName('Guest');
            $user->setLastName('Account');
            $this->orm->persist($user);
            $this->orm->flush();
        }

        $encoder = $this->encoder->getEncoder($user);
        $password = $encoder->encodePassword('guest', $user->getSalt());
        $this->context->setToken(new UsernamePasswordToken($user, $password, 'main', ['ROLE_GUEST', 'IS_AUTHENTICATED_ANONYMOUSLY']));

        if (null !== $this->logger) {
            $this->logger->info('Populated SecurityContext with an anonymous Token');
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', -128],
            KernelEvents::RESPONSE => ['onKernelResponse', -128]
        ];
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $request = $event->getRequest();
        $controller = $request->get('_controller');
        if($controller == 'HWI\Bundle\OAuthBundle\Controller\ConnectController::connectServiceAction')
        {
            if (null !== ($token = $this->context->getToken()) && $token->isAuthenticated() && $token->getUser() != 'anon.') {
                $url = $this->router->generate('home');
            }
            else {
                $url = $this->router->generate('account_login');
            }
            $event->setResponse(new RedirectResponse($url));
        }

    }

}

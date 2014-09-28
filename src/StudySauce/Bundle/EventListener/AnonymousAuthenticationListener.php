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
use Symfony\Component\Security\Core\Authentication\AuthenticationProviderManager;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

/**
 * AnonymousAuthenticationListener automatically adds a Token if none is
 * already present.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class AnonymousAuthenticationListener implements ListenerInterface
{
    /** @var $context \Symfony\Component\Security\Core\SecurityContext */
    private $context;

    private $key;
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

    /**
     * @param SecurityContextInterface $context
     * @param $key
     * @param LoggerInterface $logger
     * @param UserManager $userManager
     * @param $email
     * @param EntityManager $orm
     * @param EncoderFactory $encoder
     */
    public function __construct(
        SecurityContextInterface $context,
        $key,
        LoggerInterface
        $logger = null,
        UserManager $userManager,
        $email,
        EntityManager $orm,
        EncoderFactory $encoder,
        AuthenticationProviderManager $authenticationManager)
    {
        $this->context          = $context;
        $this->key              = $key;
        $this->email            = $email;
        $this->logger           = $logger;
        $this->userManager      = $userManager;
        $this->orm              = $orm;
        $this->encoder          = $encoder;
        $this->authenticationManager = $authenticationManager;
    }

    /**
     * Handles anonymous authentication.
     *
     * @param GetResponseEvent $event A GetResponseEvent instance
     */
    public function handle(GetResponseEvent $event)
    {
        /** @var $user \StudySauce\Bundle\Entity\User */
        /** @var $encoder \Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder */

        // only handle anonymous users with no context
        if (null !== ($token = $this->context->getToken()) && $token->isAuthenticated()) {
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
            $user->setCreated(new \DateTime());
            $user->addRole("ROLE_GUEST");
            $user->setEnabled(true);
            $this->orm->persist($user);
            $this->orm->flush();
        }

        $this->context->setToken(new AnonymousToken($this->key, 'anon.', []));

        $encoder = $this->encoder->getEncoder($user);
        $password = $encoder->encodePassword('guest', $user->getSalt());
        $this->context->setToken(new UsernamePasswordToken($user, $password, $this->key, ['ROLE_GUEST']));

        if (null !== $this->logger) {
            $this->logger->info('Populated SecurityContext with an anonymous Token');
        }
    }
}

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
use FOS\UserBundle\Security\LoginManager;
use StudySauce\Bundle\Controller\CalcController;
use StudySauce\Bundle\Controller\CourseController;
use StudySauce\Bundle\Controller\DeadlinesController;
use StudySauce\Bundle\Controller\GoalsController;
use StudySauce\Bundle\Controller\HomeController;
use StudySauce\Bundle\Controller\MetricsController;
use StudySauce\Bundle\Controller\PartnerController;
use StudySauce\Bundle\Controller\ScheduleController;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
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

    private $logger;

    /** @var ContainerInterface $container */
    private $container;

    /**
     * @param SecurityContextInterface $context
     * @param $key
     * @param LoggerInterface $logger
     * @param ContainerInterface $container
     */
    public function __construct(
        SecurityContextInterface $context,
        $key,
        LoggerInterface $logger = null,
        ContainerInterface $container)
    {
        $this->context          = $context;
        $this->logger           = $logger;
        $this->container        = $container;
    }

    /**
     * Handles anonymous authentication.
     *
     * @param GetResponseEvent $event A GetResponseEvent instance
     */
    public function handle(GetResponseEvent $event)
    {
        /** @var User $user */
        /** @var MessageDigestPasswordEncoder $encoder */

        $request = $event->getRequest();

        // only handle anonymous users with no context
        if (null !== ($token = $this->context->getToken()) && $token->isAuthenticated() && $token->getUser() != 'anon.' &&
            $request->get('_route') != 'demo') {
            // reset Guest User for oauth connections
            $controller = $request->get('_controller');
            if(($controller == 'HWI\Bundle\OAuthBundle\Controller\ConnectController::connectServiceAction' ||
                    $controller == 'StudySauce\Bundle\Controller\AccountController::login' ||
                    $controller == 'StudySauce\Bundle\Controller\AccountController::register' ||
                    $controller == 'HWI\Bundle\OAuthBundle\Controller\ConnectController::redirectToServiceAction')&&
                ($user = $token->getUser()) !== null && ($user->hasRole('ROLE_GUEST') || $user->hasRole('ROLE_DEMO')))
            {
                $this->context->setToken(new AnonymousToken('main', 'anon.', []));
            }
            return;
        }

        $username = 'Guest' . ($request->get('_route') == 'demo' ? ('-' . substr(md5(microtime()), -5)) : '');
        /** @var UserManager $userManager */
        $userManager = $this->container->get('fos_user.user_manager');
        /** @var EntityManager $orm */
        $orm = $this->container->get('doctrine')->getManager();
        /** @var Router $router */
        $router = $this->container->get('router');
        /** @var EncoderFactory $encoder */
        $encoder = $this->container->get('security.encoder_factory');
        $user = $userManager->findUserByUsername($username);
        if($user == null || $request->get('_route') == 'demo')
        {
            // generate a new guest user in the database
            $user = $userManager->createUser();
            $user->setUsername($username);
            $password = $encoder->getEncoder($user)->encodePassword($username, $user->getSalt());
            $user->setPassword($password);
            $user->setEmail($username . '_studysauce.com@mailinator.com');
            $userManager->updateCanonicalFields($user);
            if($request->get('_route') == 'demo') {
                $user->addRole('ROLE_DEMO');
                $user->addRole('ROLE_PAID');
            }
            else
                $user->addRole('ROLE_GUEST');
            $user->setEnabled(true);
            $user->setFirst($username);
            $user->setLast('Account');
            $orm->persist($user);
            $orm->flush();
        }

        $password = $encoder->getEncoder($user)->encodePassword('guest', $user->getSalt());
        $this->context->setToken(new UsernamePasswordToken($user, $password, 'main', $user->getRoles()));

        if (null !== $this->logger) {
            $this->logger->info('Populated SecurityContext with an anonymous Token');
        }

        if($request->get('_route') == 'demo')
        {
            if($user->hasRole('ROLE_GUEST') || $user->hasRole('ROLE_DEMO')) {
                ScheduleController::getDemoSchedule($this->container);
                DeadlinesController::getDemoDeadlines($this->container);
                MetricsController::getDemoCheckins($this->container);
                GoalsController::getDemoGoals($this->container);
                CalcController::getDemoCalculations($this->container);
                CourseController::getDemoCourses($this->container);
                PartnerController::getDemoPartner($this->container);
            }
            list($route, $options) = HomeController::getUserRedirect($user);
            $response = new RedirectResponse($router->generate($route, $options));

            /** @var LoginManager $loginManager */
            $loginManager = $this->container->get('fos_user.security.login_manager');
            $loginManager->loginUser('main', $user, $response);

            $event->setResponse($response);
        }
    }

}

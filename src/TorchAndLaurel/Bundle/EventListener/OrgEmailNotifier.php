<?php

namespace TorchAndLaurel\Bundle\EventListener;

use StudySauce\Bundle\Entity\User;
use Swift_Mailer;
use Swift_Message;
use Swift_Mime_SimpleMessage;
use Symfony\Bundle\FrameworkBundle\Templating\DelegatingEngine;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * Class RedirectListener
 */
class OrgEmailNotifier implements EventSubscriberInterface
{
    /** @var ContainerInterface $container */
    protected $container;
    /** @var DelegatingEngine $templating */
    protected $templating;
    /** @var  Swift_Mailer $mailer */
    protected $mailer;

    /**
     * @param $container
     * @param EngineInterface $templating
     * @param $mailer
     */
    public function __construct($container, EngineInterface $templating, $mailer)
    {
        $this->container = $container;
        $this->templating = $templating;
        $this->mailer = $mailer;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => ['onRegistrationSuccess', -128],
            KernelEvents::REQUEST => ['onCheckoutBegin', -128],
        ];
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onCheckoutBegin(GetResponseEvent $event)
    {
        $session = $event->getRequest()->getSession();
        /** @var SecurityContext $context */
        $context = $this->container->get('security.context');
        if($event->getRequest()->get('_controller') == 'StudySauce\Bundle\Controller\BuyController::checkoutAction') {
            $user = $context->getToken()->getUser();
            if ($user->hasGroup('Torch And Laurel') || ($session->has('organization') &&
                    $session->get('organization') == 'Torch And Laurel')) {
                $session->set('coupon', 'TAL17A0D6');
            }
        }
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onRegistrationSuccess(FilterResponseEvent $event)
    {

        /** @var SecurityContext $context */
        $context = $this->container->get('security.context');
        if($event->getRequest()->get('_controller') == 'StudySauce\Bundle\Controller\AccountController::createAction' &&
            $event->getResponse() instanceof RedirectResponse) {

            /** @var User $user */
            $user = $context->getToken()->getUser();

            if ($user->hasGroup('Torch And Laurel')) {
                /** @var Swift_Mime_SimpleMessage $message */
                $message = Swift_Message::newInstance()
                    ->setSubject($user->getFirst() . ' ' . $user->getLast() . ' has signed up for an account.')
                    ->setFrom('admin@studysauce.com')
                    ->setTo('torchandlaurel@mailinator.com')
                    ->setBody(
                        $this->templating->render(
                            'TorchAndLaurelBundle:Emails:registration-notification.html.php',
                            [
                                'user' => $user,
                                'greeting' => 'Dear Torch And Laurel,'
                            ]
                        ),
                        'text/html'
                    );
                $headers = $message->getHeaders();
                $headers->addParameterizedHeader(
                    'X-SMTPAPI',
                    preg_replace('/(.{1,72})(\s)/i', "\1\n   ", json_encode(['category' => ['welcome-partner']]))
                );
                $this->mailer->send($message);
            }
        }
    }
}
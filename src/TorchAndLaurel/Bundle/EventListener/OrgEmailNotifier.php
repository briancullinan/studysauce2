<?php

namespace TorchAndLaurel\Bundle\EventListener;

use StudySauce\Bundle\Entity\User;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Templating\DelegatingEngine;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * Class RedirectListener
 */
class OrgEmailNotifier implements EventSubscriberInterface
{
    protected $container;
    /** @var DelegatingEngine $templating */
    protected $templating;
    protected $kernel;
    protected $mailer;

    /**
     * @param $container
     * @param EngineInterface $templating
     * @param $kernel
     * @param $mailer
     */
    public function __construct($container, EngineInterface $templating, $kernel, $mailer)
    {
        $this->container = $container;
        $this->templating = $templating;
        $this->kernel = $kernel;
        $this->mailer = $mailer;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => ['onRegistrationSuccess', -128],
        ];
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onRegistrationSuccess(FilterResponseEvent $event)
    {

        /** @var SecurityContext $context */
        $context = $this->container->get('security.context');
        if($event->getRequest()->get('_controller') == 'StudySauceBundle:Account:create' &&
            $event->getResponse() instanceof RedirectResponse) {

            /** @var User $user */
            $user = $context->getToken()->getUser();

            if ($user->hasGroup('Torch And Laurel')) {
                $message = Swift_Message::newInstance()
                    ->setSubject($user->getFirst() . ' ' . $user->getLast() . ' has signed up for an account.')
                    ->setFrom('admin@studysauce.com')
                    ->setTo('torchandlaurel@mailinator.com')
                    ->setBody(
                        $this->templating->render(
                            'TorchAndLaurel:Emails:registration-notification.html.php',
                            [
                                'name' => $user,
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
<?php

namespace StudySauce\Bundle\EventListener;

use Doctrine\ORM\EntityManager;
use StudySauce\Bundle\Entity\Visit;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * Class RedirectListener
 */
class PageTracker implements EventSubscriberInterface
{
    private $orm;
    private $session;

    /**
     *
     */
    public function __construct(EntityManager $orm, Session $session, SecurityContext $context)
    {
        $this->orm = $orm;
        $this->session = $session;
        $this->context = $context;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', -128]
        ];
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if($event->isMasterRequest()) {
            $request = $event->getRequest();
            $path = $request->getPathInfo();
            $visit = new Visit();
            $visit->setPath($path);
            $visit->setHash('');
            $this->session->start();
            $id = $this->session->getId();
            $session = $this->orm->getRepository('StudySauceBundle:Session')->find($id);
            $visit->setSession($session);
            $user = $this->context->getToken()->getUser();
            $visit->setUser($user);
            $visit->setQuery(serialize($request->query->all()));
            $this->orm->persist($visit);
            $this->orm->flush();
        }
    }
}
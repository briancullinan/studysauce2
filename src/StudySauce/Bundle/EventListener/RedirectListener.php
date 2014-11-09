<?php

namespace StudySauce\Bundle\EventListener;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class RedirectListener
 */
class RedirectListener implements EventSubscriberInterface
{
    protected $templating;
    protected $kernel;
    /**
     *
     */
    public function __construct(EngineInterface $templating, $kernel)
    {
        $this->templating = $templating;
        $this->kernel = $kernel;

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
            KernelEvents::RESPONSE => ['onKernelResponse', -128],
            KernelEvents::EXCEPTION => ['onKernelException', -128]
        ];
    }


    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        // provide the better way to display a enhanced error page only in prod environment, if you want
        //if ('prod' == $this->kernel->getEnvironment()) {
        // exception object
        $exception = $event->getException();

        // new Response object
        $response = new Response();

        // set response content
        if ($exception instanceof NotFoundHttpException) {
            $event->getRequest()->request->set('_format', 'funnel');
            $response->setContent(
            // create you custom template AcmeFooBundle:Exception:exception.html.twig
                $this->templating->render(
                    'StudySauceBundle:Exception:error404.html.php'
                )
            );
        }
        else {
            $response->setContent(
            // create you custom template AcmeFooBundle:Exception:exception.html.twig
                $this->templating->render(
                    'StudySauceBundle:Exception:error.html.php'
                )
            );
        }
        // HttpExceptionInterface is a special type of exception
        // that holds status code and header details
        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
        } else {
            $response->setStatusCode(500);
        }

        // set the new $response object to the $event
        $event->setResponse($response);
        //}
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();
        $request = $event->getRequest();

        if ($request->isXmlHttpRequest() && $response->isRedirect()) {
            $response->setContent(json_encode(['redirect' => $response->headers->get('Location')]));
            $response->setStatusCode(200);
            $response->headers->remove('Location');
        }
    }
}
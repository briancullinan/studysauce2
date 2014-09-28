<?php

/*
 * This file is NOT part of the Symfony package.
 *
 * (c) Brian Cullinan <bjcullinan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace StudySauce\Bundle\DependencyInjection;

if (!defined('ENT_SUBSTITUTE')) {
    define('ENT_SUBSTITUTE', 8);
}

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Fragment\HIncludeFragmentRenderer;
use Symfony\Component\HttpKernel\Fragment\InlineFragmentRenderer;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\UriSigner;
use Symfony\Component\Templating\EngineInterface;

/**
 * Implements the Hinclude rendering strategy.
 *
 * @author Brian Cullinan <bjcullinan@gmail.com>
 */
class SIncludeFragmentRenderer extends HIncludeFragmentRenderer
{
    private $container;
    private $kernel;
    private $dispatcher;

    /**
     * {@inheritdoc}
     */
    public function __construct(ContainerInterface $container, UriSigner $signer = null, $globalDefaultTemplate = null, HttpKernelInterface $kernel, EventDispatcherInterface $dispatcher = null)
    {
        $this->container = $container;
        $this->kernel = $kernel;
        $this->dispatcher = $dispatcher;

        parent::__construct(null, $signer, $globalDefaultTemplate, $container->getParameter('kernel.charset'));
    }

    /**
     * {@inheritdoc}
     */
    public function render($uri, Request $request, array $options = [])
    {
        // setting the templating cannot be done in the constructor
        // as it would lead to an infinite recursion in the service container
        if (!$this->hasTemplating()) {
            $this->setTemplating($this->container->get('templating'));
        }
        $session = $request->getSession();
        $noInclude = $request->query->get('noInclude');
        if($noInclude)
            $session->set('noInclude', true);
        else
            $noInclude = $session->get('noInclude');
        if($noInclude)
        {
            $renderer = new InlineFragmentRenderer($this->kernel, $this->dispatcher);
            return $renderer->render($uri, $request, $options);
        }
        $response = parent::render($uri, $request, $options);
        $response->setContent(str_replace('</hx:include>', '</div>', str_replace('<hx:include src=', '<div class="sinclude" data-src=', $response->getContent())));
        return $response;
    }

    public function getName()
    {
        return 'sinclude';
    }
}

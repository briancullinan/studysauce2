<?php

// app/config/routing.php
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();

$collection->add(
    'command_control',
    new Route('/command',['_controller' => 'AdminBundle:Admin:index', '_format' => 'adviser'])
);

$collection->add(
    'command_callback',
    new Route(
        '/command/control',
        ['_controller' => 'AdminBundle:Admin:index', '_format' => 'tab'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add(
    'remove_user',
    new Route(
        '/command/remove/user',
        ['_controller' => 'AdminBundle:Admin:removeUser', '_format' => 'tab'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

return $collection;
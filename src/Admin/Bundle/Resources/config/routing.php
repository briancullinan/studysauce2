<?php

// app/config/routing.php
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();

$collection->add(
    'command_control',
    new Route(
        '/command/{_format}',
        ['_controller' => 'AdminBundle:Admin:index', '_format' => 'adviser'],
        ['_format' => DASHBOARD_VIEWS]
    )
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

$collection->add(
    'cancel_user',
    new Route(
        '/command/cancel/user',
        ['_controller' => 'AdminBundle:Admin:cancelUser', '_format' => 'tab'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add(
    'save_user',
    new Route(
        '/command/save/user',
        ['_controller' => 'AdminBundle:Admin:saveUser', '_format' => 'tab'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add(
    'add_user',
    new Route(
        '/command/add/user',
        ['_controller' => 'AdminBundle:Admin:addUser', '_format' => 'tab'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add(
    'save_group',
    new Route(
        '/command/save/group',
        ['_controller' => 'AdminBundle:Admin:saveGroup', '_format' => 'tab'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add(
    'reset_user',
    new Route(
        '/command/reset/user',
        ['_controller' => 'AdminBundle:Admin:resetUser', '_format' => 'tab'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add(
    'validation',
    new Route(
        '/validation',
        ['_controller' => 'AdminBundle:Validation:index', '_format' => 'adviser'],
        ['_format' => DASHBOARD_VIEWS]
    )
);

$collection->add(
    'validation_test',
    new Route(
        '/validation/test',
        ['_controller' => 'AdminBundle:Validation:test'],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

return $collection;
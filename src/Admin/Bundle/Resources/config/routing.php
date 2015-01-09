<?php

// app/config/routing.php
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();

$collection->add(
    'command_control',
    new Route('/command', ['_controller' => 'AdminBundle:Admin:index', '_format' => 'adviser'])
);

return $collection;
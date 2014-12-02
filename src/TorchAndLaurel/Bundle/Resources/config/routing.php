<?php

// app/config/routing.php
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();

$collection->add(
    'torchandlaurel_welcome',
    new Route('/torchandlaurel/{_code}', ['_controller' => 'TorchAndLaurelBundle:Landing:index','_code' => ''])
);

return $collection;
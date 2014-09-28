<?php

// app/config/routing.php
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();

$collection->add('course1', new Route('/course/1/{_format}', [
            '_controller' => 'Course1Bundle:Course1:index',
            '_format'     => 'dashboard'
        ]));

$collection->add('course1wizard', new Route('/course/1/{_step}/{_format}', [
            '_controller' => 'Course1Bundle:Course1:wizard',
            '_step' => 1,
            '_format'     => 'dashboard'
        ]));

return $collection;
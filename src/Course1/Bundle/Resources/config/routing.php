<?php

// app/config/routing.php
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();

$collection->add('lesson1', new Route('/course/1/lesson/1/step/{_step}/{_format}', [
            '_controller' => 'Course1Bundle:Lesson1:wizard',
            '_step' => 0,
            '_format'     => 'index'
        ], [
            '_format' => DASHBOARD_VIEWS,
            '_step' => '0|1|2|3|4'
        ]));

$collection->add('lesson2', new Route('/course/1/lesson/2/step/{_step}/{_format}', [
            '_controller' => 'Course1Bundle:Lesson2:wizard',
            '_step' => 0,
            '_format'     => 'index'
        ], [
            '_format' => DASHBOARD_VIEWS,
            '_step' => '0|1|2|3|4'
        ]));

$collection->add('lesson3', new Route('/course/1/lesson/3/step/{_step}/{_format}', [
            '_controller' => 'Course1Bundle:Lesson3:wizard',
            '_step' => 0,
            '_format'     => 'index'
        ], [
            '_format' => DASHBOARD_VIEWS,
            '_step' => '0|1|2|3|4'
        ]));

return $collection;
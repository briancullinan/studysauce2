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

$collection->add(
    'lesson1_update',
    new Route(
        '/course/1/lesson/1/update',
        [
            '_controller' => 'Course1Bundle:Lesson1:update'
        ],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add('lesson2', new Route('/course/1/lesson/2/step/{_step}/{_format}', [
            '_controller' => 'Course1Bundle:Lesson2:wizard',
            '_step' => 0,
            '_format'     => 'index'
        ], [
            '_format' => DASHBOARD_VIEWS,
            '_step' => '0|1|2|3|4'
        ]));

$collection->add(
    'lesson2_update',
    new Route(
        '/course/1/lesson/2/update',
        [
            '_controller' => 'Course1Bundle:Lesson2:update'
        ],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add('lesson3', new Route('/course/1/lesson/3/step/{_step}/{_format}', [
            '_controller' => 'Course1Bundle:Lesson3:wizard',
            '_step' => 0,
            '_format'     => 'index'
        ], [
            '_format' => DASHBOARD_VIEWS,
            '_step' => '0|1|2|3|4'
        ]));

$collection->add(
    'lesson3_update',
    new Route(
        '/course/1/lesson/3/update',
        [
            '_controller' => 'Course1Bundle:Lesson3:update'
        ],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add('lesson4', new Route('/course/1/lesson/4/step/{_step}/{_format}', [
            '_controller' => 'Course1Bundle:Lesson4:wizard',
            '_step' => 0,
            '_format'     => 'index'
        ], [
            '_format' => DASHBOARD_VIEWS,
            '_step' => '0|1|2|3|4'
        ]));

$collection->add(
    'lesson4_update',
    new Route(
        '/course/1/lesson/4/update',
        [
            '_controller' => 'Course1Bundle:Lesson4:update'
        ],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

return $collection;
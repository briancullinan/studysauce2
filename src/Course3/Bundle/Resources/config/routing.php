<?php

// app/config/routing.php
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();

$collection->add('course3_strategies', new Route('/course/3/lesson/1/step/{_step}/{_format}', [
            '_controller' => 'Course3Bundle:Strategies:wizard',
            '_step' => 0,
            '_format'     => 'index'
        ], [
            '_format' => DASHBOARD_VIEWS,
            '_step' => '0|1|2|3|4'
        ]));

$collection->add(
    'course3_strategies_update',
    new Route(
        '/course/3/lesson/1/update',
        [
            '_controller' => 'Course3Bundle:Strategies:update'
        ],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);


$collection->add('course3_group_study', new Route('/course/3/lesson/2/step/{_step}/{_format}', [
            '_controller' => 'Course3Bundle:GroupStudy:wizard',
            '_step' => 0,
            '_format'     => 'index'
        ], [
            '_format' => DASHBOARD_VIEWS,
            '_step' => '0|1|2|3|4'
        ]));

$collection->add(
    'course3_group_study_update',
    new Route(
        '/course/3/lesson/2/update',
        [
            '_controller' => 'Course3Bundle:GroupStudy:update'
        ],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add('course3_teaching', new Route('/course/3/lesson/3/step/{_step}/{_format}', [
            '_controller' => 'Course3Bundle:Teaching:wizard',
            '_step' => 0,
            '_format'     => 'index'
        ], [
            '_format' => DASHBOARD_VIEWS,
            '_step' => '0|1|2|3|4'
        ]));

$collection->add(
    'course3_teaching_update',
    new Route(
        '/course/3/lesson/3/update',
        [
            '_controller' => 'Course3Bundle:Teaching:update'
        ],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add('course3_active_reading', new Route('/course/3/lesson/4/step/{_step}/{_format}', [
            '_controller' => 'Course3Bundle:ActiveReading:wizard',
            '_step' => 0,
            '_format'     => 'index'
        ], [
            '_format' => DASHBOARD_VIEWS,
            '_step' => '0|1|2|3|4'
        ]));

$collection->add(
    'course3_active_reading_update',
    new Route(
        '/course/3/lesson/4/update',
        [
            '_controller' => 'Course3Bundle:ActiveReading:update'
        ],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add('course3_spaced_repetition', new Route('/course/3/lesson/5/step/{_step}/{_format}', [
            '_controller' => 'Course3Bundle:SpacedRepetition:wizard',
            '_step' => 0,
            '_format'     => 'index'
        ], [
            '_format' => DASHBOARD_VIEWS,
            '_step' => '0|1|2|3|4'
        ]));

$collection->add(
    'course3_spaced_repetition_update',
    new Route(
        '/course/3/lesson/5/update',
        [
            '_controller' => 'Course3Bundle:SpacedRepetition:update'
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
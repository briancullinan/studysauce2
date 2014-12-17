<?php

// app/config/routing.php
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();

$collection->add('course2_study_metrics', new Route('/course/2/lesson/1/step/{_step}/{_format}', [
            '_controller' => 'Course2Bundle:StudyMetrics:wizard',
            '_step' => 0,
            '_format'     => 'index'
        ], [
            '_format' => DASHBOARD_VIEWS,
            '_step' => '0|1|2|3|4'
        ]));

$collection->add(
    'course2_study_metrics_update',
    new Route(
        '/course/2/lesson/1/update',
        [
            '_controller' => 'Course2Bundle:StudyMetrics:update'
        ],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add('course2_study_plan', new Route('/course/2/lesson/2/step/{_step}/{_format}', [
            '_controller' => 'Course2Bundle:StudyPlan:wizard',
            '_step' => 0,
            '_format'     => 'index'
        ], [
            '_format' => DASHBOARD_VIEWS,
            '_step' => '0|1|2|3|4'
        ]));

$collection->add(
    'course2_study_plan_update',
    new Route(
        '/course/2/lesson/2/update',
        [
            '_controller' => 'Course2Bundle:StudyPlan:update'
        ],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add('course2_interleaving', new Route('/course/2/lesson/3/step/{_step}/{_format}', [
            '_controller' => 'Course2Bundle:Interleaving:wizard',
            '_step' => 0,
            '_format'     => 'index'
        ], [
            '_format' => DASHBOARD_VIEWS,
            '_step' => '0|1|2|3|4'
        ]));

$collection->add(
    'course2_interleaving_update',
    new Route(
        '/course/2/lesson/3/update',
        [
            '_controller' => 'Course2Bundle:Interleaving:update'
        ],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add('course2_study_tests', new Route('/course/2/lesson/4/step/{_step}/{_format}', [
            '_controller' => 'Course2Bundle:StudyTests:wizard',
            '_step' => 0,
            '_format'     => 'index'
        ], [
            '_format' => DASHBOARD_VIEWS,
            '_step' => '0|1|2|3|4'
        ]));

$collection->add(
    'course2_study_tests_update',
    new Route(
        '/course/2/lesson/4/update',
        [
            '_controller' => 'Course2Bundle:StudyTests:update'
        ],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add('course2_test_taking', new Route('/course/2/lesson/5/step/{_step}/{_format}', [
            '_controller' => 'Course2Bundle:TestTaking:wizard',
            '_step' => 0,
            '_format'     => 'index'
        ], [
            '_format' => DASHBOARD_VIEWS,
            '_step' => '0|1|2|3|4'
        ]));

$collection->add(
    'course2_test_taking_update',
    new Route(
        '/course/2/lesson/5/update',
        [
            '_controller' => 'Course2Bundle:TestTaking:update'
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
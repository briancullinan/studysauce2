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

$collection->add('course2_strategies', new Route('/course/2/lesson/6/step/{_step}/{_format}', [
            '_controller' => 'Course2Bundle:Strategies:wizard',
            '_step' => 0,
            '_format'     => 'index'
        ], [
            '_format' => DASHBOARD_VIEWS,
            '_step' => '0|1|2|3|4'
        ]));

$collection->add(
    'course2_strategies_update',
    new Route(
        '/course/2/lesson/6/update',
        [
            '_controller' => 'Course2Bundle:Strategies:update'
        ],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);


$collection->add('course2_group_study', new Route('/course/2/lesson/7/step/{_step}/{_format}', [
            '_controller' => 'Course2Bundle:GroupStudy:wizard',
            '_step' => 0,
            '_format'     => 'index'
        ], [
            '_format' => DASHBOARD_VIEWS,
            '_step' => '0|1|2|3|4'
        ]));

$collection->add(
    'course2_group_study_update',
    new Route(
        '/course/2/lesson/7/update',
        [
            '_controller' => 'Course2Bundle:GroupStudy:update'
        ],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add('course2_teaching', new Route('/course/2/lesson/8/step/{_step}/{_format}', [
            '_controller' => 'Course2Bundle:Teaching:wizard',
            '_step' => 0,
            '_format'     => 'index'
        ], [
            '_format' => DASHBOARD_VIEWS,
            '_step' => '0|1|2|3|4'
        ]));

$collection->add(
    'course2_teaching_update',
    new Route(
        '/course/2/lesson/8/update',
        [
            '_controller' => 'Course2Bundle:Teaching:update'
        ],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add('course2_active_reading', new Route('/course/2/lesson/9/step/{_step}/{_format}', [
            '_controller' => 'Course2Bundle:ActiveReading:wizard',
            '_step' => 0,
            '_format'     => 'index'
        ], [
            '_format' => DASHBOARD_VIEWS,
            '_step' => '0|1|2|3|4'
        ]));

$collection->add(
    'course2_active_reading_update',
    new Route(
        '/course/2/lesson/9/update',
        [
            '_controller' => 'Course2Bundle:ActiveReading:update'
        ],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add('course2_spaced_repetition', new Route('/course/2/lesson/10/step/{_step}/{_format}', [
            '_controller' => 'Course2Bundle:SpacedRepetition:wizard',
            '_step' => 0,
            '_format'     => 'index'
        ], [
            '_format' => DASHBOARD_VIEWS,
            '_step' => '0|1|2|3|4'
        ]));

$collection->add(
    'course2_spaced_repetition_update',
    new Route(
        '/course/2/lesson/10/update',
        [
            '_controller' => 'Course2Bundle:SpacedRepetition:update'
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
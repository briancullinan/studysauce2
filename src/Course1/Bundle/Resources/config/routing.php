<?php

// app/config/routing.php
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();

$collection->add('course1_introduction', new Route('/course/1/lesson/1/step/{_step}/{_format}', [
            '_controller' => 'Course1Bundle:Introduction:wizard',
            '_step' => 0,
            '_format'     => 'index'
        ], [
            '_format' => DASHBOARD_VIEWS,
            '_step' => '0|1|2|3|4'
        ]));

$collection->add(
    'course1_introduction_update',
    new Route(
        '/course/1/lesson/1/update',
        [
            '_controller' => 'Course1Bundle:Introduction:update'
        ],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add('course1_setting_goals', new Route('/course/1/lesson/2/step/{_step}/{_format}', [
            '_controller' => 'Course1Bundle:SettingGoals:wizard',
            '_step' => 0,
            '_format'     => 'index'
        ], [
            '_format' => DASHBOARD_VIEWS,
            '_step' => '0|1|2|3|4'
        ]));

$collection->add(
    'course1_setting_goals_update',
    new Route(
        '/course/1/lesson/2/update',
        [
            '_controller' => 'Course1Bundle:SettingGoals:update'
        ],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add('course1_procrastination', new Route('/course/1/lesson/4/step/{_step}/{_format}', [
            '_controller' => 'Course1Bundle:Procrastination:wizard',
            '_step' => 0,
            '_format'     => 'index'
        ], [
            '_format' => DASHBOARD_VIEWS,
            '_step' => '0|1|2|3|4'
        ]));

$collection->add(
    'course1_procrastination_update',
    new Route(
        '/course/1/lesson/4/update',
        [
            '_controller' => 'Course1Bundle:Procrastination:update'
        ],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add('course1_distractions', new Route('/course/1/lesson/3/step/{_step}/{_format}', [
            '_controller' => 'Course1Bundle:Distractions:wizard',
            '_step' => 0,
            '_format'     => 'index'
        ], [
            '_format' => DASHBOARD_VIEWS,
            '_step' => '0|1|2|3|4'
        ]));

$collection->add(
    'course1_distractions_update',
    new Route(
        '/course/1/lesson/3/update',
        [
            '_controller' => 'Course1Bundle:Distractions:update'
        ],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add('course1_environment', new Route('/course/1/lesson/5/step/{_step}/{_format}', [
            '_controller' => 'Course1Bundle:Environment:wizard',
            '_step' => 0,
            '_format'     => 'index'
        ], [
            '_format' => DASHBOARD_VIEWS,
            '_step' => '0|1|2|3|4'
        ]));

$collection->add(
    'course1_environment_update',
    new Route(
        '/course/1/lesson/5/update',
        [
            '_controller' => 'Course1Bundle:Environment:update'
        ],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add('course1_partners', new Route('/course/1/lesson/6/step/{_step}/{_format}', [
            '_controller' => 'Course1Bundle:Partners:wizard',
            '_step' => 0,
            '_format'     => 'index'
        ], [
            '_format' => DASHBOARD_VIEWS,
            '_step' => '0|1|2|3|4'
        ]));

$collection->add(
    'course1_partners_update',
    new Route(
        '/course/1/lesson/6/update',
        [
            '_controller' => 'Course1Bundle:Partners:update'
        ],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add('course1_upgrade', new Route('/course/1/lesson/7/step/{_step}/{_format}', [
            '_controller' => 'Course1Bundle:Upgrade:wizard',
            '_step' => 0,
            '_format'     => 'index'
        ], [
            '_format' => DASHBOARD_VIEWS,
            '_step' => '0|1|2|3|4'
        ]));

$collection->add(
    'course1_upgrade_update',
    new Route(
        '/course/1/lesson/7/update',
        [
            '_controller' => 'Course1Bundle:Upgrade:update'
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
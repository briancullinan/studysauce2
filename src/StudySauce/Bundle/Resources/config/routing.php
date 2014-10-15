<?php
define('DASHBOARD_VIEWS', 'index|tab|json|funnel');

// app/config/routing.php
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();

$collection->add(
    '_welcome',
    new Route(
        '/', [
            '_controller' => 'StudySauceBundle:Landing:index',
        ]
    )
);

$collection->add(
    'home',
    new Route(
        '/home/{_format}', [
            '_controller' => 'StudySauceBundle:Home:index',
            '_format' => 'index'
        ], [
            '_format' => DASHBOARD_VIEWS,
        ]
    )
);

$collection->add(
    'schedule',
    new Route(
        '/schedule/{_format}', [
            '_controller' => 'StudySauceBundle:Schedule:index',
            '_format' => 'index'
        ], [
            '_format' => DASHBOARD_VIEWS,
        ]
    )
);

$collection->add(
    'institutions',
    new Route(
        '/institutions',
        [
            '_controller' => 'StudySauceBundle:Schedule:institutions'
        ],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add(
    'update_schedule',
    new Route(
        '/schedule/update',
        [
            '_controller' => 'StudySauceBundle:Schedule:update'
        ],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add(
    'remove_schedule',
    new Route(
        '/schedule/remove',
        [
            '_controller' => 'StudySauceBundle:Schedule:remove'
        ],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add(
    'goals',
    new Route(
        '/goals/{_format}', [
            '_controller' => 'StudySauceBundle:Goals:index',
            '_format' => 'index',
        ], [
            '_format' => DASHBOARD_VIEWS,
        ]
    )
);

$collection->add(
    'update_goals',
    new Route(
        '/goals/update',
        [
            '_controller' => 'StudySauceBundle:Goals:update'
        ],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add(
    'checkin',
    new Route(
        '/checkin/{_format}', [
            '_controller' => 'StudySauceBundle:Checkin:index',
            '_format' => 'index',
        ], [
            '_format' => DASHBOARD_VIEWS,
        ]
    )
);

$collection->add(
    'checkin_update',
    new Route(
        '/checkin/update', [
            '_controller' => 'StudySauceBundle:Checkin:update'
        ],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add(
    'metrics',
    new Route(
        '/metrics/{_format}', [
            '_controller' => 'StudySauceBundle:Metrics:index',
            '_format' => 'index',
        ], [
            '_format' => DASHBOARD_VIEWS,
        ]
    )
);

$collection->add(
    'deadlines',
    new Route(
        '/deadlines/{_format}', [
            '_controller' => 'StudySauceBundle:Deadlines:index',
            '_format' => 'index',
        ], [
            '_format' => DASHBOARD_VIEWS,
        ]
    )
);

$collection->add(
    'update_deadlines',
    new Route(
        '/deadlines/update',
        [
            '_controller' => 'StudySauceBundle:Deadlines:update'
        ],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add(
    'remove_deadlines',
    new Route(
        '/deadlines/remove',
        [
            '_controller' => 'StudySauceBundle:Deadlines:remove'
        ],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add(
    'partner',
    new Route(
        '/partner/{_format}', [
            '_controller' => 'StudySauceBundle:Partner:index',
            '_format' => 'index',
        ], [
            '_format' => DASHBOARD_VIEWS,
        ]
    )
);

$collection->add(
    'update_partner',
    new Route(
        '/partner/update',
        [
            '_controller' => 'StudySauceBundle:Partner:update'
        ],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add(
    'plan',
    new Route(
        '/plan/{_format}', [
            '_controller' => 'StudySauceBundle:Plan:index',
            '_format' => 'index',
        ], [
            '_format' => DASHBOARD_VIEWS,
        ]
    )
);

$collection->add(
    'profile',
    new Route(
        '/profile/{_format}', [
            '_controller' => 'StudySauceBundle:Profile:index',
            '_format' => 'index',
        ], [
            '_format' => DASHBOARD_VIEWS,
        ]
    )
);

$collection->add(
    'premium',
    new Route(
        '/premium/{_format}', [
            '_controller' => 'StudySauceBundle:Premium:index',
            '_format' => 'index',
        ], [
            '_format' => DASHBOARD_VIEWS,
        ]
    )
);

$collection->add(
    'tips',
    new Route(
        '/tips/{_format}', [
            '_controller' => 'StudySauceBundle:Tips:index',
            '_format' => 'index',
        ], [
            '_format' => DASHBOARD_VIEWS,
        ]
    )
);

$collection->add(
    'account',
    new Route(
        '/account/{_format}', [
            '_controller' => 'StudySauceBundle:Account:index',
            '_format' => 'index',
        ], [
            '_format' => DASHBOARD_VIEWS,
        ]
    )
);

$collection->add(
    'account_update',
    new Route(
        '/account/update', [
            '_controller' => 'StudySauceBundle:Account:update'
        ],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add(
    'account_remove',
    new Route(
        '/account/remove', [
            '_controller' => 'StudySauceBundle:Account:remove'
        ],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add(
    'account_login',
    new Route(
        '/account/login', [
            '_controller' => 'StudySauceBundle:Account:login',
            '_format' => 'funnel'
        ], [
            '_format' => DASHBOARD_VIEWS,
        ]
    )
);

$collection->add('account_logout', new Route('/account/logout'));

$collection->add(
    'account_auth',
    new Route(
        '/account/authenticate', [
            '_controller' => 'StudySauceBundle:Account:authenticate'
        ],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add(
    'account_register',
    new Route(
        '/account/register', [
            '_controller' => 'StudySauceBundle:Account:register',
            '_format' => 'funnel'
        ], [
            '_format' => DASHBOARD_VIEWS,
        ]
    )
);

$collection->add(
    'account_denied',
    new Route(
        '/account/denied', [
            '_controller' => 'StudySauceBundle:Account:denied',
            '_format' => 'funnel'
        ], [
            '_format' => DASHBOARD_VIEWS,
        ]
    )
);

$collection->add(
    'account_create',
    new Route(
        '/account/create', [
            '_controller' => 'StudySauceBundle:Account:create'
        ],
        [],
        [],
        '',
        [],
        [],
        'request.isXmlHttpRequest()'
    )
);

$collection->add(
    'courses',
    new Route(
        '/courses/{_format}', [
            '_controller' => 'StudySauceBundle:Course:index',
            '_format' => 'index'
        ], [
            '_format' => DASHBOARD_VIEWS,
        ]
    )
);

/*
$collection->add('course', new Route('/course/{_course}/{_format}', array(
            '_controller' => 'StudySauceBundle:Courses:Course{_course}:index',
            '_format'     => 'dashboard'
        )));

$collection->add('default', new Route('/{_controller}'));

$acmeHello = $loader->import('@StudySauceBundle/Resources/public/images/', 'directory');
$acmeHello->addPrefix('/bundles/studysauce/images/');
$collection->addCollection($acmeHello);
*/

return $collection;
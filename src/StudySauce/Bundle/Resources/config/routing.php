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
    'terms',
    new Route(
        '/terms/{_format}', [
            '_controller' => 'StudySauceBundle:Landing:terms',
            '_format' => 'funnel'
        ], [
            '_format' => DASHBOARD_VIEWS,
        ]
    )
);

$collection->add(
    'privacy',
    new Route(
        '/privacy/{_format}', [
            '_controller' => 'StudySauceBundle:Landing:privacy',
            '_format' => 'funnel'
        ], [
            '_format' => DASHBOARD_VIEWS,
        ]
    )
);

$collection->add(
    'about',
    new Route(
        '/about/{_format}', [
            '_controller' => 'StudySauceBundle:Landing:about',
            '_format' => 'funnel'
        ], [
            '_format' => DASHBOARD_VIEWS,
        ]
    )
);

$collection->add(
    'refund',
    new Route(
        '/refund/{_format}', [
            '_controller' => 'StudySauceBundle:Landing:refund',
            '_format' => 'funnel'
        ], [
            '_format' => DASHBOARD_VIEWS,
        ]
    )
);

$collection->add(
    '_visit',
    new Route(
        '/_visit', [
            '_controller' => 'StudySauceBundle:Landing:visit',
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
    'partner_welcome',
    new Route(
        '/partners/{_code}', [
            '_controller' => 'StudySauceBundle:Landing:partners',
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
    'goals_partner',
    new Route(
        '/goals/{_format}/{_user}', [
            '_controller' => 'StudySauceBundle:Goals:partner',
            '_format' => 'funnel',
        ], [
            '_format' => DASHBOARD_VIEWS,
            '_user' => '[0-9]+'
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
    'claim_goals',
    new Route(
        '/goals/claim', [
            '_controller' => 'StudySauceBundle:Goals:notifyClaim'
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
    'metrics_partner',
    new Route(
        '/metrics/{_format}/{_user}', [
            '_controller' => 'StudySauceBundle:Metrics:partner',
            '_format' => 'funnel',
        ], [
            '_format' => DASHBOARD_VIEWS,
            '_user' => '[0-9]+'
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
    'deadlines_partner',
    new Route(
        '/deadlines/{_format}/{_user}', [
            '_controller' => 'StudySauceBundle:Deadlines:partner',
            '_format' => 'funnel',
        ], [
            '_format' => DASHBOARD_VIEWS,
            '_user' => '[0-9]+'
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
    'plan_week',
    new Route(
        '/plan/{_week}/{_format}', [
            '_controller' => 'StudySauceBundle:Plan:index',
            '_format' => 'index',
            '_week' => ''
        ], [
            '_format' => DASHBOARD_VIEWS,
            '_week' => '^$|[0-9]+|[0-9]{4}-[0-9]{2}-[0-9]{2}T00:00:00\.000Z'
        ]
    )
);

$collection->add(
    'plan_partner',
    new Route(
        '/plan/{_week}/{_format}/{_user}', [
            '_controller' => 'StudySauceBundle:Plan:partner',
            '_format' => 'funnel',
        ], [
            '_format' => DASHBOARD_VIEWS,
            '_user' => '[0-9]+'
        ]
    )
);

$collection->add(
    'plan_strategy',
    new Route(
        '/plan/strategy',
        [
            '_controller' => 'StudySauceBundle:Plan:updateStrategy'
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
    'login',
    new Route(
        '/login', [
            '_controller' => 'StudySauceBundle:Account:login',
            '_format' => 'funnel'
        ], [
            '_format' => DASHBOARD_VIEWS,
        ]
    )
);

$collection->add('facebook_login', new Route('/login/facebook'));

$collection->add('google_login', new Route('/login/google'));

$collection->add(
    'account_auth',
    new Route(
        '/authenticate', [
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
    'register',
    new Route(
        '/register', [
            '_controller' => 'StudySauceBundle:Account:register',
            '_format' => 'funnel'
        ], [
            '_format' => DASHBOARD_VIEWS,
        ]
    )
);

$collection->add('logout', new Route('/logout'));

$collection->add(
    'account_denied',
    new Route(
        '/denied', [
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

$collection->add(
    'file_create',
    new Route(
        '/file/create', [
            '_controller' => 'StudySauceBundle:File:create'
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
    'uploads_partner',
    new Route(
        '/file/{_format}/{_user}', [
            '_controller' => 'StudySauceBundle:File:partner',
            '_format' => 'funnel'
        ], [
            '_format' => DASHBOARD_VIEWS,
            '_user' => '[0-9]+'
        ]
    )
);

$collection->add(
    'checkout',
    new Route(
        '/checkout/{_format}', [
            '_controller' => 'StudySauceBundle:Buy:checkout',
            '_format' => 'funnel'
        ], [
            '_format' => DASHBOARD_VIEWS,
        ]
    )
);

$collection->add(
    'checkout_pay',
    new Route(
        '/checkout/pay', [
            '_controller' => 'StudySauceBundle:Buy:pay'
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
    'userlist',
    new Route(
        '/userlist', [
            '_controller' => 'StudySauceBundle:Partner:userlist',
            '_format' => 'funnel'
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
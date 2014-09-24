<?php
define('DASHBOARD_VIEWS', 'index|tab|json');

// app/config/routing.php
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();
$collection->add(
    '_welcome',
    new Route(
        '/', array(
            '_controller' => 'StudySauceBundle:Landing:index',
        )
    )
);

$collection->add(
    'schedule',
    new Route(
        '/schedule/{_format}', array(
            '_controller' => 'StudySauceBundle:Schedule:index',
            '_format' => 'index'
        ), array(
            '_format' => DASHBOARD_VIEWS,
        )
    )
);

$collection->add(
    'goals',
    new Route(
        '/goals/{_format}', array(
            '_controller' => 'StudySauceBundle:Goals:index',
            '_format' => 'index',
        ), array(
            '_format' => DASHBOARD_VIEWS,
        )
    )
);

$collection->add(
    'checkin',
    new Route(
        '/checkin/{_format}', array(
            '_controller' => 'StudySauceBundle:Checkin:index',
            '_format' => 'index',
        ), array(
            '_format' => DASHBOARD_VIEWS,
        )
    )
);

$collection->add(
    'metrics',
    new Route(
        '/metrics/{_format}', array(
            '_controller' => 'StudySauceBundle:Metrics:index',
            '_format' => 'index',
        ), array(
            '_format' => DASHBOARD_VIEWS,
        )
    )
);

$collection->add(
    'deadlines',
    new Route(
        '/deadlines/{_format}', array(
            '_controller' => 'StudySauceBundle:Deadlines:index',
            '_format' => 'index',
        ), array(
            '_format' => DASHBOARD_VIEWS,
        )
    )
);

$collection->add(
    'partner',
    new Route(
        '/partner/{_format}', array(
            '_controller' => 'StudySauceBundle:Partner:index',
            '_format' => 'index',
        ), array(
            '_format' => DASHBOARD_VIEWS,
        )
    )
);

$collection->add(
    'plan',
    new Route(
        '/plan/{_format}', array(
            '_controller' => 'StudySauceBundle:Plan:index',
            '_format' => 'index',
        ), array(
            '_format' => DASHBOARD_VIEWS,
        )
    )
);

$collection->add(
    'profile',
    new Route(
        '/profile/{_format}', array(
            '_controller' => 'StudySauceBundle:Profile:index',
            '_format' => 'index',
        ), array(
            '_format' => DASHBOARD_VIEWS,
        )
    )
);

$collection->add(
    'premium',
    new Route(
        '/premium/{_format}', array(
            '_controller' => 'StudySauceBundle:Premium:index',
            '_format' => 'index',
        ), array(
            '_format' => DASHBOARD_VIEWS,
        )
    )
);

$collection->add(
    'tips',
    new Route(
        '/tips/{_format}', array(
            '_controller' => 'StudySauceBundle:Tips:index',
            '_format' => 'index',
        ), array(
            '_format' => DASHBOARD_VIEWS,
        )
    )
);

$collection->add(
    'account',
    new Route(
        '/account/{_format}', array(
            '_controller' => 'StudySauceBundle:Account:index',
            '_format' => 'index',
        ), array(
            '_format' => DASHBOARD_VIEWS,
        )
    )
);

$collection->add(
    'courses',
    new Route(
        '/courses/{_format}', array(
            '_controller' => 'StudySauceBundle:Course:index',
            '_format' => 'index'
        ), array(
            '_format' => DASHBOARD_VIEWS,
        )
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
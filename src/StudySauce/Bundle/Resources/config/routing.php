<?php

// app/config/routing.php
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();
$collection->add('_welcome', new Route('/', array(
    '_controller' => 'StudySauceBundle:Landing:index',
)));

$collection->add('schedule', new Route('/schedule/{_format}', array(
    '_controller' => 'StudySauceBundle:Schedule:index',
    '_format'     => 'dashboard',
)));

$collection->add('goals', new Route('/goals/{_format}', array(
    '_controller' => 'StudySauceBundle:Goals:index',
    '_format'     => 'dashboard',
)));

$collection->add('checkin', new Route('/checkin/{_format}', array(
    '_controller' => 'StudySauceBundle:Checkin:index',
    '_format'     => 'dashboard',
)));

$collection->add('metrics', new Route('/metrics/{_format}', array(
    '_controller' => 'StudySauceBundle:Metrics:index',
    '_format'     => 'dashboard',
)));

$collection->add('deadlines', new Route('/deadlines/{_format}', array(
    '_controller' => 'StudySauceBundle:Deadlines:index',
    '_format'     => 'dashboard',
)));

$collection->add('partner', new Route('/partner/{_format}', array(
    '_controller' => 'StudySauceBundle:Partner:index',
    '_format'     => 'dashboard',
)));

$collection->add('plan', new Route('/plan/{_format}', array(
    '_controller' => 'StudySauceBundle:Plan:index',
    '_format'     => 'dashboard',
)));

$collection->add('profile', new Route('/profile/{_format}', array(
    '_controller' => 'StudySauceBundle:Profile:index',
    '_format'     => 'dashboard',
)));

$collection->add('premium', new Route('/premium/{_format}', array(
    '_controller' => 'StudySauceBundle:Premium:index',
    '_format'     => 'dashboard',
)));

$collection->add('tips', new Route('/tips/{_format}', array(
    '_controller' => 'StudySauceBundle:Tips:index',
    '_format'     => 'dashboard',
)));

$collection->add('account', new Route('/account/{_format}', array(
    '_controller' => 'StudySauceBundle:Account:index',
    '_format'     => 'dashboard',
)));

$collection->add('courses', new Route('/courses/{_format}', array(
            '_controller' => 'StudySauceBundle:Course:index',
            '_format'     => 'dashboard'
)));

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
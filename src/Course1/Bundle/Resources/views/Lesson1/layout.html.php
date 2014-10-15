<?php

use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Router;

/** @var Router $router */
$router = $this->container->get('router');

/** @var RouteCollection $collection */
$collection = $router->getRouteCollection();

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

global $courseIncluded;
if (!$courseIncluded) {
    $courseIncluded = true;

    $view['slots']->start('lesson-body');
    $view['slots']->output('body');
    $view['slots']->stop();

    $controller = $app->getRequest()->get('_controller');
    if($app->getRequest()->get('_format') == 'index' && ($controller == 'Course1\Bundle\Controller\Lesson1Controller::wizardAction' ||
            $controller == 'Course1\Bundle\Controller\Lesson2Controller::wizardAction' || $controller == 'Course1\Bundle\Controller\Lesson3Controller::wizardAction')) {
        $view['slots']->start('sincludes');
        // TODO: include courses from the index page
        if ($app->getRequest()->get('_step') != 3) {
            echo $view['actions']->render(
                new ControllerReference($controller, ['_step' => 3, '_format' => 'tab']),
                ['strategy' => 'sinclude']
            );
        }

        if ($app->getRequest()->get('_step') != 2) {
            echo $view['actions']->render(
                new ControllerReference($controller, ['_step' => 2, '_format' => 'tab']),
                ['strategy' => 'sinclude']
            );
        }

        if ($app->getRequest()->get('_step') != 1) {
            echo $view['actions']->render(
                new ControllerReference($controller, ['_step' => 1, '_format' => 'tab']),
                ['strategy' => 'sinclude']
            );
        }

        if ($app->getRequest()->get('_step') != 0) {
            echo $view['actions']->render(
                new ControllerReference($controller, ['_step' => 0, '_format' => 'tab']),
                ['strategy' => 'sinclude']
            );
        }

        if ($app->getRequest()->get('_step') != 4) {
            echo $view['actions']->render(
                new ControllerReference($controller, ['_step' => 4, '_format' => 'tab']),
                ['strategy' => 'sinclude']
            );
        }
        $view['slots']->stop();
    }

    $view['slots']->start('body');
    $view['slots']->output('lesson-body');
    $view['slots']->stop();

    $view['slots']->start('stylesheets');
    foreach ($view['assetic']->stylesheets(
        [
            '@Course1Bundle/Resources/public/css/course1.css'
        ],
        [],
        ['output' => 'bundles/course1/css/*.css']
    ) as $url):
        ?>
        <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
    <?php endforeach;
    $view['slots']->stop();

    $view['slots']->start('javascripts');
    foreach ($view['assetic']->javascripts(
        [
            '@Course1Bundle/Resources/public/js/course1.js'
        ],
        [],
        ['output' => 'bundles/course1/js/*.js']
    ) as $url):
        ?>
        <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
    <?php endforeach;
    $view['slots']->stop();
}
else
{
    $view['slots']->start('stylesheets');
    $view['slots']->stop();


    $view['slots']->start('javascripts');
    $view['slots']->stop();
}

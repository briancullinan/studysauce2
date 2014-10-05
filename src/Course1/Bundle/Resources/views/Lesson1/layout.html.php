<?php

/** @var $router \Symfony\Component\Routing\Router */
use Symfony\Component\HttpKernel\Controller\ControllerReference;

$router = $this->container->get('router');

/** @var $collection \Symfony\Component\Routing\RouteCollection */
$collection = $router->getRouteCollection();

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

global $courseIncluded;
if (!$courseIncluded) {
    $courseIncluded = true;

    $view['slots']->start('lesson-body');
    $view['slots']->output('body');
    $view['slots']->stop();

    if($app->getRequest()->get('_format') == 'index') {
        $view['slots']->start('sincludes');
        // TODO: include courses from the index page
        if ($app->getRequest()->get('_step') != 3) {
            echo $view['actions']->render(
                new ControllerReference('Course1Bundle:Lesson1:wizard', ['_step' => 3, '_format' => 'tab']),
                ['strategy' => 'sinclude']
            );
        }

        if ($app->getRequest()->get('_step') != 2) {
            echo $view['actions']->render(
                new ControllerReference('Course1Bundle:Lesson1:wizard', ['_step' => 2, '_format' => 'tab']),
                ['strategy' => 'sinclude']
            );
        }

        if ($app->getRequest()->get('_step') != 1) {
            echo $view['actions']->render(
                new ControllerReference('Course1Bundle:Lesson1:wizard', ['_step' => 1, '_format' => 'tab']),
                ['strategy' => 'sinclude']
            );
        }

        if ($app->getRequest()->get('_step') != 0) {
            echo $view['actions']->render(
                new ControllerReference('Course1Bundle:Lesson1:wizard', ['_step' => 0, '_format' => 'tab']),
                ['strategy' => 'sinclude']
            );
        }

        if ($app->getRequest()->get('_step') != 4) {
            echo $view['actions']->render(
                new ControllerReference('Course1Bundle:Lesson1:wizard', ['_step' => 4, '_format' => 'tab']),
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

    $allRoutes = $collection->all();

//$routes = [];
    $callbackPaths = [];
    $callbackKeys = [];
    $callbackUri = [];

    /** @var $params \Symfony\Component\Routing\Route */
    foreach ($allRoutes as $route => $params) {
        $defaults = $params->getDefaults();
        $condition = $params->getCondition();
        $requirement = $params->getRequirement('_step');
        $path = $params->getPath();

        if (isset($defaults['_controller'])) {
            $controllerAction = explode(':', $defaults['_controller']);
            $controller = $controllerAction[0];

            // add lessons with multiple steps
            if (!empty($requirement) && is_numeric(explode('|', $requirement)[0])) {
                foreach (explode('|', $requirement) as $j) {
                    $key = $route . (intval($j) > 0 ? ('-step' . intval($j)) : '');
                    $callbackPaths[$key] = $router->generate(
                        $route,
                        ['_step' => intval($j), '_format' => 'tab']
                    );
                    $callbackKeys[] = $key;
                    $callbackUri[] = $router->generate($route, ['_step' => intval($j)]);
                }
            }
        }
    }
    ?>
    <script type="text/javascript">
        window.callbackPaths = $.extend(window.callbackPaths, JSON.parse('<?php print json_encode($callbackPaths); ?>'));
        window.callbackKeys = $.merge(window.callbackKeys, JSON.parse('<?php print json_encode($callbackKeys); ?>'));
        window.callbackUri = $.merge(window.callbackUri, JSON.parse('<?php print json_encode($callbackUri); ?>'));
    </script>
    <?php
    $view['slots']->stop();
}
else
{
    $view['slots']->start('stylesheets');
    $view['slots']->stop();


    $view['slots']->start('javascripts');
    $view['slots']->stop();
}

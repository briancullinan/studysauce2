<?php

/** @var $router \Symfony\Component\Routing\Router */
$router = $this->container->get('router');

/** @var $collection \Symfony\Component\Routing\RouteCollection */
$collection = $router->getRouteCollection();

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');
foreach ($view['assetic']->stylesheets([
        '@Course1Bundle/Resources/public/css/course1.css'
    ], [], ['output' => 'bundles/course1/css/*.css']) as $url):
    ?><link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>" />
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts([
        '@Course1Bundle/Resources/public/js/course1.js'
    ], [], ['output' => 'bundles/course1/js/*.js']) as $url):
    ?><script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
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
            foreach(explode('|', $requirement) as $j) {
                $callbackPaths[$route . (intval($j) > 0 ? ('-step' . intval($j)) : '')] = $router->generate($route, ['_step' => intval($j), '_format' => 'tab']);
                $callbackKeys[] = $route . (intval($j) > 0 ? ('-step' . intval($j)) : '');
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
<?php $view['slots']->stop() ?>


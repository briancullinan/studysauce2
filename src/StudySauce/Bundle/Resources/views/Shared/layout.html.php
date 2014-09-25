<?php
/** @var $view \Symfony\Bundle\FrameworkBundle\Templating\PhpEngine */

/** @var $router \Symfony\Component\Routing\Router */
$router = $this->container->get('router');

/** @var $collection \Symfony\Component\Routing\RouteCollection */
$collection = $router->getRouteCollection();


?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title><?php $view['slots']->output('title', 'Study Sauce | Discover the secret sauce to studying') ?></title>

    <?php foreach ($view['assetic']->stylesheets(
        array(
            '@StudySauceBundle/Resources/public/css/jquery-ui.min.css',
            '@StudySauceBundle/Resources/public/css/bootstrap.min.css',
            '@StudySauceBundle/Resources/public/css/selectize.default.css',
            '@StudySauceBundle/Resources/public/css/sauce.css',
            '@StudySauceBundle/Resources/public/css/dialog.css',
        ),
        array(),
        array('output' => 'bundles/studysauce/css/*.css')
    ) as $url):
        ?>
        <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>" />
    <?php endforeach; ?>

    <?php $view['slots']->output('stylesheets') ?>

    <?php
    $allRoutes = $collection->all();

    $routes = array();
    $callbackPaths = array();

    /** @var $params \Symfony\Component\Routing\Route */
    foreach ($allRoutes as $route => $params) {
        $defaults = $params->getDefaults();
        $condition = $params->getCondition();
        $path = $params->getPath();

        if (isset($defaults['_controller'])) {
            $controllerAction = explode(':', $defaults['_controller']);
            $controller = $controllerAction[0];

            if (!isset($routes[$controller])) {
                $routes[$controller] = array();
            }
            if (preg_match('/(^|\s)request.isXmlHttpRequest\(\)(\s+|$)/i', $condition)) {
                $callbackPaths[$route] = $router->generate($route);
            }

            $routes[$controller][] = $route;
        }
    }

    ?>
    <script type="text/javascript">
        window.callbackPaths = JSON.parse('<?php print json_encode($callbackPaths); ?>');
    </script>
</head>
<body class="<?php $view['slots']->output('classes') ?>">
<?php $view['slots']->output('body') ?>
<?php foreach ($view['assetic']->javascripts(
    array(
        '@StudySauceBundle/Resources/public/js/jquery-2.1.1.min.js',
        '@StudySauceBundle/Resources/public/js/jquery-ui.min.js',
        '@StudySauceBundle/Resources/public/js/bootstrap.min.js',
        '@StudySauceBundle/Resources/public/js/sinclude.js'
    ),
    array(),
    array('output' => 'bundles/studysauce/js/*.js')
) as $url):
    ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach; ?>

<?php $view['slots']->output('javascripts') ?>
</body>
</html>
<?php
/** @var $view \Symfony\Bundle\FrameworkBundle\Templating\PhpEngine */

/** @var $router \Symfony\Component\Routing\Router */
$router = $this->container->get('router');

/** @var $collection \Symfony\Component\Routing\RouteCollection */
$collection = $router->getRouteCollection();

/** @var $app \Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables */

?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=no">
    <link rel="apple-touch-icon-precomposed" href="<?php print $view['assets']->getUrl('bundles/studysauce/images/studysauce-google-page.png'); ?>" />
    <link rel="shortcut icon" href="<?php print $view['assets']->getUrl('bundles/studysauce/images/favicon.ico'); ?>" />
    <link rel="apple-touch-icon" href="<?php print $view['assets']->getUrl('bundles/studysauce/images/studysauce-google-page.png'); ?>" />
    <link rel="image_src" href="<?php print $view['assets']->getUrl('bundles/studysauce/images/studysauce-google-page.png'); ?>" />
    <meta name="description" content="Study Sauce teaches you the most effective study methods and provides you the tools to make the most of your study time." />
    <meta property="og:image" content="<?php print $view['assets']->getUrl('bundles/studysauce/images/studysauce-google-page.png'); ?>" />
    <title><?php $view['slots']->output('title', 'Study Sauce | Discover the secret sauce to studying') ?></title>

    <?php foreach ($view['assetic']->stylesheets([
            '@StudySauceBundle/Resources/public/css/jquery-ui.min.css',
            '@StudySauceBundle/Resources/public/css/bootstrap.min.css',
            '@StudySauceBundle/Resources/public/css/selectize.default.css',
            '@StudySauceBundle/Resources/public/css/fonts.css',
            '@StudySauceBundle/Resources/public/css/sauce.css',
            '@StudySauceBundle/Resources/public/css/dialog.css',
        ],
        [],
        ['output' => 'bundles/studysauce/css/*.css']
    ) as $url):
        ?>
        <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>" />
    <?php endforeach;

    $view['slots']->output('stylesheets');


    $allRoutes = $collection->all();

    //$routes = [];
    $callbackPaths = [];
    $callbackKeys = [];
    $callbackUri = [];

    /** @var $params \Symfony\Component\Routing\Route */
    foreach ($allRoutes as $route => $params) {
        $defaults = $params->getDefaults();
        $condition = $params->getCondition();
        $format = $params->getRequirement('_format');
        $step = $params->getRequirement('_step');
        $path = $params->getPath();

        if (isset($defaults['_controller'])) {
            $controllerAction = explode(':', $defaults['_controller']);
            $controller = $controllerAction[0];

            if (preg_match('/(^|\s)request.isXmlHttpRequest\(\)(\s+|$)/i', $condition)) {
                $callbackPaths[$route] = $router->generate($route);
                $callbackKeys[] = $route;
                $callbackUri[] = $router->generate($route);
            }

            if (!empty($format) && strpos($format, 'tab') > -1) {
                $callbackPaths[$route] = $router->generate($route, ['_format' => 'tab']);
                $callbackKeys[] = $route;
                $callbackUri[] = $router->generate($route);
            }

            if (!empty($step) && is_numeric(explode('|', $step)[0])) {
                foreach (explode('|', $step) as $j) {
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
        window.callbackPaths = JSON.parse('<?php print json_encode($callbackPaths); ?>');
        window.callbackKeys = JSON.parse('<?php print json_encode($callbackKeys); ?>');
        window.callbackUri = JSON.parse('<?php print json_encode($callbackUri); ?>');
    </script>
</head>
<body class="<?php $view['slots']->output('classes') ?>">
<?php $view['slots']->output('body') ?>
<script type="text/javascript" src="https://www.youtube.com/iframe_api"></script>
<?php foreach ($view['assetic']->javascripts([
        '@StudySauceBundle/Resources/public/js/jquery-2.1.1.min.js',
        '@StudySauceBundle/Resources/public/js/jquery-ui.min.js',
        '@StudySauceBundle/Resources/public/js/bootstrap.min.js',
        '@StudySauceBundle/Resources/public/js/sinclude.js'
    ],
    [],
    ['output' => 'bundles/studysauce/js/*.js']
) as $url):
    ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;

$view['slots']->output('javascripts');
$view['slots']->output('sincludes');

echo $view->render('StudySauceBundle:Shared:footer.html.php');
?>
</body>
</html>
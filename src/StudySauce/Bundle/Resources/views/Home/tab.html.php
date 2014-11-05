<?php
use Symfony\Component\HttpKernel\Controller\ControllerReference;

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');

foreach ($view['assetic']->stylesheets(
    [
        '@StudySauceBundle/Resources/public/css/home.css',
        '@StudySauceBundle/Resources/public/css/deadlines.css',
        '@StudySauceBundle/Resources/public/css/goals.css',
        '@StudySauceBundle/Resources/public/css/plan.css',
        '@StudySauceBundle/Resources/public/css/tipsy.css',
        '@StudySauceBundle/Resources/public/css/metrics.css',
    ],
    [],
    ['output' => 'bundles/studysauce/css/*.css']
) as $url):
    ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;

$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts(
    [
        '@StudySauceBundle/Resources/public/js/home.js',
    ],
    [],
    ['output' => 'bundles/studysauce/js/*.js']
) as $url):
    ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
foreach ($view['assetic']->javascripts(
    [
        '@metrics'
    ],
    [],
    ['output' => 'bundles/studysauce/js/*.js']
) as $url):
    ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
foreach ($view['assetic']->javascripts(
    [
        '@checkin',
    ],
    [],
    ['output' => 'bundles/studysauce/js/*.js']
) as $url):
    ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('body'); ?>

<div class="panel-pane" id="home">
    <?php
    print $view['actions']->render(new ControllerReference('StudySauceBundle:Course:widget'));
    print $view['actions']->render(new ControllerReference('StudySauceBundle:Goals:widget'));
    print $view['actions']->render(new ControllerReference('StudySauceBundle:Deadlines:widget'));
    print $view['actions']->render(new ControllerReference('StudySauceBundle:Metrics:widget'));
    print $view['actions']->render(new ControllerReference('StudySauceBundle:Plan:widget'));
    print $view['actions']->render(new ControllerReference('StudySauceBundle:Checkin:widget'));
    ?>
</div>

<?php $view['slots']->stop();

$view['slots']->start('sincludes');

$view['slots']->stop();

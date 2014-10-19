<?php
use Symfony\Component\HttpKernel\Controller\ControllerReference;

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');

foreach ($view['assetic']->stylesheets(
    [
        '@StudySauceBundle/Resources/public/css/home.css',
        '@StudySauceBundle/Resources/public/css/deadlines.css',
        '@StudySauceBundle/Resources/public/css/goals.css',
        '@StudySauceBundle/Resources/public/css/checkin.css',
        '@StudySauceBundle/Resources/public/css/clock.css',
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
        '@StudySauceBundle/Resources/public/js/d3.v3.min.js',
        '@StudySauceBundle/Resources/public/js/jquery.tipsy.js',
        '@StudySauceBundle/Resources/public/js/checkin.js',
        '@StudySauceBundle/Resources/public/js/metrics.js'
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
    <div class="widget-wrapper">
        <div class="widget course-widget">
            <h3>15% of course complete</h3>
            <div class="percent">
                <?php foreach ($view['assetic']->image(
                    ['@StudySauceBundle/Resources/public/images/logo_middle_transparent.png'],
                    [],
                    ['output' => 'bundles/studysauce/images/*']
                ) as $url): ?>
                    <img width="150" height="150" src="<?php echo $view->escape($url) ?>" alt="LOGO"/>
                <?php endforeach; ?>
                <div class="percent-background">&nbsp;</div>
                <div class="percent-bars">&nbsp;</div>
            </div>
            <div class="highlighted-link"><a href="" class="more">Next module</a></div>
        </div>
    </div>
    
    <?php
    print $view['actions']->render(new ControllerReference('StudySauceBundle:Goals:widget'));
    print $view['actions']->render(new ControllerReference('StudySauceBundle:Metrics:widget'));
    print $view['actions']->render(new ControllerReference('StudySauceBundle:Plan:widget'));
    print $view['actions']->render(new ControllerReference('StudySauceBundle:Deadlines:widget'));
    print $view['actions']->render(new ControllerReference('StudySauceBundle:Checkin:widget'));
    ?>
</div>

<?php $view['slots']->stop();

$view['slots']->start('sincludes');
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:sdsmessages'), ['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:checklist'), ['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:checkinempty'), ['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:timerexpire'), ['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:mozart'), ['strategy' => 'sinclude']);
$view['slots']->stop();

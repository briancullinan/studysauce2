<?php
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

/** @var GlobalVariables $app */

$view->extend('StudySauceBundle:Shared:layout.html.php');

$view['slots']->start('classes') ?>landing-home<?php $view['slots']->stop();

$view['slots']->start('stylesheets');

foreach ($view['assetic']->stylesheets(
    [
        '@StudySauceBundle/Resources/public/css/video.css',
        '@StudySauceBundle/Resources/public/css/scr.css',
        '@StudySauceBundle/Resources/public/css/banner.css',
        '@StudySauceBundle/Resources/public/css/features.css',
        '@StudySauceBundle/Resources/public/css/testimony.css',
        '@StudySauceBundle/Resources/public/css/footer.css',
        '@StudySauceBundle/Resources/public/css/landing.css'
    ],
    [],
    ['output' => 'bundles/studysauce/css/*.css']
) as $url):
    ?>
    <link rel="stylesheet" href="<?php echo $view->escape($url) ?>" />
<?php endforeach;
foreach ($view['assetic']->stylesheets(['@TorchAndLaurelBundle/Resources/public/css/landing2.css'],[],['output' => 'bundles/torchandlaurel/css/*.css']) as $url):?>
    <link rel="stylesheet" href="<?php echo $view->escape($url) ?>" />
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts');

foreach ($view['assetic']->javascripts(['@landing_scripts'],[],['output' => 'bundles/studysauce/js/*.js']) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;

$view['slots']->stop();

$view['slots']->start('body');
echo $view->render('TorchAndLaurelBundle:Landing:video.html.php');
echo $view->render('TorchAndLaurelBundle:Landing:scr.html.php');
echo $view->render('StudySauceBundle:Landing:banner.html.php');
echo $view->render('TorchAndLaurelBundle:Landing:features.html.php');
echo $view->render('StudySauceBundle:Landing:testimony.html.php');
$view['slots']->stop();

$view['slots']->start('sincludes');
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:billParents1'));
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:billParents2'));
$view['slots']->stop();
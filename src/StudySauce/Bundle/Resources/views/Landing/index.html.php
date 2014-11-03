<?php
use Symfony\Component\HttpKernel\Controller\ControllerReference;

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
        '@StudySauceBundle/Resources/public/css/footer.css'
    ],
    [],
    ['output' => 'bundles/studysauce/css/*.css']
) as $url):
    ?>
    <link rel="stylesheet" href="<?php echo $view->escape($url) ?>" />
<?php endforeach;

$view['slots']->stop();

$view['slots']->start('javascripts');

foreach ($view['assetic']->javascripts(
    [
        '@StudySauceBundle/Resources/public/js/jquery.textfill.min.js',
        //'@StudySauceBundle/Resources/public/js/jquery.fittext.js',
        '@StudySauceBundle/Resources/public/js/landing.js',
        '@StudySauceBundle/Resources/public/js/sauce.js',
        '@StudySauceBundle/Resources/public/js/contact.js'
    ],
    [],
    ['output' => 'bundles/studysauce/js/*.js']
) as $url):
    ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;

$view['slots']->stop();

$view['slots']->start('body');
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Landing:video'));
$view['slots']->stop();

$view['slots']->start('sincludes');
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Landing:scr'), ['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Landing:banner'), ['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Landing:features'), ['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Landing:testimony'), ['strategy' => 'sinclude']);
$view['slots']->stop();

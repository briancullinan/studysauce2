
<?php
/** @var $view \Symfony\Bundle\FrameworkBundle\Templating\PhpEngine */
/** @var $app \Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables */

if($app->getRequest()->get('_format') == 'index') {
    $view->extend('StudySauceBundle:Shared:layout.html.php');
} ?>

<?php $view['slots']->start('classes') ?>dashboard-home<?php $view['slots']->stop() ?>

<?php
$view['slots']->start('tmp-stylesheets');
$view['slots']->output('stylesheets');
$view['slots']->stop();
$view['slots']->start('stylesheets'); ?>
<?php foreach ($view['assetic']->stylesheets([
        '@StudySauceBundle/Resources/public/css/pietimer.css',
        '@StudySauceBundle/Resources/public/css/header.css',
        '@StudySauceBundle/Resources/public/css/menu.css',
        '@StudySauceBundle/Resources/public/css/dashboard.css',
        '@StudySauceBundle/Resources/public/css/footer.css'
    ], [], ['output' => 'bundles/studysauce/css/*.css']) as $url):
    ?><link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>" />
<?php endforeach; ?>
<?php $view['slots']->output('tmp-stylesheets'); ?>
<?php $view['slots']->stop() ?>


<?php
$view['slots']->start('tmp-javascripts');
$view['slots']->output('javascripts');
$view['slots']->stop();
$view['slots']->start('javascripts'); ?>
<?php foreach ($view['assetic']->javascripts([
        '@StudySauceBundle/Resources/public/js/selectize.min.js',
        '@StudySauceBundle/Resources/public/js/jquery.scrollintoview.js',
        '@StudySauceBundle/Resources/public/js/jquery.pietimer.js',
        '@StudySauceBundle/Resources/public/js/jquery.plugin.js',
        '@StudySauceBundle/Resources/public/js/jquery.timeentry.js',
        '@StudySauceBundle/Resources/public/js/jquery.jplayer.min.js',
        '@StudySauceBundle/Resources/public/js/sauce.js',
        '@StudySauceBundle/Resources/public/js/contact.js'
    ], [], ['output' => 'bundles/studysauce/js/*.js']) as $url):
    ?><script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach; ?>
<?php $view['slots']->output('tmp-javascripts'); ?>
<?php $view['slots']->stop() ?>

<?php
$view['slots']->start('tmp-body');
$view['slots']->output('body');
$view['slots']->stop();
$view['slots']->start('body'); ?>

<?php if($app->getRequest()->get('_format') == 'index') { ?>
<?php echo $view->render('StudySauceBundle:Shared:header.html.php'); ?>
<?php echo $view->render('StudySauceBundle:Shared:menu.html.php'); ?>
<?php } ?>

<?php $view['slots']->output('tmp-body') ?>

<?php if($app->getRequest()->get('_format') == 'index') { ?>
<?php echo $view->render('StudySauceBundle:Shared:footer.html.php'); ?>
<?php } ?>

<?php $view['slots']->stop(); ?>

<?php

if($app->getRequest()->get('_format') == 'tab') {
    $view['slots']->output('stylesheets');
    $view['slots']->output('javascripts');
    $view['slots']->output('body');
}



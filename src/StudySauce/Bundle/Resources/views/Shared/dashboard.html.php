
<?php $view->extend('StudySauceBundle:Shared:layout.html.php') ?>

<?php $view['slots']->start('classes') ?>dashboard-home<?php $view['slots']->stop() ?>

<?php
$view['slots']->start('tmp-stylesheets');
$view['slots']->output('stylesheets');
$view['slots']->stop();
$view['slots']->start('stylesheets'); ?>
<?php foreach ($view['assetic']->stylesheets(array(
        '@StudySauceBundle/Resources/public/css/header.css',
        '@StudySauceBundle/Resources/public/css/menu.css',
        '@StudySauceBundle/Resources/public/css/dashboard.css',
        '@StudySauceBundle/Resources/public/css/footer.css'
    ), array(), array('output' => 'bundles/studysauce/css/*.css')) as $url):
    ?><link rel="stylesheet" href="<?php echo $view->escape($url) ?>" />
<?php endforeach; ?>
<?php $view['slots']->output('tmp-stylesheets'); ?>
<?php $view['slots']->stop() ?>


<?php $view['slots']->start('javascripts') ?>
<?php foreach ($view['assetic']->javascripts(array(
        '@StudySauceBundle/Resources/public/js/sauce.js',
        '@StudySauceBundle/Resources/public/js/contact.js'
    ), array(), array('output' => 'bundles/studysauce/js/*.js')) as $url):
    ?><script src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach; ?>
<?php $view['slots']->stop() ?>

<?php
$view['slots']->start('tmp-body');
$view['slots']->output('body');
$view['slots']->stop();
$view['slots']->start('body'); ?>

<?php echo $view->render('StudySauceBundle:Shared:header.html.php'); ?>

<?php echo $view->render('StudySauceBundle:Shared:menu.html.php'); ?>

<?php $view['slots']->output('tmp-body') ?>

<?php echo $view->render('StudySauceBundle:Shared:footer.html.php'); ?>

<?php $view['slots']->stop(); ?>

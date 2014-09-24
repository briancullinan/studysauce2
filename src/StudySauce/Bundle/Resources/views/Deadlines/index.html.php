
<?php $view->extend('StudySauceBundle:Shared:dashboard.html.php') ?>

<?php $view['slots']->start('stylesheets'); ?>
<?php foreach ($view['assetic']->stylesheets(array(
        '@StudySauceBundle/Resources/public/css/deadlines.css'
    ), array(), array('output' => 'bundles/studysauce/css/*.css')) as $url):
    ?><link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>" />
<?php endforeach; ?>
<?php $view['slots']->stop() ?>

<?php $view['slots']->start('javascripts'); ?>
<?php foreach ($view['assetic']->javascripts(array(
        '@StudySauceBundle/Resources/public/js/deadlines.js'
    ), array(), array('output' => 'bundles/studysauce/js/*.js')) as $url):
    ?><script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach; ?>
<?php $view['slots']->stop() ?>

<?php $view['slots']->start('body'); ?>

<?php echo $view->render('StudySauceBundle:Deadlines:tab.html.php'); ?>

<?php $view['slots']->stop(); ?>

<?php use Symfony\Component\HttpKernel\Controller\ControllerReference; ?>

<?php $view->extend('StudySauceBundle:Shared:dashboard.html.php') ?>

<?php $view['slots']->start('stylesheets'); ?>
<?php foreach ($view['assetic']->stylesheets(array(
        '@StudySauceBundle/Resources/public/css/goals.css'
    ), array(), array('output' => 'bundles/studysauce/css/*.css')) as $url):
    ?><link rel="stylesheet" href="<?php echo $view->escape($url) ?>" />
<?php endforeach; ?>
<?php $view['slots']->stop() ?>

<?php $view['slots']->start('body'); ?>

<?php echo $view->render('StudySauceBundle:Goals:tab.html.php'); ?>

<?php echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:achievement'), array('strategy' => 'hinclude')); ?>

<?php $view['slots']->stop(); ?>

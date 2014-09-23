<?php use Symfony\Component\HttpKernel\Controller\ControllerReference; ?>

<?php $view->extend('StudySauceBundle:Shared:dashboard.html.php') ?>

<?php $view['slots']->start('stylesheets'); ?>
<?php foreach ($view['assetic']->stylesheets(array(
        '@StudySauceBundle/Resources/public/css/schedule.css'
    ), array(), array('output' => 'bundles/studysauce/css/*.css')) as $url):
    ?><link rel="stylesheet" href="<?php echo $view->escape($url) ?>" />
<?php endforeach; ?>
<?php $view['slots']->stop() ?>

<?php $view['slots']->start('body'); ?>

<?php echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:building'), array('strategy' => 'hinclude')); ?>

<?php echo $view->render('StudySauceBundle:Schedule:tab.html.php'); ?>

<?php $view['slots']->stop(); ?>


<?php $view->extend('StudySauceBundle:Shared:dashboard.html.php') ?>

<?php $view['slots']->start('body'); ?>

<?php echo $view->render('StudySauceBundle:Metrics:tab.html.php'); ?>

<?php $view['slots']->stop(); ?>

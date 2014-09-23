
<?php $view->extend('StudySauceBundle:Shared:dashboard.html.php') ?>

<?php $view['slots']->start('body'); ?>

<?php echo $view->render('StudySauceBundle:Partner:tab.html.php'); ?>

<?php $view['slots']->stop(); ?>

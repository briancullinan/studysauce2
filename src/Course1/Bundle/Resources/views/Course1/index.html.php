
<?php $view->extend('StudySauceBundle:Shared:dashboard.html.php') ?>

<?php $view['slots']->start('body'); ?>

<?php echo $view->render('Course1Bundle:Course1:tab.html.php'); ?>

<?php $view['slots']->stop(); ?>

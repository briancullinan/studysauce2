<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php') ?>

<?php $view['slots']->start('modal-header') ?>
<a href="<?php print $view['router']->generate('schedule'); ?>">Check in to start tracking your study hours</a>
<?php $view['slots']->stop() ?>

<?php $view['slots']->start('modal-body') ?>

<?php $view['slots']->stop() ?>

<?php $view['slots']->start('modal-footer') ?>
<a href="<?php print $view['router']->generate('schedule'); ?>" class="btn btn-primary">Checkin</a>
<?php $view['slots']->stop() ?>


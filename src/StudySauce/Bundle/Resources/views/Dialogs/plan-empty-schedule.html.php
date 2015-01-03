<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
You have no activities scheduled.
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<h3><a href="<?php print $view['router']->generate('schedule'); ?>">Edit your schedule here.</a></h3>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer');

$view['slots']->stop() ?>


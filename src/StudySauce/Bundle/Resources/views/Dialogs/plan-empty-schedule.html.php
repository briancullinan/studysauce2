<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Please enter your class schedule to get started.
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<h3><a href="<?php print $view['router']->generate('schedule'); ?>">Create your class schedule here.</a></h3>
<?php $view['slots']->stop();

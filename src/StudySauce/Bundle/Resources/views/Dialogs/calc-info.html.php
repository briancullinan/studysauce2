<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
How to use the grade calculator:
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<strong>Note:</strong> If you would like to store individual assignment grades, divide the total percent of all assignments by the number of assignments in the class (i.e. Assignments are worth 20% and there are 10 of them, you would enter 2% for each assignment entered).
<?php $view['slots']->stop();

$view['slots']->start('modal-footer');

$view['slots']->stop() ?>

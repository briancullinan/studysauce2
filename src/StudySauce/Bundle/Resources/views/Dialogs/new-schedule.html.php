<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Would you like to create a new schedule?
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
Study Sauce can help you track your progress over multiple terms.  Use the Grade calculator to see your inputted grades from past terms.  Create a new schedule for each term so you can see how you have improved over time.
<?php $view['slots']->stop();

$view['slots']->start('modal-footer') ?>
<a href="#close" data-dismiss="modal" class="btn">No, don't show again</a>
<a href="#create-schedule" data-dismiss="modal" class="btn btn-primary">Yes, create a new schedule</a>
<?php $view['slots']->stop() ?>


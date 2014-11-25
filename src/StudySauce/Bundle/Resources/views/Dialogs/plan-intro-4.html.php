<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

 $view['slots']->start('modal-header') ?>
Tip #4 - Try out different study strategies
<?php $view['slots']->stop();

 $view['slots']->start('modal-body') ?>
<p>Based on information given during your study assessment, we have paired the ideal study strategy for each course's study sessions.</p>
<p>Click on the study sessions listed below the calendar to expand the session.  Once expanded, you will see the recommended study strategy.</p>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer') ?>
<a href="#close" class="btn btn-primary" data-dismiss="modal">Done</a>
<?php $view['slots']->stop() ?>


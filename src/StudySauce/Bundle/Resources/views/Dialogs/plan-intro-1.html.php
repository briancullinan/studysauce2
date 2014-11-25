<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

 $view['slots']->start('modal-header') ?>
Tip #1 - Further customize your plan
<?php $view['slots']->stop();

 $view['slots']->start('modal-body') ?>
<p>Click and drag the study sessions on your plan to create the perfect study plan.  You can also change your class schedule or your study preferences (on the class schedule or study profile tab respectively).</p>
<p>Note - if you are on a mobile device, you will have to make the edits from a desktop computer</p>
<?php $view['slots']->stop();

 $view['slots']->start('modal-footer') ?>
<a href="#plan-intro-2" class="btn btn-primary" data-toggle="modal">Next</a>
<?php $view['slots']->stop() ?>


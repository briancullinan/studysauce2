<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Step 3 - Now let's customize your study plan
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<p>Next, add your spaced repetition study sessions.  Drag the colored boxes onto your calendar to create your plan.</p>
<p>* Remember - Spaced repetition sessions should be within 24 hours after your class</p>
<p>Watch the study plan video if you have no idea what we are talking about...</p>
<div class="highlighted-link">
    <a href="#add-spaced-repetition" data-dismiss="modal" class="more">Add spaced-repetition sessions</a>
</div>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer') ?>

<?php $view['slots']->stop() ?>


<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Step 3 - Now let's customize your study plan
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<p>We have now created the prework study sessions that you will need throughout the week.  Drag the colored boxes onto your calendar to create your plan.</p>
<p>* Remember - Prework sessions should be within 24 hours before your class</p>
<p>Watch the study plan video if you have no idea what we are talking about...</p>
<div class="highlighted-link">
    <a href="#add-prework" data-dismiss="modal" class="more">Add prework sessions</a>
</div>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer') ?>

<?php $view['slots']->stop() ?>


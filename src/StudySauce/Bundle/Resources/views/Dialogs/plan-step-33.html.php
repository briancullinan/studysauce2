<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Step 3 - Now let's customize your study plan
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<p>Finally, add your free study sessions.  These can go anywhere you want and are used when you need extra time to complete a paper, work on a project, or catch up on studying.</p>
<p>Watch the study plan video if you have no idea what we are talking about...</p>
<div class="highlighted-link">
    <a href="#add-free-study" data-dismiss="modal" class="more">Add free study sessions</a>
</div>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer') ?>

<?php $view['slots']->stop() ?>


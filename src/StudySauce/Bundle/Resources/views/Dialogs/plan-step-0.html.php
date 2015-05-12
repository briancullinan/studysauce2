<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Let's get started building your personal study plan
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<p>We will guide you through a few steps to get the information needed to create your personalized plan.</p>
<div class="highlighted-link">
    <ul class="dialog-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    <button type="submit" value="#plan-step-0" class="more">Get started</button>
</div>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer') ?>

<?php $view['slots']->stop() ?>


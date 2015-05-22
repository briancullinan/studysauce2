<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Let's get started building your personal study plan
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<p>We will guide you through a few steps to get the information needed to create your personalized plan.<br/><br/><br/><br/></p>
<div class="highlighted-link">
    <ul class="dialog-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    <a href="#plan-step-1" data-toggle="modal" class="more">Get started</a>
</div>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer') ?>

<?php $view['slots']->stop() ?>


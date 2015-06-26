<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Let's get started building your personal study plan
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<p>We will guide you through a few steps to:<br /></p>
<ol>
    <li>Create your ideal study plan</li>
    <li>Download the plan to a calendar</li>
</ol>
<br />
<?php $view['slots']->stop();

$view['slots']->start('modal-footer'); ?>
<a href="#plan-step-1" data-toggle="modal" class="btn btn-primary">Get started</a>
<?php $view['slots']->stop();

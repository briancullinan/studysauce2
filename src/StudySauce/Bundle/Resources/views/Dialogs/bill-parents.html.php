<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Send an email to have someone prepay for Study Sauce.  We will then alert you when your account has been activated.
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<div class="first-name">
    <label class="input"><span>Parent's first name</span><input name="parent-first" type="text" value=""></label>
</div>
<div class="last-name">
    <label class="input"><span>Parent's last name</span><input name="parent-last" type="email" value=""></label>
</div>
<div class="email">
    <label class="input"><span>Parent's email</span><input name="parent-email" type="email" value=""></label>
</div>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer') ?>
<a href="#submit-contact" class="btn btn-primary">Send</a>
<?php $view['slots']->stop() ?>

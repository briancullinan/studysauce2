<?php
use StudySauce\Bundle\Entity\User;

/** @var User $user */
$user = $app->getUser();

$view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Invite your student to start using Study Sauce.
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<div class="first-name">
    <label class="input"><span>Student's first name</span><input type="text" value=""></label>
</div>
<div class="last-name">
    <label class="input"><span>Student's last name</span><input type="text" value=""></label>
</div>
<div class="email">
    <label class="input"><span>Student's email</span><input type="email" value=""></label>
</div>
<?php
if(!is_object($user) || $user->hasRole('ROLE_GUEST')) { ?>
    <div class="first-name">
        <label class="input"><span>Your first name</span><input type="text" value=""></label>
    </div>
    <div class="last-name">
        <label class="input"><span>Your last name</span><input type="text" value=""></label>
    </div>
    <div class="email">
        <label class="input"><span>Your email</span><input type="email" value=""></label>
    </div>
<?php }
$view['slots']->stop();

$view['slots']->start('modal-footer') ?>
<a href="#submit-contact" class="btn btn-primary">Send</a>
<?php $view['slots']->stop() ?>

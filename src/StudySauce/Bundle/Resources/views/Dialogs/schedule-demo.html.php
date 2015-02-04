<?php
use StudySauce\Bundle\Entity\User;

$view->extend('StudySauceBundle::Dialogs/dialog.html.php');

/** @var User $user */
$user = $app->getUser();
$isDemo = $user == 'anon.' || $user->hasRole('ROLE_GUEST') || $user->hasRole('ROLE_DEMO');


$view['slots']->start('modal-header') ?>
Schedule a demo
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<form action="<?php print $view['router']->generate('contact_send'); ?>" method="post">
    <div class="name">
        <label class="input"><span>Your name</span><input name="your-name" type="text" value="<?php print ($isDemo ? '' : $view->escape($user->getFirst() . ' ' . $user->getLast())); ?>"></label>
    </div>
    <div class="email">
        <label class="input"><span>Your email</span><input name="your-email" type="email" value="<?php print ($isDemo || substr($user->getEmail(), -strlen('@example.org')) == '@example.org' ? '' : $view->escape($user->getEmail())); ?>"></label>
    </div>
    <div class="message">
        <label class="input"><span>Message</span><textarea placeholder="" cols="60" rows="2"></textarea></label>
    </div>
    <div class="highlighted-link">
        <button type="submit" value="#submit-contact" class="more">Send</button>
    </div>
</form>
<?php $view['slots']->stop();


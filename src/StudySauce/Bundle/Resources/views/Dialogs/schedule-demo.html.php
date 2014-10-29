<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Schedule a demo
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<label class="input"><span>Your name</span><input name="your-name" type="text" value=""></label>
<label class="input"><span>Your email</span><input name="your-email" type="email" value=""></label>
<label class="input">
    <span>Message</span>
    <textarea placeholder="" cols="60" rows="2"></textarea>
</label>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer') ?>
<a href="#submit-contact" class="btn btn-primary">Send</a>
<?php $view['slots']->stop() ?>

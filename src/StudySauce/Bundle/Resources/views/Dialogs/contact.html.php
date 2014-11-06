<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

 $view['slots']->start('modal-header') ?>
Contact us
<?php $view['slots']->stop();

 $view['slots']->start('modal-body') ?>
<p>If you have any questions at all, please contact us. &nbsp;We would love to hear from you! &nbsp;
    We want to help you to get the most out of your study time and your comments and feedback are important to us.</p>
<div class="name">
    <label class="input"><span>Your name</span><input name="your-name" type="text" value=""></label>
</div>
<div class="email">
    <label class="input"><span>Your email</span><input name="your-email" type="email" value=""></label>
</div>
<div class="message">
    <label class="input"><span>Message</span><textarea placeholder="" cols="60" rows="2"></textarea></label>
</div>
<?php $view['slots']->stop();

 $view['slots']->start('modal-footer') ?>
<a href="#submit-contact" class="btn btn-primary">Send</a>
<?php $view['slots']->stop() ?>


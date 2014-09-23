<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php') ?>

<?php $view['slots']->start('modal-header') ?>
Contact us
<?php $view['slots']->stop() ?>

<?php $view['slots']->start('modal-body') ?>
<p>If you have any questions at all, please contact us. &nbsp;We would love to hear from you! &nbsp;
    We want to help you to get the most out of your study time and your comments and feedback are important to us.</p>
<div class="form-item webform-component webform-component-textfield webform-component--your-name">
    <label for="edit-submitted-your-name">Your name</label>
    <input type="text" id="edit-submitted-your-name" name="submitted[your_name]" value="" size="60" maxlength="128" class="form-text required">
</div>
<div class="form-item webform-component webform-component-email webform-component--your-email">
    <label for="edit-submitted-your-email">Your email</label>
    <input class="email form-text form-email required" type="email" id="edit-submitted-your-email" name="submitted[your_email]" value="" size="60">
</div>
<div class="form-item webform-component webform-component-textarea webform-component--message">
    <label for="edit-submitted-message">Message</label>
    <div class="form-textarea-wrapper resizable textarea-processed resizable-textarea">
        <textarea id="edit-submitted-message" name="submitted[message]" cols="60" rows="4" class="form-textarea required"></textarea>
    </div>
</div>
<?php $view['slots']->stop() ?>

<?php $view['slots']->start('modal-footer') ?>
<button type="button" class="btn btn-primary">Send</button>
<?php $view['slots']->stop() ?>


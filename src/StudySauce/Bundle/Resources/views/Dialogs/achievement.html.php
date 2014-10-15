<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

 $view['slots']->start('modal-header') ?>
Let your sponsor know about your study achievement.
<?php $view['slots']->stop();

 $view['slots']->start('modal-body') ?>
<div class="plupload html5">
    <input type="file" accept="image/png,image/gif,image/jpeg,image/*" multiple="multiple">
</div>
<div class="field-type-text-long field-name-field-message field-widget-text-textarea ">
    <div class="form-item form-type-textarea">
        <label>Message </label>

        <div class="form-textarea-wrapper resizable textarea-processed resizable-textarea"><textarea class="text-full form-textarea jquery_placeholder-processed" placeholder="Message" name="field_goals[und][0][field_message][und][0][value]" cols="60" rows="5"></textarea>
        </div>
    </div>
</div>
<?php $view['slots']->stop();

 $view['slots']->start('modal-footer') ?>
<button type="button" class="btn btn-primary">Save</button>
<?php $view['slots']->stop() ?>


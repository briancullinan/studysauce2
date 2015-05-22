<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Step 7 - Download your new study plan
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<p>Now you can download your study plan into your calendar.  Just click on the icon below.<br/><br/></p>
<a href="#download-plan">&nbsp;</a>
<br/><br/><br/><br/>
<div class="highlighted-link">
    <ul class="dialog-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    <a href="#plan-step-5" data-dismiss="modal" class="more">Go to study plan</a>
</div>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer') ?>

<?php $view['slots']->stop() ?>


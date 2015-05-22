<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Step 4 - Would you like calendar alerts?
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<br /><br/>
<header>
    <label>Yes</label>
    <label>No</label>
    <label>Alert</label>
</header>
<h4>Classes</h4>
<label class="radio"><span>Yes</span><input name="classes-alert" type="radio" value="1" checked="checked"><i></i></label>
<label class="radio"><span>No</span><input name="classes-alert" type="radio" value="0"><i></i></label>
<label class="input"><span>Alert</span><select name="classes-alert"><option value="15">15 min</option><option value="30">30 min</option><option value="60">1 hour</option></select></label>
<h4>Prework</h4>
<label class="radio"><span>Yes</span><input name="prework-alert" type="radio" value="1" checked="checked"><i></i></label>
<label class="radio"><span>No</span><input name="prework-alert" type="radio" value="0"><i></i></label>
<label class="input"><span>Alert</span><select name="prework-alert"><option value="15">15 min</option><option value="30">30 min</option><option value="60">1 hour</option></select></label>
<h4>Spaced repetition</h4>
<label class="radio"><span>Yes</span><input name="spaced-alert" type="radio" value="1" checked="checked"><i></i></label>
<label class="radio"><span>No</span><input name="spaced-alert" type="radio" value="0"><i></i></label>
<label class="input"><span>Alert</span><select name="spaced-alert"><option value="15">15 min</option><option value="30">30 min</option><option value="60">1 hour</option></select></label>
<h4>Free study</h4>
<label class="radio"><span>Yes</span><input name="free-alert" type="radio" value="1" checked="checked"><i></i></label>
<label class="radio"><span>No</span><input name="free-alert" type="radio" value="0"><i></i></label>
<label class="input"><span>Alert</span><select name="free-alert"><option value="15">15 min</option><option value="30">30 min</option><option value="60">1 hour</option></select></label>
<h4>Non-academic events</h4>
<label class="radio"><span>Yes</span><input name="other-alert" type="radio" value="1" checked="checked"><i></i></label>
<label class="radio"><span>No</span><input name="other-alert" type="radio" value="0"><i></i></label>
<label class="input"><span>Alert</span><select name="other-alert"><option value="15">15 min</option><option value="30">30 min</option><option value="60">1 hour</option></select></label>
<br/><br/><br/><br/>
<div class="highlighted-link">
    <ul class="dialog-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    <a href="#plan-step-5" data-toggle="modal" class="more">Next</a>
</div>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer') ?>

<?php $view['slots']->stop() ?>


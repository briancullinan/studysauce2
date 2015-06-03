<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Step 3 - Would you like calendar alerts?
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<br /><br/>
<header>
    <label>Yes</label>
    <label>No</label>
    <label>Alert</label>
</header>
<h4>Classes</h4>
<label class="radio"><span>Yes</span><input name="event-type-c" type="radio" value="1" checked="checked"><i></i></label>
<label class="radio"><span>No</span><input name="event-type-c" type="radio" value="0"><i></i></label>
<label class="input"><span>Alert</span><select name="event-type-c"><option value="15">15 min</option><option value="30">30 min</option><option value="60">1 hour</option></select></label>
<h4>Prework</h4>
<label class="radio"><span>Yes</span><input name="event-type-p" type="radio" value="1" checked="checked"><i></i></label>
<label class="radio"><span>No</span><input name="event-type-p" type="radio" value="0"><i></i></label>
<label class="input"><span>Alert</span><select name="event-type-p"><option value="15">15 min</option><option value="30">30 min</option><option value="60">1 hour</option></select></label>
<h4>Spaced repetition</h4>
<label class="radio"><span>Yes</span><input name="event-type-sr" type="radio" value="1" checked="checked"><i></i></label>
<label class="radio"><span>No</span><input name="event-type-sr" type="radio" value="0"><i></i></label>
<label class="input"><span>Alert</span><select name="event-type-sr"><option value="15">15 min</option><option value="30">30 min</option><option value="60">1 hour</option></select></label>
<h4>Free study</h4>
<label class="radio"><span>Yes</span><input name="event-type-f" type="radio" value="1" checked="checked"><i></i></label>
<label class="radio"><span>No</span><input name="event-type-f" type="radio" value="0"><i></i></label>
<label class="input"><span>Alert</span><select name="event-type-f"><option value="15">15 min</option><option value="30">30 min</option><option value="60">1 hour</option></select></label>
<h4>Non-academic events</h4>
<label class="radio"><span>Yes</span><input name="event-type-o" type="radio" value="1" checked="checked"><i></i></label>
<label class="radio"><span>No</span><input name="event-type-o" type="radio" value="0"><i></i></label>
<label class="input"><span>Alert</span><select name="event-type-o"><option value="15">15 min</option><option value="30">30 min</option><option value="60">1 hour</option></select></label>
<br/><br/><br/><br/>
<div class="highlighted-link setup-mode">
    <ul class="dialog-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    <a href="#plan-step-4" data-toggle="modal" class="more">Next</a>
</div>
<div class="highlighted-link">
    <ul class="dialog-tracker"><li><a href="#plan-step-1" title="Class difficulty" data-toggle="modal">&bullet;</a></li><li><a href="#plan-step-3" title="Notifications" data-toggle="modal">&bullet;</a></li><li><a href="#plan-step-4" title="Class type" data-toggle="modal">&bullet;</a></li></ul>
    <a href="#plan-step-4" data-toggle="modal" class="more">Next</a>
</div>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer') ?>

<?php $view['slots']->stop() ?>


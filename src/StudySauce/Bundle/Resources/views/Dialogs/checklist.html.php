<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

 $view['slots']->start('modal-header') ?>
Pilots run through a flight checklist to make sure they are focused and ready to go. We have built a study version for you below.
<?php $view['slots']->stop();

 $view['slots']->start('modal-body') ?>
<ol class="checkboxes">
    <li><input type="checkbox" name="mobile" id="mobile"><label for="mobile">Turn mobile device to airplane mode</label></li>
    <li><input type="checkbox" name="distractions" id="distractions"><label for="distractions">Minimize other electronic distractions (turn off TV, close FB, etc.)</label></li>
    <li><input type="checkbox" name="materials" id="materials"><label for="materials">Gather all of your study materials before starting</label></li>
    <li><input type="checkbox" name="objective" id="objective"><label for="objective">Understand your objective for the session (study a particular chapter, memorize key terms, etc.)</label></li>
    <li><input type="checkbox" name="comfortable" id="comfortable"><label for="comfortable">Get comfortable, but not too comfortable (try not to study on your bed)</label></li>
    <li><input type="checkbox" name="hour" id="hour"><label for="hour">Get ready to study for 1 hour, then you can take a short break</label></li>
</ol>
<ol class="no-checkboxes">
    <li>Turn mobile device to airplane mode</li>
    <li>Minimize other electronic distractions (turn off TV, close FB, etc.)</li>
    <li>Gather all of your study materials before starting</li>
    <li>Understand your objective for the session (study a particular chapter, memorize key terms, etc.)</li>
    <li>Get comfortable, but not too comfortable (try not to study on your bed)</li>
    <li>Get ready to study for 1 hour, then you can take a short break</li>
</ol>
<?php $view['slots']->stop();

 $view['slots']->start('modal-footer') ?>
<a href="#study" class="btn btn-primary">Continue to session</a>
<?php $view['slots']->stop() ?>


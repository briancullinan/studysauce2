<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Step 2 - What kind of grades do you want to make?
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<form action="<?php print $view['router']->generate('profile_update'); ?>" method="post">
    <p>This step will determine how many total hours you will need to study per week.</p>
    <label class="radio"><input name="profile-grades" type="radio"
                                value="as-only" <?php print ($schedule->getGrades() == 'as-only' ? 'checked="checked"' : ''); ?>><i></i><span>Nothing but As (multiple of 2.5 hours)</span></label>
    <label class="radio"><input name="profile-grades" type="radio"
                                value="has-life" <?php print ($schedule->getGrades() == 'has-life' ? 'checked="checked"' : ''); ?>><i></i><span>I want to do well, but I don't want to live in the library (multiple of 2 hours)</span></label>
    <label class="radio"><input name="profile-grades" type="radio"
                                value="cs-degrees" <?php print ($schedule->getGrades() == 'cs-degrees' ? 'checked="checked"' : ''); ?>><i></i><span>Cs get degrees (multiple of 1.5 hours)</span></label>
    <div class="highlighted-link">
        <ul class="dialog-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
        <button type="submit" value="#plan-step-2" class="more">Next</button>
    </div>
</form>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer') ?>

<?php $view['slots']->stop() ?>


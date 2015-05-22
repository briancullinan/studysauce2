<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Step 5 - Type of classes
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<form action="<?php print $view['router']->generate('profile_update'); ?>" method="post">
    <p>What is the primary type of studying you expect to do in this class?<br/><br/></p>
    <header>
        <label>Type of studying</label>
    </header>
    <?php
    foreach($courses as $i => $c)
    {
        /** @var Course $c */
        ?>
        <h4><span class="class<?php print $i; ?>"></span><?php print $c->getName(); ?></h4>
        <label class="input"><span>Type of studying</span>
            <select name="profile-type-<?php print $c->getId(); ?>">
                <option value="memorization" <?php print ($c->getStudyType() == 'memorization' ? 'checked="checked"' : ''); ?>>Memorization</option>
                <option value="memorization" <?php print ($c->getStudyType() == 'reading' ? 'checked="checked"' : ''); ?>>Reading / writing</option>
                <option value="memorization" <?php print ($c->getStudyType() == 'conceptual' ? 'checked="checked"' : ''); ?>>Memorization</option>
            </select>
        </label>
    <?php } ?>
    <br/><br/><br/><br/>
    <div class="highlighted-link">
        <ul class="dialog-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
        <a href="#plan-step-6" data-toggle="modal" class="more">Next</a>
    </div>
</form>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer') ?>

<?php $view['slots']->stop() ?>


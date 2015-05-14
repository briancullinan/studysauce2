<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Step 5 - Type of classes
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<form action="<?php print $view['router']->generate('profile_update'); ?>" method="post">
    <p>What is the primary type of studying you expect to do in this class?</p>
    <header>
        <label title="Class will primarily test your ability to remember definitions or terms">Memorization</label>
        <label title="Class work focuses on heavy reading and writing">Reading / <br/>writing</label>
        <label title="Class will focus on deeper understanding of materials">Conceptual <br/>application</label>
    </header>
    <?php
    foreach($courses as $i => $c)
    {
        /** @var Course $c */
        ?>
        <h4><span class="class<?php print $i; ?>"></span><?php print $c->getName(); ?></h4>
        <label title="Class will primarily test your ability to remember definitions or terms"
               class="radio"><span>Memorization</span><input name="profile-type-<?php print $c->getId(); ?>" type="radio" value="memorization" <?php print ($c->getStudyType() == 'memorization' ? 'checked="checked"' : ''); ?>><i></i></label>
        <label title="Class work focuses on heavy reading and writing"
               class="radio"><span>Reading / <br/>writing</span><input name="profile-type-<?php print $c->getId(); ?>" type="radio" value="reading" <?php print ($c->getStudyType() == 'reading' ? 'checked="checked"' : ''); ?>><i></i></label>
        <label title="Class will focus on deeper understanding of materials"
               class="radio"><span>Conceptual <br/>application</span><input name="profile-type-<?php print $c->getId(); ?>" type="radio" value="conceptual" <?php print ($c->getStudyType() == 'conceptual' ? 'checked="checked"' : ''); ?>><i></i></label>
    <?php } ?>
    <div class="highlighted-link">
        <ul class="dialog-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
        <a href="#plan-step-6" data-toggle="modal" class="more">Next</a>
    </div>
</form>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer') ?>

<?php $view['slots']->stop() ?>


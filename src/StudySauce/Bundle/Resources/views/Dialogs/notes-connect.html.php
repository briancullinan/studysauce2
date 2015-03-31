<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Study notes
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
Put all of your notes in one place.

<div class="form-actions">
    <?php foreach($services as $o => $url) { ?>
        <a href="<?php print $url; ?>?_target=<?php print $view['router']->generate('notes'); ?>" class="more">Get started</a></label>
    <?php } ?>
</div>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer') ?>

<?php $view['slots']->stop() ?>


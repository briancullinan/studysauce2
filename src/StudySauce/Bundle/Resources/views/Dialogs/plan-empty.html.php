<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php') ?>

<?php $view['slots']->start('modal-header') ?>
You have no activities scheduled this week.
<?php $view['slots']->stop() ?>

<?php $view['slots']->start('modal-body') ?>
<h3><a href="#schedule">Edit your schedule here.</a></h3>
<h3>- or -</h3>
<h3>Use the arrows above to the right to find a week with activities.</h3>
<?php $view['slots']->stop() ?>

<?php $view['slots']->start('modal-footer') ?>
<?php $view['slots']->stop() ?>


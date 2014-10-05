<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

 $view['slots']->start('modal-header') ?>
Just a moment while we build your plan.
<?php $view['slots']->stop();

 $view['slots']->start('modal-body') ?>
<div class="timer"></div>
<?php $view['slots']->stop();

 $view['slots']->start('modal-footer') ?>
<button type="button" class="btn btn-primary">Hide</button>
<?php $view['slots']->stop() ?>


<?php

$view->extend('StudySauceBundle:Dialogs:dialog.html.php');

$view['slots']->start('modal-header') ?>

<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<i class="sauce"></i>
<p>Add Study Sauce to your home screen by tapping below</p>
<i class="arrow"></i>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer') ?>

<?php $view['slots']->stop() ?>


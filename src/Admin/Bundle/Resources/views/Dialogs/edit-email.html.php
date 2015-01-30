<?php
use StudySauce\Bundle\Entity\Group;

$view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Edit email templates
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>

<?php $view['slots']->stop();

$view['slots']->start('modal-footer'); ?>
<a href="#close" class="btn btn-primary" data-dismiss="modal">Save</a>
<?php $view['slots']->stop() ?>


<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Change all reoccurring events?
<?php $view['slots']->stop();

$view['slots']->start('modal-footer') ?>
<a href="#close" data-dismiss="modal" class="btn">No, this one only</a>
<a href="#close" data-dismiss="modal" class="btn btn-primary">Yes, change all</a>
<?php $view['slots']->stop();

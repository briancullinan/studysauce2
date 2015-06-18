<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Change all reoccurring events?
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<div class="highlighted-link">
    <a href="#close" data-dismiss="modal">No, this one only</a>
    <a href="#close" data-dismiss="modal" class="more">Yes, change all</a>
</div>
<?php $view['slots']->stop();

<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
    Use a desktop
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
    <p>Save your thumbs and setup your study plan on a desktop computer or tablet.<br/><br/><br/></p>
    <div class="highlighted-link">
        <a href="#close" data-dismiss="modal" class="more">Don't show this again</a>
    </div>
<?php $view['slots']->stop();

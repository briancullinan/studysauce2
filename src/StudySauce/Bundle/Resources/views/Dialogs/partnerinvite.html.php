<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php') ?>

<?php $view['slots']->start('modal-header') ?>
Thank you
<?php $view['slots']->stop() ?>

<?php $view['slots']->start('modal-body') ?>
<p>We have sent the email invitation.  You will be notified once the invitation is accepted.</p>
<?php $view['slots']->stop() ?>

<?php $view['slots']->start('modal-footer') ?>
<button type="button" class="btn btn-primary">Close</button>
<?php $view['slots']->stop() ?>


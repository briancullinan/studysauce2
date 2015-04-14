<?php

$view->extend('StudySauceBundle:Dialogs:dialog.html.php');

$view['slots']->start('modal-header') ?>
Add to home screen
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/Study_Sauce_Logo.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
    <img class="sauce" width="100" height="100" src="<?php echo $view->escape($url) ?>" alt="LOGO" />
<?php endforeach; ?>
<p>Launch faster from your phone.  Tap below and select "Add to home screen" to create an icon.</p>
<i class="arrow"></i>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer') ?>

<?php $view['slots']->stop() ?>


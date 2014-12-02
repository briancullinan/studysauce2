<?php
$view->extend('StudySauceBundle:Emails:layout.html.php');

$view['slots']->start('message'); ?>
A new user has signed up!
<?php $view['slots']->stop(); ?>

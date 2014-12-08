<?php
$view->extend('StudySauceBundle:Emails:layout.html.php');

$view['slots']->start('message'); ?>
A user <?php print $user->getFirst(); ?> has signed up for an account using a Torch & Laurel coupon code.
<?php $view['slots']->stop(); ?>

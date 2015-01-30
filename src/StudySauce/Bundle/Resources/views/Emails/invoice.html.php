<?php

$view->extend('StudySauceBundle:Emails:layout.html.php');

$view['slots']->start('message'); ?>
Thank you for your recent purchase.  You can find the details of your order below.  If you have any questions, please feel free to contact us at <a style="color:#FF9900;" href="mailto:admin@studysauce.com">admin@studysauce.com.</a><br />
<br />
<strong>Purchasing Information:</strong><br />
<?php print $payment->getFirst(); ?> <?php print $payment->getLast(); ?><br />
<br />
<strong>Billing Address</strong><br />
<?php print $address; ?><br />
<br />
<strong>E-mail Address:</strong><br />
<?php print $user->getEmail(); ?><br />
<br />
<strong>Order Total:</strong>
<?php print $payment->getAmount(); ?><br />
<?php $view['slots']->stop(); ?>
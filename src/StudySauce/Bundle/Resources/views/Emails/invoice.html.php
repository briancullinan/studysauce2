<?php

$view->extend('StudySauceBundle:Emails:layout.html.php');

$view['slots']->start('message'); ?>
Thank you for your recent purchase.  You can find the details of your order below.  If you have any questions, please feel free to contact us at <a style="color:#FF9900;" href="mailto:admin@studysauce.com">admin@studysauce.com.</a><br />
Purchasing Information:<br />
<?php print $payment->getFirst(); ?> <?php print $payment->getLast(); ?><br />
Billing Address<br />
<?php print $address; ?>
E-mail Address:<br />
<?php print $user->getEmail(); ?><br />
<br />
Order Total:
<?php print $payment->getAmount(); ?><br />
<?php $view['slots']->stop(); ?>
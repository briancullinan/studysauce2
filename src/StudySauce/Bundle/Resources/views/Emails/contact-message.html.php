<?php
use StudySauce\Bundle\Entity\ContactMessage;

$view->extend('StudySauceBundle:Emails:layout.html.php');

/** @var ContactMessage $message */

$view['slots']->start('message'); ?>
<p>
    <strong>Name: </strong><?php print $view->escape($message->getName()); ?><br />
    <strong>Email: </strong><?php print $view->escape($message->getEmail()); ?><br />
    <strong>Message: </strong>
    <?php print '<hr />' . $view->escape($message->getMessage()); ?>
</p>
<?php $view['slots']->stop(); ?>

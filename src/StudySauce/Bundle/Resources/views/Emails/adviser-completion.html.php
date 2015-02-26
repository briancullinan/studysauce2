<?php
use StudySauce\Bundle\Entity\GroupInvite;
use StudySauce\Bundle\Entity\User;

$view->extend('StudySauceBundle:Emails:layout.html.php');

/** @var User $user */

$view['slots']->start('message'); ?>
<strong>Incomplete students:</strong><br />
<?php foreach($incomplete as $i) {
    /** @var User $i */
    ?><span style="display:inline-block; width:30px; text-align:right;"><?php print $i->getCompleted() ?>%</span> -
    <?php print $i->getFirst() . ' ' . $i->getLast(); ?><br />
<?php } ?>
<strong>Haven't signed up:</strong><br />
<?php foreach($nosignup as $i) {
    /** @var GroupInvite $i */
    print $i->getFirst() . ' ' . $i->getLast(); ?><br />
<?php } ?>
<strong>Complete students:</strong><br />
<?php foreach($complete as $i) {
    /** @var User $i */
    print $i->getFirst() . ' ' . $i->getLast(); ?><br />
<?php }
$view['slots']->stop();


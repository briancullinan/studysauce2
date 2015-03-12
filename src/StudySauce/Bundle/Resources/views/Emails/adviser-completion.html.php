<?php
use StudySauce\Bundle\Entity\GroupInvite;
use StudySauce\Bundle\Entity\User;

$view->extend('StudySauceBundle:Emails:layout.html.php');

/** @var User $user */

$view['slots']->start('message'); ?>
<strong>Incomplete students:</strong><br />
<table border="0" style="min-width:300px;">
    <?php foreach($incomplete as $i) {
        /** @var User $i */
        ?><tr>
            <td><?php print $i->getLast(); ?>, <?php print $i->getFirst(); ?></td>
            <td style="text-align: right;"><?php print $i->getCompleted() ?>%</td>
        </tr>
    <?php } ?>
</table>
<br /><br />
<strong>Haven't signed up:</strong><br />
<?php foreach($nosignup as $i) {
    /** @var GroupInvite $i */
    print $i->getLast() . ', ' . $i->getFirst(); ?><br />
<?php } ?>
<br /><br />
<strong>Complete students:</strong><br />
<?php foreach($complete as $i) {
    /** @var User $i */
    print $i->getLast() . ', ' . $i->getFirst(); ?><br />
<?php }
$view['slots']->stop();


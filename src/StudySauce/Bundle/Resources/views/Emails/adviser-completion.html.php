<?php
use StudySauce\Bundle\Entity\GroupInvite;
use StudySauce\Bundle\Entity\User;

$view->extend('StudySauceBundle:Emails:layout.html.php');

/** @var User $user */

$view['slots']->start('message'); ?>
    Below is a list of your students' progress towards the Study Sauce course.<br />
    </p>
    <strong style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555;">Incomplete students:</strong>
    <table border="0" style="min-width:300px;">
        <?php
        if (empty($incomplete)) {
            ?>
            <tr>
                <td style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555;">None</td>
            </tr><?php
        } else {
            foreach ($incomplete as $i) {
                /** @var User $i */
                ?>
                <tr>
                    <td style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555;"><?php print $i->getLast(
                        ); ?>, <?php print $i->getFirst(); ?></td>
                    <td style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555; text-align: right;"><?php print $i->getCompleted(
                        ) ?>%
                    </td>
                </tr>
            <?php }
        } ?>
    </table>
    <br/><br/>
    <p style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555;">
        <strong style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555;">Haven't signed up:</strong><br/>
        <?php
        if (empty($nosignup)) {
            ?><span style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555;">None</span><?php
        } else {
            foreach ($nosignup as $i) {
                /** @var GroupInvite $i */
                print $i->getLast() . ', ' . $i->getFirst(); ?><br/>
            <?php }
        } ?>
        <br/><br/>
    </p>
    <p style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555;">
        <strong style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555;">Complete students:</strong><br/>
        <?php
        if (empty($complete)) {
            ?><span style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555;">None</span><?php
        } else {
            foreach ($complete as $i) {
                /** @var User $i */
                print $i->getLast() . ', ' . $i->getFirst(); ?><br/>
            <?php }
        } ?><br/>
    </p>
    <p>
<?php $view['slots']->stop();


<?php
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;

/** @var GlobalVariables $app */
/** @var User $user */
?>
<ol class="guide">
    <?php
    $user = $app->getUser();
    if(empty($user->getPartnerOrAdviser()) || !$user->hasRole('ROLE_PAID')) { ?>
    <li><span><span>&nbsp;</span>Payment</span></li>
    <li class="line"><span>&nbsp;</span></li>
    <?php } else { ?>
        <li style="display:none;"><span><span>&nbsp;</span>Payment</span></li>
        <li style="display:none;" class="line"><span>&nbsp;</span></li>
    <?php } ?>
    <li><span><span>&nbsp;</span>Study profile</span></li>
    <li class="line"><span>&nbsp;</span></li>
    <li><span><span>&nbsp;</span>Class schedule</span></li>
    <li class="line"><span>&nbsp;</span></li>
    <li><span><span>&nbsp;</span>Customization</span></li>
</ol>
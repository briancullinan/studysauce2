<?php
use StudySauce\Bundle\Entity\Invite;
use StudySauce\Bundle\Entity\ParentInvite;
use StudySauce\Bundle\Entity\StudentInvite;
use StudySauce\Bundle\Entity\User;
use StudySauce\Bundle\EventListener\InviteListener;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;
use Symfony\Bundle\FrameworkBundle\Templating\TimedPhpEngine;
/** @var GlobalVariables $app */
/** @var $view TimedPhpEngine */
/** @var $user User */
/** @var Invite $invite */
$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');
foreach ($view['assetic']->stylesheets([
        '@StudySauceBundle/Resources/public/css/account.css'
    ], [], ['output' => 'bundles/studysauce/css/*.css']) as $url):
    ?><link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>" />
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts([
        '@StudySauceBundle/Resources/public/js/register.js'
    ], [], ['output' => 'bundles/studysauce/js/*.js']
) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('body'); ?>

<div class="panel-pane" id="register">
    <div class="pane-content">
        <h2>Welcome, let's get started.</h2>
        <?php
        $isFirst = true;
        foreach($services as $o => $url)
        {
            if(!$isFirst)
            {
                ?><div class="signup-or"><span>Or</span></div><?php
            }
            $isFirst = false;
            ?><a href="<?php print $url; ?>" class="more">Sign in</a><br /><?php
        }?>
        <a href="#sign-in-with-email" class="cloak">Or register in with <span class="reveal">email</span></a>
        <div class="first-name">
            <label class="input"><input type="text" placeholder="First name" value="<?php print (isset($first) ? $first : ''); ?>"></label>
        </div>
        <div class="last-name">
            <label class="input"><input type="text" placeholder="Last name" value="<?php print (isset($last) ? $last : ''); ?>"></label>
        </div>
        <div class="email">
            <label class="input"><input type="text" placeholder="Email" value="<?php print (isset($email) ? $email : ''); ?>"></label>
        </div>
        <div class="password">
            <label class="input"><input type="password" placeholder="Enter password" value=""></label>
        </div>
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>"/>
        <div class="form-actions highlighted-link invalid">
            <?php /* if(!empty($invite) && (!empty($invite->getUser() || $invite instanceof ParentInvite || $invite instanceof StudentInvite))) { ?>
                <a href="<?php print $view['router']->generate('logout'); ?>" class="cloak">You have been invited by <?php print (!empty($invite) ? $invite->getUser()->getFirst() : $invite->getFromFirst()); ?>.  Click here to decline.</a>
            <?php } */ ?>
            <a href="#user-register" class="more">Register</a>
        </div>
    </div>
</div>

<?php $view['slots']->stop(); ?>

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
foreach ($view['assetic']->stylesheets(['@StudySauceBundle/Resources/public/css/account.css'], [], ['output' => 'bundles/studysauce/css/*.css']) as $url): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>" />
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts(['@StudySauceBundle/Resources/public/js/register.js'], [], ['output' => 'bundles/studysauce/js/*.js']) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('body'); ?>

<div class="panel-pane" id="reset">
    <div class="pane-content">
        <h2>Set a new password.</h2>
        <div class="email readonly">
            <label class="input"><input readonly="readonly" type="text" placeholder="Email" value="<?php print (isset($email) ? $email : ''); ?>"></label>
        </div>
        <div class="password">
            <label class="input"><input type="password" placeholder="New password" value=""></label>
        </div>
        <input type="hidden" name="token" value="<?php echo $token; ?>"/>
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>"/>
        <div class="form-actions highlighted-link invalid">
            <a href="#save-password" class="more">Set password</a>
        </div>
    </div>
</div>

<?php $view['slots']->stop(); ?>

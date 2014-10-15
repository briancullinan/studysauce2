<?php
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Templating\TimedPhpEngine;

/** @var $view TimedPhpEngine */
/** @var $user User */

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
        '@StudySauceBundle/Resources/public/js/account.js'
    ], [], ['output' => 'bundles/studysauce/js/*.js']
) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('body'); ?>

<div class="panel-pane" id="account">
    <div class="pane-content">
        <h2>User settings</h2>
        <div class="first-name">
            <label class="input"><span>First name</span><input type="text" placeholder="First name" value="<?php print $user->getFirstName(); ?>"></label>
        </div>
        <div class="last-name">
            <label class="input"><span>Last name</span><input type="text" placeholder="Last name" value="<?php print $user->getLastName(); ?>"></label>
        </div>
        <div class="email">
            <label class="input"><span>E-mail address</span><input type="text" placeholder="Email" value="<?php print $user->getEmail(); ?>"></label>
        </div>
        <div class="password">
            <label class="input"><span>Current password</span><input type="password" placeholder="Enter password" value=""></label>
        </div>
        <div class="new-password">
            <label class="input"><span>New password</span><input type="password" placeholder="No change &bullet; &bullet; &bullet; &bullet; &bullet; &bullet;" value=""></label>
        </div>
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>"/>
        <div class="form-actions highlighted-link invalid">
            <a href="#save-account" class="more">Save</a>
        </div>
    </div>
</div>

<?php $view['slots']->stop(); ?>

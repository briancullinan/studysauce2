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
        '@StudySauceBundle/Resources/public/js/login.js'
    ], [], ['output' => 'bundles/studysauce/js/*.js']
) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('body'); ?>

<div class="panel-pane" id="account">
    <div class="pane-content">
        <h2>Welcome back!</h2>
        <div class="highlighted-link social"></div>
        <?php
        $first = true;
        foreach($services as $o => $url)
        {
            if(!$first)
            {
                ?><div class="signup-or"><span>Or</span></div><?php
            }
            $first = false;
            ?><a href="<?php print $url; ?>" class="more">Sign in</a><br /><?php
        }?>
        <a href="#sign-in-with-email" class="cloak">Or sign in with <span class="reveal">email</span></a>
        <div class="email">
            <label class="input"><input type="text" placeholder="Email" value="<?php print (isset($username) ? $username : ''); ?>"></label>
        </div>
        <div class="password">
            <label class="input"><input type="password" placeholder="Password" value=""></label>
            <small>Invalid email or password.</small>
        </div>
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>"/>
        <div class="form-actions highlighted-link invalid">
            <a href="#user-login" class="more">Login</a>
        </div>
    </div>
</div>

<?php $view['slots']->stop(); ?>

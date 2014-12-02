<?php
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\PartnerInvite;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;
use Symfony\Component\Templating\PhpEngine;

/** @var PhpEngine $view */
/** @var GlobalVariables $app */

$view['slots']->start('tmp-stylesheets');
$view['slots']->output('stylesheets');
$view['slots']->stop();
$view['slots']->start('stylesheets');
$view['slots']->output('tmp-stylesheets');
foreach ($view['assetic']->stylesheets(['@TorchAndLaurelBundle/Resources/public/css/header2.css'],[],['output' => 'bundles/torchandlaurel/css/*.css']) as $url): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>" />
<?php endforeach;
$view['slots']->stop();

/** @var User $user */
$user = $app->getUser();

/** @var PartnerInvite $partner */
$partner = !empty($user) ? $user->getPartnerInvites()->first() : null;
$advisers = !empty($user)
    ? array_values($user->getGroups()
            ->map(function (Group $g) {return $g->getUsers()->filter(function (User $u) {
                        return $u->hasRole('ROLE_ADVISER');})->toArray();})
            ->filter(function ($c) {return !empty($c);})
            ->toArray())
    : [];
if(count($advisers) > 1)
    $advisers = call_user_func_array('array_merge', $advisers);
elseif(count($advisers) > 0)
    $advisers = $advisers[0];
usort($advisers, function (User $a, User $b) {return $a->hasRole('ROLE_MASTER_ADVISER') - $b->hasRole('ROLE_MASTER_ADVISER');});
/** @var User $adviser */
$adviser = reset($advisers);

?>
    <div class="header-wrapper navbar navbar-inverse">
        <div class="header">
            <div id="site-name" class="container navbar-header">
                <a title="Home" href="<?php print $view['router']->generate('_welcome'); ?>">
                    <?php foreach ($view['assetic']->image(['@TorchAndLaurelBundle/Resources/public/images/tal_logo.png'], [], ['output' => 'bundles/torchandlaurel/images/*']) as $url): ?>
                        <img width="48" height="48" src="<?php echo $view->escape($url) ?>" alt="LOGO" />
                    <?php endforeach; ?><span><small>Powered by</small><strong>Study</strong> Sauce</span></a>
            </div>
            <?php if($app->getRequest()->get('_format') == 'index') { ?>
                <div id="partner-message">
                    <?php if(!empty($adviser)) { ?>
                        <div>
                            <?php if (empty($adviser->getPhoto())) {
                                foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/empty-photo.png'],[],['output' => 'bundles/studysauce/images/*']) as $url): ?>
                                    <img width="48" height="48" alt="Partner" src="<?php echo $view->escape($url) ?>"/>
                                <?php endforeach;
                            } else {
                                ?><img width="48" height="48" src="<?php echo $view->escape($adviser->getPhoto()->getUrl()) ?>"
                                       alt="LOGO" />
                            <?php } ?>
                        </div>
                        <div>I am accountable to: <br><span><?php print $adviser->getFirst(); ?> <?php print $adviser->getLast(); ?></span></div>
                    <?php } elseif(!empty($partner)) { ?>
                        <div>
                            <?php if (empty($partner->getPhoto())) {
                                foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/empty-photo.png'],[],['output' => 'bundles/studysauce/images/*']) as $url): ?>
                                    <img width="48" height="48" alt="Partner" src="<?php echo $view->escape($url) ?>"/>
                                <?php endforeach;
                            } else {
                                ?><img width="48" height="48" src="<?php echo $view->escape($partner->getPhoto()->getUrl()) ?>"
                                       alt="LOGO" />
                            <?php } ?>
                        </div>
                        <div>I am accountable to: <br><span><?php print $partner->getFirst(); ?> <?php print $partner->getLast(); ?></span></div>
                    <?php } else { ?>
                        <div>
                            <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/empty-photo.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                                <img width="48" height="48" alt="Partner" src="<?php echo $view->escape($url) ?>" />
                            <?php endforeach; ?>
                        </div>
                        <div>I am accountable to: <br><a href="<?php print $view['router']->generate('partner'); ?>">Click to set up</a></div>
                    <?php } ?>
                </div>
            <?php } ?>
            <?php if($app->getRequest()->get('_format') != 'funnel') { ?>
                <div id="welcome-message"><strong><?php print $app->getUser()->getFirst(); ?></strong>
                    <a href="<?php print $view['router']->generate('logout'); ?>" title="Log out">logout</a></div>
                <div id="jquery_jplayer" style="width: 0; height: 0;"></div>
            <?php } ?>
        </div>
    </div>
<?php



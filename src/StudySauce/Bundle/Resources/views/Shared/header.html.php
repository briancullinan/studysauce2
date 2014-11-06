<?php
$isDashboard = ($app->getUser() != 'anon.' && !$app->getUser()->hasRole('ROLE_GUEST')) ||
    strpos($view['slots']->get('classes'), 'dashboard-home') > -1;
$isAdviser = $app->getUser() != 'anon.' && $app->getUser()->hasRole('ROLE_ADVISER');
?>
<div class="header-wrapper navbar navbar-inverse">
    <div class="header">
        <div id="site-name" class="container navbar-header">
            <a title="Home" href="<?php print $view['router']->generate($isAdviser ? 'userlist' : ($isDashboard ? 'home' : '_welcome')); ?>">
                <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/logo_4_trans_2.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                    <img width="48" height="48" src="<?php echo $view->escape($url) ?>" alt="LOGO" />
                <?php endforeach; ?><span><strong>Study</strong> Sauce</span></a>
        </div>
        <?php if($app->getRequest()->get('_format') == 'index' && $isDashboard) { ?>
            <div id="partner-message">
                <?php if(!empty($partner = $app->getUser()->getPartners()->first()) && !empty($partner->getPhoto())) {
                    ?><img width="48" height="48" src="<?php echo $view->escape($partner->getPhoto()->getUrl()) ?>" alt="LOGO" />
                    <div>I am accountable to: <br><span><?php print $partner->getFirst(); ?> <?php print $partner->getLast(); ?></span></div>
                <?php }
                else {
                    foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/empty-photo.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                        <img width="48" height="48" alt="Partner" src="<?php echo $view->escape($url) ?>" />
                    <?php endforeach; ?>
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
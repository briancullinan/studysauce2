<div class="header-wrapper navbar navbar-inverse">
    <div class="header">
        <div id="site-name" class="container navbar-header">
            <a title="Home" href="<?php print $view['router']->generate('_welcome'); ?>">
                <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/logo_4_trans_2.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                    <img width="48" height="48" src="<?php echo $view->escape($url) ?>" alt="LOGO" />
                <?php endforeach; ?><strong>Study</strong> Sauce</a>
            <div id="site-slogan">Discover the secret sauce to new brand</div>
        </div>
        <div id="partner-message">
            <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/empty-photo.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                <img width="48" height="48" alt="Partner" src="<?php echo $view->escape($url) ?>" />
            <?php endforeach; ?>
            <div style="display:inline-block;">
                I am accountable to: <br><a href="#partner">Click to set up</a>
            </div>
        </div>
        <div id="welcome-message"><strong>Brian</strong>
            <a href="/user/logout" title="Log out">logout</a>    </div>
        <div id="jquery_jplayer" style="width: 0px; height: 0px;"><audio id="jp_audio_0" preload="metadata"></audio></div>
    </div>
</div>
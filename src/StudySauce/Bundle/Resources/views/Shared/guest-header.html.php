<?php
$isDashboard = strpos($view['slots']->get('classes'), 'dashboard-home') > -1;
?>
<div class="header-wrapper navbar navbar-inverse">
    <div class="header">
        <div id="site-name" class="container navbar-header">
            <a title="Home" href="<?php print $view['router']->generate($isDashboard ? 'home' : '_welcome'); ?>">
                <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/logo_4_trans_2.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                    <img width="48" height="48" src="<?php echo $view->escape($url) ?>" alt="LOGO" />
                <?php endforeach; ?><strong>Study</strong> Sauce</a>
            <div id="site-slogan">Discover the secret sauce to studying</div>
        </div>
    </div>
</div>
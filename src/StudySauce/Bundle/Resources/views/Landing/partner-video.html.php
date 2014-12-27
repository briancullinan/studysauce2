<div class="video clearfix">
    <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/SMU_blur.jpg'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
        <img width="100%" height="100%" src="<?php echo $view->escape($url) ?>" />
    <?php endforeach; ?>
    <div class="flexslider">
        <h1>Help your student succeed</h1>
        <ul class="slides clearfix">
            <li class="clone">
                <div class="highlighted-link">
                    <p><a class="more" href="<?php print $view['router']->generate('register'); ?>">Sign up to help - it's free</a><br /><span>or </span><a href="<?php print $view['router']->generate('login'); ?>">Sign in</a></p>
                </div>
                <div class="player-divider">
                    <div class="player-wrapper">
                        <iframe id="landing-intro-player" src="https://www.youtube.com/embed/toC_caUD05w?rel=0&amp;controls=0&amp;modestbranding=1&amp;showinfo=0&amp;enablejsapi=1&amp;origin=<?php print $app->getRequest()->getScheme() . '://' . $app->getRequest()->getHttpHost(); ?>"></iframe>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>

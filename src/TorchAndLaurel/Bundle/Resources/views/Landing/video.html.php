<div class="video clearfix">
    <video autoplay="" loop="" id="bgvid">
        <source src="https://s3-us-west-2.amazonaws.com/studysauce/Study_Difficulties.webm" type="video/webm">
        <source src="https://s3-us-west-2.amazonaws.com/studysauce/Study_Difficulties.mp4" type="video/mp4">
    </video>
    <div id="site-name" class="navbar-header">
        <a href="<?php print $view['router']->generate('login'); ?>">Sign in</a>
    </div>
    <div class="flexslider">
        <div class="player-divider">
            <h1>STOP THE STRESS!</h1>
            <div class="player-wrapper">
                <iframe id="landing-intro-player" src="https://www.youtube.com/embed/xY-LuIsFpio?rel=0&amp;controls=0&amp;modestbranding=1&amp;showinfo=0&amp;enablejsapi=1&amp;origin=<?php print $app->getRequest()->getScheme() . '://' . $app->getRequest()->getHttpHost(); ?>"></iframe>
            </div>
            <div class="highlighted-link">
                <a class="more" href="<?php print $view['router']->generate('checkout'); ?>">Get the Deal</a>
            </div>
        </div>
    </div>
</div>
<div class="did-you-know">
    <div>
        <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/did_you_know_620x310.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
            <img width="165" src="<?php echo $view->escape($url) ?>" alt="LOGO" />
        <?php endforeach; ?>
        <h2>Torch & Laurel students receive a huge discount.</h2>
        <div class="one"><h3><span>1</span>Broke? &nbsp; Get sponsored.</h3></div>
        <div class="two"><h3><span>2</span>Buy now at 75% off</h3></div>
        <div class="one"><a class="more" href="#bill-parents" data-toggle="modal">Ask your parents</a></div>
        <div class="two highlighted-link"><a class="more" href="<?php print $view['router']->generate('checkout'); ?>">Get the Deal</a></div>
    </div>
</div>

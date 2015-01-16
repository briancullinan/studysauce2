<div class="video clearfix">
    <div class="flexslider">
        <h1>College is hard...even for good students</h1>
        <ul class="slides clearfix">
            <li class="clone">
                <div class="highlighted-link">
                    <p><a class="more" href="<?php print $view['router']->generate('checkout'); ?>">Get the Deal</a><br /><span>or </span><a href="<?php print $view['router']->generate('login'); ?>">Sign in</a></p>
                </div>
                <div class="player-divider">
                    <div class="player-wrapper">
                        <iframe id="landing-intro-player" src="https://www.youtube.com/embed/vJG9PDaXNaQ?rel=0&amp;controls=0&amp;modestbranding=1&amp;showinfo=0&amp;enablejsapi=1"></iframe>
                    </div>
                </div>
            </li>
        </ul>
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

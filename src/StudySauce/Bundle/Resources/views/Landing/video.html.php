<div class="video clearfix">
    <video autoplay="" loop="" id="bgvid">
        <source src="https://s3-us-west-2.amazonaws.com/studysauce/Studying_Lesson.webm" type="video/webm">
        <source src="https://s3-us-west-2.amazonaws.com/studysauce/Studying_Lesson.mp4" type="video/mp4">
    </video>
    <div class="flexslider">
        <div id="site-name" class="container navbar-header">
            <a title="Home" href="<?php print $view['router']->generate('_welcome'); ?>">
                <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/logo_4_trans_2.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                    <img width="48" height="48" src="<?php echo $view->escape($url) ?>" alt="LOGO" />
                <?php endforeach; ?><span><strong>Study</strong> Sauce</span></a>
        </div>
        <a href="<?php print $view['router']->generate('login'); ?>">Sign in</a>
        <ul class="slides clearfix">
            <li class="clone">
                <div class="highlighted-link">

                </div>
                <div class="player-divider">
                    <h1>Learn how to learn</h1>
                    <div class="player-wrapper">
                        <iframe id="landing-intro-player" src="https://www.youtube.com/embed/vJG9PDaXNaQ?rel=0&amp;controls=0&amp;modestbranding=1&amp;showinfo=0&amp;enablejsapi=1&amp;origin=<?php print $app->getRequest()->getScheme() . '://' . $app->getRequest()->getHttpHost(); ?>"></iframe>
                    </div>
                    <div class="highlighted-link">
                        <a class="more" href="<?php print $view['router']->generate('register'); ?>">Sign up for free</a>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>
<div class="price-wrapper">
    <div class="price-section">
        <div class="student">
            <h2>Study Sauce</h2>
            <h3>Student</h3>
            <h4>$10 / month</h4>
            <a class="more" href="<?php print $view['router']->generate('checkout'); ?>">Buy now</a>
        </div>
        <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/Study_Sauce_Logo_Gray.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
            <img width="200" height="200" src="<?php echo $view->escape($url) ?>" alt="LOGO" />
        <?php endforeach; ?>
        <div class="business">
            <h2>Study Sauce</h2>
            <h3>Organization</h3>
            <h4>Contact for pricing</h4>
            <a href="#schedule-demo" class="more" data-toggle="modal">Contact us</a>
        </div>
    </div>
</div>

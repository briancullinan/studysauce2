<?php

$user = $app->getUser();

$view->extend('Course2Bundle:Shared:layout.html.php');

 $view['slots']->start('body'); ?>
<div class="panel-pane course2 step0" id="course2_test_taking">
    <div class="pane-content">
        <h2>Test taking <span class="time">10<small> minutes</small></span></h2>
        <div class="learn-bullets">
            <h3><span>In this course you will learn:</span></h3>
            <ol>
                <li><a><span>1</span>What to do leading up to the test</a></li>
                <li><a><span>2</span>How to manage test anxiety</a></li>
                <li><a><span>3</span>What to do during the test</a></li>
            </ol>
        </div>
        <div class="player-divider">
            <div class="player-wrapper">
                <?php foreach ($view['assetic']->image(['@Course2Bundle/Resources/public/images/Test_Taking.png'], [], ['output' => 'bundles/course2/images/*']) as $url): ?>
                    <img src="<?php echo $view->escape($url) ?>" alt="LOGO" />
                <?php endforeach; ?>
            </div>
        </div>
        <div class="highlighted-link">
            <?php if(!$user->hasRole('ROLE_PAID')) { ?>
                <a href="<?php print $view['router']->generate('premium'); ?>" class="more">Go Premium</a>
            <?php } else { ?>
                <a href="<?php print $view['router']->generate('course2_test_taking', ['_step' => 1]); ?>" class="more">Launch</a>
            <?php } ?>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>
<?php $view['slots']->stop(); ?>

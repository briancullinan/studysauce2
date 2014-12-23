<?php

$user = $app->getUser();

$view->extend('Course3Bundle:Shared:layout.html.php');

 $view['slots']->start('body'); ?>
<div class="panel-pane course3 step0" id="course3_spaced_repetition">
    <div class="pane-content">
        <h2>Spaced repetition <span class="time">10<small> minutes</small></span></h2>
        <div class="learn-bullets">
            <h3><span>In this course you will learn:</span></h3>
            <ol>
                <li><a><span>1</span>What the Forgetting Curve is</a></li>
                <li><a><span>2</span>How to defeat it</a></li>
                <li><a><span>3</span>How to use flash cards to complete a spaced repetition study session</a></li>
            </ol>
        </div>
        <div class="player-divider">
            <div class="player-wrapper">
                <?php foreach ($view['assetic']->image(['@Course3Bundle/Resources/public/images/Spaced_Repetition.png'], [], ['output' => 'bundles/course3/images/*']) as $url): ?>
                    <img src="<?php echo $view->escape($url) ?>" alt="LOGO" />
                <?php endforeach; ?>
            </div>
        </div>
        <div class="highlighted-link">
            <?php if(!$user->hasRole('ROLE_PAID')) { ?>
                <a href="<?php print $view['router']->generate('premium'); ?>" class="more">Go Premium</a>
            <?php } else { ?>
                <a href="<?php print $view['router']->generate('course3_spaced_repetition', ['_step' => 1]); ?>" class="more">Launch</a>
            <?php } ?>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>
<?php $view['slots']->stop(); ?>

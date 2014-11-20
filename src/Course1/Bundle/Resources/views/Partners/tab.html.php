<?php

$view->extend('Course1Bundle:Shared:layout.html.php');

 $view['slots']->start('body'); ?>
<div class="panel-pane course1 step0" id="course1_partners">
    <div class="pane-content">
        <h2>Partners <span class="time">10<small> minutes</small></span></h2>
        <div class="learn-bullets">
            <h3><span>In this course you will learn:</span></h3>
            <ol>
                <li><a><span>1</span>How an accountability partner can help you in school.</a></li>
                <li><a><span>2</span>What to look for in a good accountability partner.</a></li>
                <li><a><span>3</span>How to work with an accountability partner.</a></li>
            </ol>
        </div>
        <div class="player-divider">
            <div class="player-wrapper">
                <?php foreach ($view['assetic']->image(['@Course1Bundle/Resources/public/images/intro2.jpg'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                    <img src="<?php echo $view->escape($url) ?>" alt="LOGO" />
                <?php endforeach; ?>
            </div>
        </div>
        <div class="highlighted-link">
            <a href="<?php print $view['router']->generate('course1_partners', ['_step' => 1]); ?>" class="more">Launch</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>
<?php $view['slots']->stop(); ?>
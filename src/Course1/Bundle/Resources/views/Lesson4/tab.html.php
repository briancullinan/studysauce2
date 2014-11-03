<?php $view->extend('Course1Bundle:Shared:layout.html.php');

 $view['slots']->start('body'); ?>
<div class="panel-pane course1 step0" id="lesson4">
    <div class="pane-content">
        <h2>Distractions <span class="time">10<small> minutes</small></span></h2>
        <div class="learn-bullets">
            <h3><span>In this course you will learn:</span></h3>
            <ol>
                <li><a><span>1</span>The science behind multitasking</a></li>
                <li><a><span>2</span>The downside of multitasking</a></li>
                <li><a><span>3</span>How distractions affect your performance</a></li>
                <li><a><span>4</span>How long an interruption affects you when you study</a></li>
            </ol>
        </div>
        <div class="player-divider">
            <div class="player-wrapper">
                <iframe id="ytplayer" src="https://www.youtube.com/embed/fkF0jueJYDQ?rel=0&amp;controls=0&amp;modestbranding=1&amp;showinfo=0&amp;enablejsapi=1&amp;playerapiid=ytplayer"></iframe>
            </div>
        </div>
        <div class="highlighted-link">
            <a href="<?php print $view['router']->generate('lesson4', ['_step' => 1]); ?>" class="more">Launch</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>
<?php $view['slots']->stop(); ?>

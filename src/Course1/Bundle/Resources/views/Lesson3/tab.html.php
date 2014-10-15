<?php $view->extend('Course1Bundle:Lesson1:layout.html.php');

 $view['slots']->start('body'); ?>
<div class="panel-pane course1 step0" id="lesson3">
    <div class="pane-content">
        <h2>In this course you will learn:<span class="time">10<small> minutes</small></span></h2>

        <ol>
            <li><a ><span>1</span>Why procrastination leads to a cycle of cramming</a></li>
            <li><a ><span>2</span>How the human brain creates memories</a></li>
            <li><a ><span>3</span>How to defeat the procrastination to cramming cycle</a></li>
            <li><a ><span>4</span>Two tools that will help you stop procrastination</a></li>
        </ol>
        <div class="player-divider">
            <div class="player-wrapper">
                <iframe id="ytplayer" src="https://www.youtube.com/embed/Un_7_J0I0p8?rel=0&controls=0&modestbranding=1&showinfo=0&enablejsapi=1&playerapiid=ytplayer" frameborder="0"></iframe>
            </div>
        </div>

        <div class="highlighted-link">
            <a href="<?php print $view['router']->generate('lesson3', ['_step' => 1]); ?>" class="more">Launch</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>
<?php $view['slots']->stop(); ?>

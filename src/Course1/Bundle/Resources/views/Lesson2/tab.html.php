<?php $view->extend('Course1Bundle:Shared:layout.html.php');

 $view['slots']->start('body'); ?>
<div class="panel-pane course1 step0" id="lesson2">
    <div class="pane-content">
        <h2>In this course you will learn:<span class="time">10<small> minutes</small></span></h2>

        <ol>
            <li><a ><span>1</span>Why goal setting is so powerful</a></li>
            <li><a ><span>2</span>How your brain works while you are studying</a></li>
            <li><a ><span>3</span>How to set effective goals</a></li>
            <li><a ><span>4</span>How to stay motivated with intrinsic and extrinsic rewards</a></li>
        </ol>
        <div class="player-divider">
            <div class="player-wrapper">
                <iframe id="ytplayer" src="https://www.youtube.com/embed/mdU2t7VOFDY?rel=0&amp;controls=0&amp;modestbranding=1&amp;showinfo=0&amp;enablejsapi=1&amp;playerapiid=ytplayer"></iframe>
            </div>
        </div>

        <div class="highlighted-link">
            <a href="<?php print $view['router']->generate('lesson2', ['_step' => 1]); ?>" class="more">Launch</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>
<?php $view['slots']->stop(); ?>

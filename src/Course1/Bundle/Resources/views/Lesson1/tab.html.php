<?php use Symfony\Component\HttpKernel\Controller\ControllerReference;

 $view->extend('Course1Bundle:Shared:layout.html.php');

 $view['slots']->start('body'); ?>
<div class="panel-pane course1 step0" id="lesson1">

    <div class="pane-content">

        <h2>In this course you will learn:<span class="time">10<small> minutes</small></span></h2>

        <ol>
            <li><a ><span>1</span>Some common misconceptions about studying</a></li>
            <li><a ><span>2</span>Why studying is important</a></li>
            <li><a ><span>3</span>How Study Sauce will help you take control of your academic life</a></li>
        </ol>
        <div class="player-divider">
            <div class="player-wrapper">
                <iframe id="ytplayer" src="https://www.youtube.com/embed/KgCJ5yDISNs?rel=0&amp;controls=0&amp;modestbranding=1&amp;showinfo=0&amp;enablejsapi=1&amp;playerapiid=ytplayer"></iframe>
            </div>
        </div>

        <div class="highlighted-link">
            <a href="<?php print $view['router']->generate('lesson1', ['_step' => 1]); ?>" class="more">Launch</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>

</div>

<?php $view['slots']->stop(); ?>

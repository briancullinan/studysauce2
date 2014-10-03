
<?php $view->extend('Course1Bundle:Lesson1:layout.html.php') ?>

<?php $view['slots']->start('body'); ?>
<div class="panel-pane course1 step0" id="lesson1">

    <div class="pane-content">

        <h2>In this course you will learn:</h2>

        <ol>
            <li><a ><span>1</span>Introduction to StudySauce</a></li>
            <li><a ><span>2</span>How to progress</a></li>
            <li><a ><span>3</span>Where to start</a></li>
            <li><a ><span>4</span>Study schedule</a></li>
        </ol>
        <div class="player-divider">
            <div class="player-wrapper">
                <iframe id="ytplayer" src="https://www.youtube.com/embed/vJG9PDaXNaQ?rel=0&controls=0&modestbranding=1&showinfo=0&enablejsapi=1&playerapiid=ytplayer" frameborder="0"></iframe>
            </div>
        </div>

        <div class="highlighted-link">
            <a href="<?php print $view['router']->generate('lesson1', ['_step' => 1]); ?>" class="more">Launch</a>
        </div>
    </div>

</div>
<?php $view['slots']->stop(); ?>

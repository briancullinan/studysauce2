<?php use Symfony\Component\HttpKernel\Controller\ControllerReference;

 $view->extend('Course1Bundle:Lesson1:layout.html.php');

 $view['slots']->start('body'); ?>
<div class="panel-pane course1 step0" id="lesson1">

    <div class="pane-content">

        <h2>In this course you will learn:</h2>

        <ol>
            <li><a ><span>1</span>Some common misconceptions about studying</a></li>
            <li><a ><span>2</span>Why studying is important</a></li>
            <li><a ><span>3</span>How Study Sauce will help you take control of your academic life</a></li>
        </ol>
        <div class="player-divider">
            <div class="player-wrapper">
                <iframe id="ytplayer" src="https://www.youtube.com/embed/vJG9PDaXNaQ?rel=0&controls=0&modestbranding=1&showinfo=0&enablejsapi=1&playerapiid=ytplayer" frameborder="0"></iframe>
            </div>
        </div>

        <div class="highlighted-link">
            <a href="<?php print $view['router']->generate('lesson1', ['_step' => 1]); ?>" class="more">Launch</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>

</div>

<?php echo $view['actions']->render(new ControllerReference('Course1Bundle:Lesson1:wizard', ['_step' => 1, '_format' => 'tab']), ['strategy' => 'sinclude']);

 echo $view['actions']->render(new ControllerReference('Course1Bundle:Lesson1:wizard', ['_step' => 2, '_format' => 'tab']), ['strategy' => 'sinclude']);

 echo $view['actions']->render(new ControllerReference('Course1Bundle:Lesson1:wizard', ['_step' => 3, '_format' => 'tab']), ['strategy' => 'sinclude']);

 echo $view['actions']->render(new ControllerReference('Course1Bundle:Lesson1:wizard', ['_step' => 4, '_format' => 'tab']), ['strategy' => 'sinclude']);

 $view['slots']->stop(); ?>

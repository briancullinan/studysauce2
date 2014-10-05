<?php use Symfony\Component\HttpKernel\Controller\ControllerReference;

 $view->extend('Course1Bundle:Lesson1:layout.html.php');

 $view['slots']->start('body'); ?>
<div class="panel-pane course1" id="lesson1-step1">

    <div class="pane-content">

        <div class="player-wrapper">
            <?php /* &origin=http%3A%2F%2Flocalhost */ ?>
            <iframe id="ytplayer" src="https://www.youtube.com/embed/vJG9PDaXNaQ?rel=0&controls=0&modestbranding=1&showinfo=0&enablejsapi=1&playerapiid=ytplayer" frameborder="0"></iframe>
        </div>
        <div class="highlighted-link">
            <a href="<?php print $view['router']->generate('lesson1', ['_step' => 2]); ?>" class="more">Next</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php echo $view['actions']->render(new ControllerReference('Course1Bundle:Lesson1:wizard', ['_step' => 2, '_format' => 'tab']), ['strategy' => 'sinclude']);

 echo $view['actions']->render(new ControllerReference('Course1Bundle:Lesson1:wizard', ['_step' => 0, '_format' => 'tab']), ['strategy' => 'sinclude']);

 echo $view['actions']->render(new ControllerReference('Course1Bundle:Lesson1:wizard', ['_step' => 3, '_format' => 'tab']), ['strategy' => 'sinclude']);

 echo $view['actions']->render(new ControllerReference('Course1Bundle:Lesson1:wizard', ['_step' => 4, '_format' => 'tab']), ['strategy' => 'sinclude']);

 $view['slots']->stop(); ?>

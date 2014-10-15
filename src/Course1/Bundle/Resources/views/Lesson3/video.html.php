
<?php $view->extend('Course1Bundle:Lesson1:layout.html.php');

 $view['slots']->start('body'); ?>
<div class="panel-pane course1 step1" id="lesson3-step1">

    <div class="pane-content">

        <div class="player-wrapper">
            <?php /* &origin=http%3A%2F%2Flocalhost */ ?>
            <iframe id="ytplayer" src="https://www.youtube.com/embed/Un_7_J0I0p8?rel=0&controls=0&modestbranding=1&showinfo=0&enablejsapi=1&playerapiid=ytplayer" frameborder="0"></iframe>
        </div>
        <div class="highlighted-link">
            <a href="<?php print $view['router']->generate('lesson3', ['_step' => 2]); ?>" class="more">Next</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php $view['slots']->stop(); ?>

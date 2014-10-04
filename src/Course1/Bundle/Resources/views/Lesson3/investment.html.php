
<?php $view->extend('Course1Bundle:Lesson1:layout.html.php') ?>

<?php $view['slots']->start('body'); ?>
<div class="panel-pane course1" id="lesson3-step4">

    <div class="pane-content">

        <h2>Great job!</h2>
        <h3>Now let's put your knowledge to action.</h3>
        <h3>Assignment:</h3>
        <div class="highlighted-link">
            <a href="<?php print $view['router']->generate('schedule'); ?>" class="more">Go</a>
        </div>
    </div>
</div>

<?php $view['slots']->stop(); ?>

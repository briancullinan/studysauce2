
<?php $view->extend('Course1Bundle:Lesson1:layout.html.php') ?>

<?php $view['slots']->start('body'); ?>
<div class="panel-pane course1 step3" id="lesson3-step3">
    <div class="pane-content" id="new-award">
        <div>
            <span class="badge">&nbsp;</span>
            <div class="description">
                <h3>You have been awarded the <strong>Pulse Detected</strong> badge.</h3>
                <div class="badge-date"><strong>Date achieved:</strong></div>
                <p>Yep, you are alive.  Now let's get started.  Check in and begin a study session.</p>
                <h4>Badge description</h4>
                <p>Our Study Detection Software will automatically guide you to become a great studier.  Enter your class information <span>here</span> to unlock this badge.</p>
            </div>
            <div class="highlighted-link">
                <a href="<?php print $view['router']->generate('lesson3', ['_step' => 4]); ?>" class="more">Next</a>
            </div>
        </div>
    </div>
</div>

<?php $view['slots']->stop(); ?>

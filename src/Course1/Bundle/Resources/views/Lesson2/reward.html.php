
<?php $view->extend('Course1Bundle:Lesson1:layout.html.php');

 $view['slots']->start('body'); ?>
<div class="panel-pane course1 step3" id="lesson2-step3">
    <div class="pane-content" id="new-award">
        <div>
            <span class="badge setup-hours">&nbsp;</span>
            <div class="description">
                <h3>You have been awarded the <strong>Goal Setting Badge</strong> badge.</h3>
                <p>Congratulations on learning how to set effective goals! You will be amazed at the power that simple goal setting will have on your academic performance.</p>
            </div>
            <div class="highlighted-link">
                <a href="<?php print $view['router']->generate('lesson2', ['_step' => 4]); ?>" class="more">Next</a>
            </div>
            <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
        </div>
    </div>
</div>

<?php $view['slots']->stop(); ?>

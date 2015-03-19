<?php
use Course1\Bundle\Entity\Course1;

$view->extend('Course1Bundle:Shared:layout.html.php');

/** @var Course1 $course */
$complete = $course->getEnjoyed() !== null;

 $view['slots']->start('body'); ?>
<div class="panel-pane course1 step2 <?php print ($complete ? ' right' : ''); ?>" id="course1_upgrade-step2">
    <div class="pane-content">
        <h2>Now let's see how much you remember</h2>
        <h3>Have you enjoyed the Study Sauce course?</h3>
        <div class="questions">
            <label class="radio"><input name="quiz-enjoyed" type="radio" value="1" <?php print ($course->getEnjoyed() ? 'checked="checked"' : ''); ?> /><i></i><span>Yes</span></label>
            <label class="radio"><input name="quiz-enjoyed" type="radio" value="0" <?php print ($course->getEnjoyed() === false ? 'checked="checked"' : ''); ?> /><i></i><span>No</span></label>
        </div>
        <?php if($complete) { ?>
        <div class="results">
            <p>We hope you have enjoyed the course to this point.  Even if you decide not to upgrade, you are welcome to continue to use our free tools.</p>
        </div>
        <?php } ?>
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>"/>
        <div class="highlighted-link <?php print ($complete ? ' valid' : ' invalid'); ?>">
            <div class="invalid-only">You must complete all fields before moving on.</div>
            <a href="#submit-quiz" class="more">Submit</a>
            <a href="<?php print $view['router']->generate('course1_upgrade', ['_step' => 3]); ?>" class="more">Next</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php $view['slots']->stop(); ?>

<?php
use Course2\Bundle\Entity\StudyPlan;

$view->extend('Course2Bundle:Shared:layout.html.php');

/** @var StudyPlan $quiz */
$complete = !empty($quiz->getMultiply()) && !empty($quiz->getProcrastination()) && !empty($quiz->getStudySessions()) &&
    !empty($quiz->getStickPlan());

 $view['slots']->start('body'); ?>
<div class="panel-pane course2 step2 <?php print ($complete ? ' right' : ''); ?>" id="course2_study_plan-step2">
    <div class="pane-content">
        <h2>Now let's see how much you remember</h2>
        <h3>Multiply your class hours by ___ to get the total number of hours you should study per week.</h3>
        <div class="questions">
            <label class="radio"><input name="quiz-multiply" type="radio" value="1" <?php print ($quiz->getMultiply() == 1 ? 'checked="checked"' : ''); ?>><i></i><span>1</span></label>
            <label class="radio"><input name="quiz-multiply" type="radio" value="2" <?php print ($quiz->getMultiply() == 2 ? 'checked="checked"' : ''); ?>><i></i><span>2</span></label>
            <label class="radio"><input name="quiz-multiply" type="radio" value="3" <?php print ($quiz->getMultiply() == 3 ? 'checked="checked"' : ''); ?>><i></i><span>3</span></label>
            <label class="radio"><input name="quiz-multiply" type="radio" value="4" <?php print ($quiz->getMultiply() == 4 ? 'checked="checked"' : ''); ?>><i></i><span>4</span></label>
        </div>
        <?php if($complete) { ?>
        <div class="results">
            <p>Multiply by 3 to get the number of weekly study hours.</p>
        </div>
        <?php } ?>
        <h3>How does building a study plan help you stop procrastinating?</h3>
        <div class="questions">
            <label class="input"><textarea name="quiz-procrastination"><?php print $view->escape($quiz->getProcrastination()); ?></textarea></label>
        </div>
        <?php if($complete) { ?>
        <div class="results">
            <p>You commit yourself to studying at a time when you are motivated instead of waiting until you can find reasons not to study.</p>
        </div>
        <?php } ?>
        <h3>How should you use the time in your free study sessions?</h3>
        <div class="questions">
            <label class="input"><textarea name="quiz-studySessions"><?php print $view->escape($quiz->getStudySessions()); ?></textarea></label>
        </div>
        <?php if($complete) { ?>
        <div class="results">
            <p>This time should be allocated to give you the flexibility to focus on whichever class is most in need of attention at the time.  It is also a great time to work on bigger projects or papers that span several weeks.</p>
        </div>
        <?php } ?>
        <h3>What are two methods you can use to help yourself stick with a study plan?</h3>
        <div class="questions">
            <label class="input"><textarea name="quiz-stickPlan"><?php print $view->escape($quiz->getStickPlan()); ?></textarea></label>
        </div>
        <?php if($complete) { ?>
            <div class="results">
                <p>Starting immediately is a great way to get some momentum.  Additionally, choosing study locations ahead of time can help you keep your motivation.</p>
            </div>
        <?php } ?>
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>"/>
        <div class="highlighted-link <?php print ($complete ? ' valid' : ' invalid'); ?>">
            <div class="invalid-only">You must complete all fields before moving on.</div>
            <a href="#submit-quiz" class="more">Submit</a>
            <a href="<?php print $view['router']->generate('course2_study_plan', ['_step' => 3]); ?>" class="more">Next</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php $view['slots']->stop(); ?>

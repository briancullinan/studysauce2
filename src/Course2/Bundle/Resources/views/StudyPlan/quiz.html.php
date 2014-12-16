<?php
use Course2\Bundle\Entity\Quiz6;

$view->extend('Course2Bundle:Shared:layout.html.php');

/** @var Quiz6 $quiz */
$complete = !empty($quiz->getHelp()) && !empty($quiz->getAttribute()) && !empty($quiz->getOften()) && !empty($quiz->getUsage());

 $view['slots']->start('body'); ?>
<div class="panel-pane course2 step2 <?php print ($complete ? ' right' : ''); ?>" id="course2_study_plan-step2">
    <div class="pane-content">
        <h2>Now let's see how much you remember</h2>
        <h3>Select the two main ways an accountability partners can help you in school from the list below:</h3>
        <div class="questions">
            <label class="checkbox"><input name="quiz-partners-help" type="checkbox" value="motivate" <?php print (in_array('motivate', $quiz->getHelp()) ? 'checked="checked"' : ''); ?>><i></i><span>To motivate you</span></label>
            <label class="checkbox"><input name="quiz-partners-help" type="checkbox" value="tutor" <?php print (in_array('tutor', $quiz->getHelp()) ? 'checked="checked"' : ''); ?>><i></i><span>Tutoring for your most difficult classes</span></label>
            <label class="checkbox"><input name="quiz-partners-help" type="checkbox" value="focus" <?php print (in_array('focus', $quiz->getHelp()) ? 'checked="checked"' : ''); ?>><i></i><span>Help keep you focused</span></label>
            <label class="checkbox"><input name="quiz-partners-help" type="checkbox" value="incentive" <?php print (in_array('incentive', $quiz->getHelp()) ? 'checked="checked"' : ''); ?>><i></i><span>To incentivize you to achieve your goals</span></label>
        </div>
        <?php if($complete) { ?>
        <div class="results">
            <p>Your accountability partner may be able to help you in several different ways, but the two main ways are by keeping you focused and motivated.</p>
        </div>
        <?php } ?>
        <h3>Which of the following is not a key attribute to look for when choosing your accountability partner?</h3>
        <div class="questions">
            <label class="radio"><input name="quiz-partners-attribute" type="radio" value="trust" <?php print ($quiz->getAttribute() == 'trust' ? 'checked="checked"' : ''); ?>><i></i><span>Someone you trust.</span></label>
            <label class="radio"><input name="quiz-partners-attribute" type="radio" value="challenge" <?php print ($quiz->getAttribute() == 'challenge' ? 'checked="checked"' : ''); ?>><i></i><span>Someone that will challenge you.</span></label>
            <label class="radio"><input name="quiz-partners-attribute" type="radio" value="knows" <?php print ($quiz->getAttribute() == 'knows' ? 'checked="checked"' : ''); ?>><i></i><span>Someone that knows you best.</span></label>
            <label class="radio"><input name="quiz-partners-attribute" type="radio" value="celebrate" <?php print ($quiz->getAttribute() == 'celebrate' ? 'checked="checked"' : ''); ?>><i></i><span>Someone that will celebrate your successes.</span></label>
        </div>
        <?php if($complete) { ?>
        <div class="results">
            <p>Take time to choose your accountability partner.  You should have already established trust with the person because you will need them to challenge you and to celebrate successes with you.</p>
        </div>
        <?php } ?>
        <h3>How often should you talk with your accountability partner?</h3>
        <div class="questions">
            <label class="input"><span>1</span><input name="quiz-partners-often" type="text" value="<?php print $view->escape($quiz->getOften()); ?>"></label>
        </div>
        <?php if($complete) { ?>
        <div class="results">
            <p>Ideally, you can communicate with your accountability partner on a weekly basis.</p>
        </div>
        <?php } ?>
        <h3>According to the video, which of the following are examples of other ways accountability partners are used?</h3>
        <div class="questions">
            <label class="checkbox"><input name="quiz-partners-usage" type="checkbox" value="drive" <?php print (in_array('drive', $quiz->getUsage()) ? 'checked="checked"' : ''); ?>><i></i><span>Learning to drive</span></label>
            <label class="checkbox"><input name="quiz-partners-usage" type="checkbox" value="dieting" <?php print (in_array('dieting', $quiz->getUsage()) ? 'checked="checked"' : ''); ?>><i></i><span>Dieting</span></label>
            <label class="checkbox"><input name="quiz-partners-usage" type="checkbox" value="gyms" <?php print (in_array('gyms', $quiz->getUsage()) ? 'checked="checked"' : ''); ?>><i></i><span>Gyms</span></label>
            <label class="checkbox"><input name="quiz-partners-usage" type="checkbox" value="churches" <?php print (in_array('churches', $quiz->getUsage()) ? 'checked="checked"' : ''); ?>><i></i><span>Churches</span></label>
        </div>
        <?php if($complete) { ?>
            <div class="results">
                <p>Although there are many other ways that accountability partners are used, the video specifically highlights gyms, dieting, and churches.</p>
            </div>
        <?php } ?>
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>"/>
        <div class="highlighted-link <?php print ($complete ? ' valid' : ' invalid'); ?>">
            <a href="#submit-quiz" class="more">Submit</a>
            <a href="<?php print $view['router']->generate('course2_study_plan', ['_step' => 3]); ?>" class="more">Next</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php $view['slots']->stop(); ?>

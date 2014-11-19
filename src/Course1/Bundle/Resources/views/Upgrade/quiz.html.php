<?php
use Course1\Bundle\Entity\Quiz2;

$view->extend('Course1Bundle:Shared:layout.html.php');

/** @var Quiz2 $quiz */
$complete = !empty($quiz->getGoalPerformance()) &&
    !empty($quiz->getSpecific()) && !empty($quiz->getMeasurable()) && !empty($quiz->getAchievable()) &&
    !empty($quiz->getRelevant()) && !empty($quiz->getTimeBound()) && !empty($quiz->getIntrinsic()) &&
    !empty($quiz->getExtrinsic());

 $view['slots']->start('body'); ?>
<div class="panel-pane course1 step2 <?php print ($complete ? ' right' : ''); ?>" id="course1_upgrade-step2">
    <div class="pane-content">
        <h2>Now let's see how much you remember</h2>
        <h3>How much more likely are you to perform at a higher level if you set specific and challenging goals?<?php
            //if($complete)
            //    print ($quiz->getGoalPerformance() == '90'
            //        ? '<span class="answer correct">Correct!</span>'
            //        : '<span class="answer wrong">Wrong.</span>');
            ?></h3>
        <div class="questions">
            <label class="radio"><input name="quiz-goal-performance" type="radio" value="20" <?php print ($quiz->getGoalPerformance() == '20' ? 'checked="checked"' : ''); ?>><i></i><span>20%</span></label>
            <label class="radio"><input name="quiz-goal-performance" type="radio" value="40" <?php print ($quiz->getGoalPerformance() == '40' ? 'checked="checked"' : ''); ?>><i></i><span>40%</span></label>
            <label class="radio"><input name="quiz-goal-performance" type="radio" value="60" <?php print ($quiz->getGoalPerformance() == '60' ? 'checked="checked"' : ''); ?>><i></i><span>60%</span></label>
            <label class="radio"><input name="quiz-goal-performance" type="radio" value="90" <?php print ($quiz->getGoalPerformance() == '90' ? 'checked="checked"' : ''); ?>><i></i><span>90%</span></label>
        </div>
        <?php if($complete) { ?>
        <div class="results">
            <p>You are 90% more likely to perform at a higher level if you set specific and challenging goals.</p>
        </div>
        <?php } ?>
        <h3>What does the SMART acronym stand for?<?php
            /*if($complete)
                print (strpos(strtolower($quiz->getSpecific()), 'specific') > -1 &&
                        strpos(strtolower($quiz->getMeasurable()), 'measurable') > -1 &&
                        strpos(strtolower($quiz->getAchievable()), 'achievable') > -1 &&
                        strpos(strtolower($quiz->getRelevant()), 'relevant') > -1 &&
                        strpos(strtolower($quiz->getTimeBound()), 'time') > -1 &&
                        strpos(strtolower($quiz->getTimeBound()), 'bound', strpos(strtolower($quiz->getTimeBound()), 'time') > -1) > -1
                    ? '<span class="answer correct">Correct!</span>'
                    : '<span class="answer wrong">Wrong.</span>'); */
            ?></h3>
        <div class="questions">
            <label class="input"><span>S</span><input name="quiz-smart-acronym-S" type="text" value="<?php print $view->escape($quiz->getSpecific()); ?>"></label>
            <label class="input"><span>M</span><input name="quiz-smart-acronym-M" type="text" value="<?php print $view->escape($quiz->getMeasurable()); ?>"></label>
            <label class="input"><span>A</span><input name="quiz-smart-acronym-A" type="text" value="<?php print $view->escape($quiz->getAchievable()); ?>"></label>
            <label class="input"><span>R</span><input name="quiz-smart-acronym-R" type="text" value="<?php print $view->escape($quiz->getRelevant()); ?>"></label>
            <label class="input"><span>T</span><input name="quiz-smart-acronym-T" type="text" value="<?php print $view->escape($quiz->getTimeBound()); ?>"></label>
        </div>
        <?php if($complete) { ?>
        <div class="results">
            <p>Answers are specific, measurable, achievable, relevant, time-bound</p>
        </div>
        <?php } ?>
        <h3>What are the two types of motivation?<?php
            /*if($complete)
                print (strpos(strtolower($quiz->getIntrinsic()), 'intrinsic') > -1 &&
                        strpos(strtolower($quiz->getExtrinsic()), 'extrinsic') > -1
                    ? '<span class="answer correct">Correct!</span>'
                    : '<span class="answer wrong">Wrong.</span>');*/
            ?></h3>
        <div class="questions">
            <label class="input"><span>1</span><input name="quiz-motivation-I" type="text" value="<?php print $view->escape($quiz->getIntrinsic()); ?>"></label>
            <label class="input"><span>2</span><input name="quiz-motivation-E" type="text" value="<?php print $view->escape($quiz->getExtrinsic()); ?>"></label>
        </div>
        <?php if($complete) { ?>
        <div class="results">
            <p>The two types of motivation are intrinsic and extrinsic motivation.</p>
            <p>Intrinsic motivation is motivation that comes from within.  Ex. studying because you want the satisfaction of learning something new.</p>
            <p>Extrinsic motivation is a reward that comes externally.  Ex. studying in order to get a good grade.</p>
        </div>
        <?php } ?>
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>"/>
        <div class="highlighted-link">
            <a href="#submit-quiz" class="more">Submit</a>
            <a href="<?php print $view['router']->generate('course1_upgrade', ['_step' => 3]); ?>" class="more">Next</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php $view['slots']->stop(); ?>

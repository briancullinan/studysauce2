<?php
use Course2\Bundle\Entity\StudyMetrics;

$view->extend('Course2Bundle:Shared:layout.html.php');

/** @var StudyMetrics $quiz */
$complete = !empty($quiz->getTrackHours()) && $quiz->getDoingWell() !== null && $quiz->getAllTogether() !== null;

$view['slots']->start('body'); ?>
<div class="panel-pane course2 step2 <?php print ($complete ? ' right' : ''); ?>" id="course2_study_metrics-step2">
    <div class="pane-content">
        <h2>Now let's see how much you remember</h2>
        <h3>Which of the following are reasons to track your study hours?</h3>
        <div class="questions">
            <label class="checkbox"><input name="quiz-track-hours" type="checkbox" value="guarantee" <?php print (in_array('guarantee', $quiz->getTrackHours()) ? 'checked="checked"' : ''); ?>><i></i><span>By studying a certain number of hours per week, you guarantee you will be prepared.</span></label>
            <label class="checkbox"><input name="quiz-track-hours" type="checkbox" value="procrastination" <?php print (in_array('procrastination', $quiz->getTrackHours()) ? 'checked="checked"' : ''); ?>><i></i><span>Tracking your study time helps you avoid procrastination because you can easily see how much time you have been studying.</span></label>
            <label class="checkbox"><input name="quiz-track-hours" type="checkbox" value="tracking" <?php print (in_array('tracking', $quiz->getTrackHours()) ? 'checked="checked"' : ''); ?>><i></i><span>Tracking your hours helps cement your new good study habits.</span></label>
            <label class="checkbox"><input name="quiz-track-hours" type="checkbox" value="problems" <?php print (in_array('problems', $quiz->getTrackHours()) ? 'checked="checked"' : ''); ?>><i></i><span>It helps you identify any problems early so you have the time to fix them and they don’t become big problems.</span></label>
        </div>
        <?php if($complete) { ?>
        <div class="results">
            <p>Tracking helps you in several ways, but it does not guarantee that you are prepared. The total number of hours is less important that the quality of those hours spent studying.</p>
        </div>
        <?php } ?>
        <h3>Your school has many people that whose sole job is to make sure you are doing well in school.</h3>
        <div class="questions">
            <label class="radio"><input name="quiz-doing-well" type="radio" value="1" <?php print ($quiz->getDoingWell() ? 'checked="checked"' : ''); ?>><i></i><span>True</span></label>
            <label class="radio"><input name="quiz-doing-well" type="radio" value="0" <?php print ($quiz->getDoingWell() === false ? 'checked="checked"' : ''); ?>><i></i><span>False</span></label>
        </div>
        <?php if($complete) { ?>
        <div class="results">
            <p>True - Help provide them some job security and find out what resources are available for you. No need to bang your head against the wall unnecessarily.</p>
        </div>
        <?php } ?>
        <h3>Why does everyone else look like they have it all together?</h3>
        <div class="questions">
            <label class="radio"><input name="quiz-all-together" type="radio" value="1" <?php print ($quiz->getAllTogether() ? 'checked="checked"' : ''); ?>><i></i><span>True</span></label>
            <label class="radio"><input name="quiz-all-together" type="radio" value="0" <?php print ($quiz->getAllTogether() === false ? 'checked="checked"' : ''); ?>><i></i><span>False</span></label>
        </div>
        <?php if($complete) { ?>
        <div class="results">
            <p>They don't. They just typically put on a brave face so that you will think they are smart. Everyone struggles in school, so don't get down on yourself.</p>
        </div>
        <?php } ?>
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>"/>
        <div class="highlighted-link <?php print ($complete ? ' valid' : ' invalid'); ?>">
            <a href="#submit-quiz" class="more">Submit</a>
            <a href="<?php print $view['router']->generate('course2_study_metrics', ['_step' => 3]); ?>" class="more">Next</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php $view['slots']->stop(); ?>

<?php
use Course1\Bundle\Entity\Quiz4;

$view->extend('Course1Bundle:Shared:layout.html.php');

/** @var Quiz4 $quiz */
$complete = !empty($quiz->getMultitask()) &&
    !empty($quiz->getLowerScore()) && !empty($quiz->getDistraction()) && !empty($quiz->getDownside());

 $view['slots']->start('body'); ?>
<div class="panel-pane course1 step2 <?php print ($complete ? ' right' : ''); ?>" id="course1_distractions-step2">
    <div class="pane-content">
        <h2>Now let's see how much you remember</h2>
        <h3>True or False.  You are excellent at multitasking.<?php
            /*if($complete)
                print ($quiz->getMultitask() == '0'
                    ? '<span class="answer correct">Correct!</span>'
                    : '<span class="answer wrong">Wrong.</span>');*/
            ?></h3>
        <div class="questions">
            <label class="radio"><input name="quiz-multitask" type="radio" value="true" <?php print ($quiz->getMultitask() == 'true' ? 'checked="checked"' : ''); ?>><i></i><span>True</span></label>
            <label class="radio"><input name="quiz-multitask" type="radio" value="false" <?php print ($quiz->getMultitask() == 'false' ? 'checked="checked"' : ''); ?>><i></i><span>False</span></label>
        </div>
        <?php if($complete) { ?>
        <div class="results">
            <p>You are not good at multitasking despite what you believe.  No one is good at multitasking because that is not how the brain works.</p>
        </div>
        <?php } ?>
        <h3>Which of the following is <i style="text-decoration: underline">not</i> a downside of multitasking?<?php
            /*if($complete)
                print ($quiz->getDownside() == 'shorter'
                    ? '<span class="answer correct">Correct!</span>'
                    : '<span class="answer wrong">Wrong.</span>');*/
            ?></h3>
        <div class="questions">
            <label class="radio"><input name="quiz-downside" type="radio" value="tired" <?php print ($quiz->getDownside() == 'tired' ? 'checked="checked"' : ''); ?>><i></i><span>Get tired more easily</span></label>
            <label class="radio"><input name="quiz-downside" type="radio" value="shorter" <?php print ($quiz->getDownside() == 'shorter' ? 'checked="checked"' : ''); ?>><i></i><span>Shorter memory of material</span></label>
            <label class="radio"><input name="quiz-downside" type="radio" value="remember" <?php print ($quiz->getDownside() == 'remember' ? 'checked="checked"' : ''); ?>><i></i><span>Remember less</span></label>
            <label class="radio"><input name="quiz-downside" type="radio" value="longer" <?php print ($quiz->getDownside() == 'longer' ? 'checked="checked"' : ''); ?>><i></i><span>Takes longer to study</span></label>
        </div>
        <?php if($complete) { ?>
        <div class="results">
            <p>The downside of multitasking includes: getting tired more easily, remembering less when you study, and taking longer to study.</p>
        </div>
        <?php } ?>
        <h3>How much lower do students interrupted by technology score on tests (in research studies)?<?php
            /*if($complete)
                print ($quiz->getLowerScore() == '20'
                    ? '<span class="answer correct">Correct!</span>'
                    : '<span class="answer wrong">Wrong.</span>');*/
            ?></h3>
        <div class="questions">
            <label class="radio"><input name="quiz-lower-score" type="radio" value="10" <?php print ($quiz->getLowerScore() == '10' ? 'checked="checked"' : ''); ?>><i></i><span>10%</span></label>
            <label class="radio"><input name="quiz-lower-score" type="radio" value="20" <?php print ($quiz->getLowerScore() == '20' ? 'checked="checked"' : ''); ?>><i></i><span>20%</span></label>
            <label class="radio"><input name="quiz-lower-score" type="radio" value="30" <?php print ($quiz->getLowerScore() == '30' ? 'checked="checked"' : ''); ?>><i></i><span>30%</span></label>
            <label class="radio"><input name="quiz-lower-score" type="radio" value="40" <?php print ($quiz->getLowerScore() == '40' ? 'checked="checked"' : ''); ?>><i></i><span>40%</span></label>
        </div>
        <?php if($complete) { ?>
        <div class="results">
            <p>Research shows that students that have technological distractions score 20% lower on tests than their peers without distractions.  That is like dropping from an A to a C!</p>
        </div>
        <?php } ?>
        <h3>How long can a text message distract you from your optimal study state?<?php
            /*if($complete)
                print ($quiz->getDistraction() == '40'
                    ? '<span class="answer correct">Correct!</span>'
                    : '<span class="answer wrong">Wrong.</span>');*/
            ?></h3>
        <div class="questions">
            <label class="radio"><input name="quiz-distraction" type="radio" value="3" <?php print ($quiz->getDistraction() == '3' ? 'checked="checked"' : ''); ?>><i></i><span>1-3 minutes</span></label>
            <label class="radio"><input name="quiz-distraction" type="radio" value="15" <?php print ($quiz->getDistraction() == '15' ? 'checked="checked"' : ''); ?>><i></i><span>5-15 minutes</span></label>
            <label class="radio"><input name="quiz-distraction" type="radio" value="40" <?php print ($quiz->getDistraction() == '40' ? 'checked="checked"' : ''); ?>><i></i><span>25-40 minutes</span></label>
            <label class="radio"><input name="quiz-distraction" type="radio" value="60" <?php print ($quiz->getDistraction() == '60' ? 'checked="checked"' : ''); ?>><i></i><span>45-60 minutes</span></label>
        </div>
        <?php if($complete) { ?>
        <div class="results">
            <p>Turn off your phone when you study!!!  It may seem like you can get back into the swing of things in 1-3 minutes, but the research shows that it takes 25-40 minutes.  The phone is your greatest enemy while studying and whatever is on it can wait until you are ready to take a study break.</p>
        </div>
        <?php } ?>
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>"/>
        <div class="highlighted-link <?php print ($complete ? ' valid' : ' invalid'); ?>">
            <a href="#submit-quiz" class="more">Submit</a>
            <a href="<?php print $view['router']->generate('course1_distractions', ['_step' => 3]); ?>" class="more">Next</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php $view['slots']->stop(); ?>

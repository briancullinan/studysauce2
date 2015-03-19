<?php
use Course3\Bundle\Entity\Strategies;

$view->extend('Course3Bundle:Shared:layout.html.php');

/** @var Strategies $quiz */
$complete = !empty($quiz->getSelfTesting());

$view['slots']->start('body'); ?>
<div class="panel-pane course3 step2 <?php print ($complete ? ' right' : ''); ?>" id="course3_strategies-step2">
    <div class="pane-content">
        <h2>Now let's see how much you remember</h2>
        <h3>Which of the following are examples of self-testing?</h3>
        <div class="questions">
            <label class="checkbox"><input name="quiz-self-testing" type="checkbox" value="reading" <?php print (in_array('reading', $quiz->getSelfTesting()) ? 'checked="checked"' : ''); ?>><i></i><span>Reading and rereading your notes</span></label>
            <label class="checkbox"><input name="quiz-self-testing" type="checkbox" value="flash" <?php print (in_array('flash', $quiz->getSelfTesting()) ? 'checked="checked"' : ''); ?>><i></i><span>Flash cards</span></label>
            <label class="checkbox"><input name="quiz-self-testing" type="checkbox" value="teaching" <?php print (in_array('teaching', $quiz->getSelfTesting()) ? 'checked="checked"' : ''); ?>><i></i><span>Teaching others</span></label>
            <label class="checkbox"><input name="quiz-self-testing" type="checkbox" value="practice" <?php print (in_array('practice', $quiz->getSelfTesting()) ? 'checked="checked"' : ''); ?>><i></i><span>Creating practice tests</span></label>
        </div>
        <?php if ($complete) { ?>
            <div class="results">
                <p>Creating and using flash cards, teaching others concepts, and creating practice tests are all great examples of self-testing.  Reading and rereading notes is a passive form of studying that is far less effective.</p>
            </div>
        <?php } ?>
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>"/>
        <div class="highlighted-link <?php print ($complete ? ' valid' : ' invalid'); ?>">
            <div class="invalid-only">You must complete all fields before moving on.</div>
            <a href="#submit-quiz" class="more">Submit</a>
            <a href="<?php print $view['router']->generate('course3_strategies', ['_step' => 3]); ?>" class="more">Next</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php $view['slots']->stop(); ?>

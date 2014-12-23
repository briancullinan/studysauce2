<?php
use Course3\Bundle\Entity\SpacedRepetition;

$view->extend('Course3Bundle:Shared:layout.html.php');

/** @var SpacedRepetition $quiz */
$complete = $quiz->getSpaceOut() !== null && !empty($quiz->getForgetting()) && !empty($quiz->getRevisiting()) && !empty($quiz->getAnotherName());

$view['slots']->start('body'); ?>
<div class="panel-pane course3 step2 <?php print ($complete ? ' right' : ''); ?>" id="course3_spaced_repetition-step2">
    <div class="pane-content">
        <h2>Now let's see how much you remember</h2>
        <h3>Spacing out your study sessions isn't important at all.</h3>
        <div class="questions">
            <label class="radio"><input name="quiz-space-out" type="radio" value="1" <?php print ($quiz->getSpaceOut() ? 'checked="checked"' : ''); ?>><i></i><span>True</span></label>
            <label class="radio"><input name="quiz-space-out" type="radio" value="0" <?php print ($quiz->getSpaceOut() === false ? 'checked="checked"' : ''); ?>><i></i><span>False</span></label>
        </div>
        <?php if ($complete) { ?>
            <div class="results">
                <p>False.  We just can't resist beating this dead horse.</p>
            </div>
        <?php } ?>
        <h3>What is the Forgetting Curve?</h3>
        <div class="questions">
            <label class="input"><textarea name="quiz-forgetting"><?php print $view->escape($quiz->getForgetting()); ?></textarea></label>
        </div>
        <?php if ($complete) { ?>
            <div class="results">
                <p>The forgetting curve illustrates how quickly we lose information from our memory if we do not return to reinforce it.  We forget almost everything we learn in a manner of days.</p>
            </div>
        <?php } ?>
        <h3>How often do we recommend you revisit your flash cards in spaced repetition study sessions?</h3>
        <div class="questions">
            <label class="radio"><input name="quiz-revisiting" type="radio" value="daily" <?php print ($quiz->getRevisiting() == 'daily' ? 'checked="checked"' : ''); ?>><i></i><span>Every day</span></label>
            <label class="radio"><input name="quiz-revisiting" type="radio" value="weekly" <?php print ($quiz->getRevisiting() == 'weekly' ? 'checked="checked"' : ''); ?>><i></i><span>Once a week</span></label>
            <label class="radio"><input name="quiz-revisiting" type="radio" value="biweekly" <?php print ($quiz->getRevisiting() == 'biweekly' ? 'checked="checked"' : ''); ?>><i></i><span>Every other week</span></label>
            <label class="radio"><input name="quiz-revisiting" type="radio" value="monthly" <?php print ($quiz->getRevisiting() == 'monthly' ? 'checked="checked"' : ''); ?>><i></i><span>Once a month</span></label>
        </div>
        <?php if ($complete) { ?>
            <div class="results">
                <p>We recommend going through the same material once a week for the first month.  After that, revisit as necessary.</p>
            </div>
        <?php } ?>
        <h3>Which of the following is <strong>not</strong> another name for Spaced Repetition?</h3>
        <div class="questions">
            <label class="radio"><input name="quiz-another-name" type="radio" value="practice" <?php print ($quiz->getAnotherName() == 'practice' ? 'checked="checked"' : ''); ?>><i></i><span>Spaced practice</span></label>
            <label class="radio"><input name="quiz-another-name" type="radio" value="distributed" <?php print ($quiz->getAnotherName() == 'distributed' ? 'checked="checked"' : ''); ?>><i></i><span>Distributed practice</span></label>
            <label class="radio"><input name="quiz-another-name" type="radio" value="blocked" <?php print ($quiz->getAnotherName() == 'blocked' ? 'checked="checked"' : ''); ?>><i></i><span>Blocked practice</span></label>
        </div>
        <?php if ($complete) { ?>
            <div class="results">
                <p>Spaced Repetition is also know as spaced practice and distributed practice.</p>
            </div>
        <?php } ?>
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>"/>
        <div class="highlighted-link <?php print ($complete ? ' valid' : ' invalid'); ?>">
            <a href="#submit-quiz" class="more">Submit</a>
            <a href="<?php print $view['router']->generate('course3_spaced_repetition', ['_step' => 3]); ?>" class="more">Next</a>
        </div>
        <ul class="tab-tracker">
            <li>&bullet;</li>
            <li>&bullet;</li>
            <li>&bullet;</li>
            <li>&bullet;</li>
            <li>&bullet;</li>
        </ul>
    </div>
</div>

<?php $view['slots']->stop(); ?>

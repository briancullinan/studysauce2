<?php
use Course2\Bundle\Entity\TestTaking;

$view->extend('Course2Bundle:Shared:layout.html.php');

/** @var TestTaking $quiz */
$complete = !empty($quiz->getIdeaCram()) && !empty($quiz->getBreathing()) && !empty($quiz->getSkimming());

$view['slots']->start('body'); ?>
<div class="panel-pane course2 step2 <?php print ($complete ? ' right' : ''); ?>" id="course2_test_taking-step2">
    <div class="pane-content">
        <h2>Now let's see how much you remember</h2>
        <h3>Leading up to the test, it is a super good idea to cram.</h3>
        <div class="questions">
            <label class="radio"><input name="quiz-ideaCram" type="radio" value="1" <?php print ($quiz->getIdeaCram() ? 'checked="checked"' : ''); ?>><i></i><span>True</span></label>
            <label class="radio"><input name="quiz-ideaCram" type="radio" value="0" <?php print ($quiz->getIdeaCram() === false ? 'checked="checked"' : ''); ?>><i></i><span>False</span></label>
        </div>
        <?php if ($complete) { ?>
            <div class="results">
                <p>SAY NO TO CRAMMING!!!</p>
            </div>
        <?php } ?>
        <h3>What is the name of the breathing exercise demonstrated in this video?</h3>
        <div class="questions">
            <label class="input"><input name="quiz-breathing" type="text" value="<?php print $view->escape($quiz->getBreathing()); ?>"></label>
        </div>
        <?php if ($complete) { ?>
            <div class="results">
                <p>It is called four-part breathing.  It is also sometimes called combat or tactical breathing.</p>
            </div>
        <?php } ?>
        <h3>What should you be looking for when you skim the test?</h3>
        <div class="questions">
            <label class="input"><input name="quiz-skimming" type="text" value="<?php print $view->escape($quiz->getSkimming()); ?>"></label>
        </div>
        <?php if ($complete) { ?>
            <div class="results">
                <p>Skimming the test will help you pace yourself.  In particular, look for the number of questions, the type of questions, and the value of questions.</p>
            </div>
        <?php } ?>
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>"/>
        <div class="highlighted-link <?php print ($complete ? ' valid' : ' invalid'); ?>">
            <div class="invalid-only">You must complete all fields before moving on.</div>
            <a href="#submit-quiz" class="more">Submit</a>
            <a href="<?php print $view['router']->generate('course2_test_taking', ['_step' => 3]); ?>" class="more">Next</a>
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

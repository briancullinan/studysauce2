<?php
use Course3\Bundle\Entity\ActiveReading;

$view->extend('Course3Bundle:Shared:layout.html.php');

/** @var ActiveReading $quiz */
$complete = !empty($quiz->getWhatReading()) && $quiz->getHighlighting() !== null && $quiz->getSkimming() !== null &&
    $quiz->getSelfExplanation() !== null;

$view['slots']->start('body'); ?>
<div class="panel-pane course3 step2 <?php print ($complete ? ' right' : ''); ?>" id="course3_active_reading-step2">
    <div class="pane-content">
        <h2>Now let's see how much you remember</h2>
        <h3>What is active reading?</h3>
        <div class="questions">
            <label class="input"><textarea name="quiz-what-reading"><?php print $view->escape($quiz->getWhatReading()); ?></textarea>
        </div>
        <?php if ($complete) { ?>
            <div class="results">
                <p>Active reading is simply trying to understand what you are reading and recognizing which parts are most important to your needs.  The key to active reading is being curious about what you are covering.</p>
            </div>
        <?php } ?>
        <h3>Highlighting and underlining is an effective tool for active reading.</h3>
        <div class="questions">
            <label class="radio"><input name="quiz-highlighting" type="radio" value="1" <?php print ($quiz->getHighlighting() ? 'checked="checked"' : ''); ?>><i></i><span>True</span></label>
            <label class="radio"><input name="quiz-highlighting" type="radio" value="0" <?php print ($quiz->getHighlighting() === false ? 'checked="checked"' : ''); ?>><i></i><span>False</span></label>
        </div>
        <?php if ($complete) { ?>
            <div class="results">
                <p>False.  People have been lying to you for years...  Highlighting and underlining is a complete waste of time and won't help you remember anything.  In order to learn the material, you have to go one step further.  Convert whatever you are highlighting and underlining into an exercise that will help you commit the material to memory.</p>
            </div>
        <?php } ?>
        <h3>Skimming through the reading is an effective tool for active reading.</h3>
        <div class="questions">
            <label class="radio"><input name="quiz-skimming" type="radio" value="1" <?php print ($quiz->getSkimming() ? 'checked="checked"' : ''); ?>><i></i><span>True</span></label>
            <label class="radio"><input name="quiz-skimming" type="radio" value="0" <?php print ($quiz->getSkimming() === false ? 'checked="checked"' : ''); ?>><i></i><span>False</span></label>
        </div>
        <?php if ($complete) { ?>
            <div class="results">
                <p>True.  Use this technique to get curious about the topic before you start reading.  Pay particular attention to learning objectives, chapter summaries, charts, tables, and section headings.  The author is trying to tell you what is important and what isn't.  Take the hint.</p>
            </div>
        <?php } ?>
        <h3>Self-explanation is an effective tool for active reading</h3>
        <div class="questions">
            <label class="radio"><input name="quiz-self-explanation" type="radio" value="1" <?php print ($quiz->getSelfExplanation() ? 'checked="checked"' : ''); ?>><i></i><span>True</span></label>
            <label class="radio"><input name="quiz-self-explanation" type="radio" value="0" <?php print ($quiz->getSelfExplanation() === false ? 'checked="checked"' : ''); ?>><i></i><span>False</span></label>
        </div>
        <?php if ($complete) { ?>
            <div class="results">
                <p>True.  Pause periodically when reading and try to explain what is going on in the text.  This will help you stop spacing out in the middle of your reading sessions and will also help you maintain your curiosity.</p>
            </div>
        <?php } ?>
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>"/>
        <div class="highlighted-link <?php print ($complete ? ' valid' : ' invalid'); ?>">
            <a href="#submit-quiz" class="more">Submit</a>
            <a href="<?php print $view['router']->generate('course3_active_reading', ['_step' => 3]); ?>" class="more">Next</a>
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

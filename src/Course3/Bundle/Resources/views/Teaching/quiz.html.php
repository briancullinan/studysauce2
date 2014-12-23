<?php
use Course3\Bundle\Entity\Teaching;

$view->extend('Course3Bundle:Shared:layout.html.php');

/** @var Teaching $quiz */
$complete = !empty($quiz->getNewLanguage()) && $quiz->getMemorizing() !== null && !empty($quiz->getVideotaping());

$view['slots']->start('body'); ?>
<div class="panel-pane course3 step2 <?php print ($complete ? ' right' : ''); ?>" id="course3_teaching-step2">
    <div class="pane-content">
        <h2>Now let's see how much you remember</h2>
        <h3>Why is using the teaching to learn strategy similar to learning a new language?</h3>
        <div class="questions">
            <label class="input"><input name="quiz-new-language" type="text" value="<?php print ($quiz->getNewLanguage() ? 'checked="checked"' : ''); ?>"></label>
        </div>
        <?php if ($complete) { ?>
            <div class="results">
                <p>They are similar because you lose the ability to guess the answer based on context and because you are forced to understand the information at a deeper level when you have to explain it.</p>
            </div>
        <?php } ?>
        <h3>True or false, teaching to learn is an effective strategy for memorizing lots of information.</h3>
        <div class="questions">
            <label class="radio"><input name="quiz-memorizing" type="radio" value="1" <?php print ($quiz->getMemorizing() ? 'checked="checked"' : ''); ?>><i></i><span>True</span></label>
            <label class="radio"><input name="quiz-memorizing" type="radio" value="0" <?php print ($quiz->getMemorizing() === false ? 'checked="checked"' : ''); ?>><i></i><span>False</span></label>
        </div>
        <?php if ($complete) { ?>
            <div class="results">
                <p>False.  Teaching to learn should be used when you need to understand a topic more deeply.  For memorizing, there are better strategies to use.</p>
            </div>
        <?php } ?>
        <h3>Why is videotaping yourself explaining a concept particularly helpful?</h3>
        <div class="questions">
            <label class="input"><input name="quiz-videotaping" type="text" value="<?php print $view->escape($quiz->getVideotaping()); ?>"></label>
        </div>
        <?php if ($complete) { ?>
            <div class="results">
                <p>First, when you have a video recorded, you can tell immediately if you don't understand the material.  Second, everyone wants to look good on camera.  This will actually help you think through the response more deeply and will help force yourself to learn the concept.</p>
            </div>
        <?php } ?>
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>"/>
        <div class="highlighted-link <?php print ($complete ? ' valid' : ' invalid'); ?>">
            <a href="#submit-quiz" class="more">Submit</a>
            <a href="<?php print $view['router']->generate('course3_teaching', ['_step' => 3]); ?>" class="more">Next</a>
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

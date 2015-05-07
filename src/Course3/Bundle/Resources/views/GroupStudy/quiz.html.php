<?php
use Course3\Bundle\Entity\GroupStudy;

$view->extend('Course3Bundle:Shared:layout.html.php');

/** @var GroupStudy $quiz */
$complete = !empty($quiz->getBadTimes()) && !empty($quiz->getBuilding()) && !empty($quiz->getGroupRole()) &&
    $quiz->getGroupBreaks() !== null;

$view['slots']->start('body'); ?>
<div class="panel-pane course3 step2 <?php print ($complete ? ' right' : ''); ?>" id="course3_group_study-step2">
    <div class="pane-content">
        <h2>Now let's see how much you remember</h2>
        <h3>Which of the following are usually bad times to study as a group?</h3>
        <div class="questions">
            <label class="checkbox"><input name="quiz-badTimes" type="checkbox" value="writing" <?php print (in_array('writing', $quiz->getBadTimes()) ? 'checked="checked"' : ''); ?>><i></i><span>Writing a paper</span></label>
            <label class="checkbox"><input name="quiz-badTimes" type="checkbox" value="difficult" <?php print (in_array('difficult', $quiz->getBadTimes()) ? 'checked="checked"' : ''); ?>><i></i><span>Trying to clarify difficult concepts</span></label>
            <label class="checkbox"><input name="quiz-badTimes" type="checkbox" value="material" <?php print (in_array('material', $quiz->getBadTimes()) ? 'checked="checked"' : ''); ?>><i></i><span>Looking at material for the first time</span></label>
            <label class="checkbox"><input name="quiz-badTimes" type="checkbox" value="memorizing" <?php print (in_array('memorizing', $quiz->getBadTimes()) ? 'checked="checked"' : ''); ?>><i></i><span>Memorizing information</span></label>
        </div>
        <?php if ($complete) { ?>
            <div class="results">
                <p>Study groups can be counterproductive when you need to have some peace and quiet (writing papers, memorizing information).  They can also be a waste of time if you are not properly prepared (if you are looking at something for the first time).</p>
            </div>
        <?php } ?>
        <h3>How many people should you shoot for when building your study group?</h3>
        <div class="questions">
            <label class="radio"><input name="quiz-building" type="radio" value="2" <?php print ($quiz->getBuilding() == 2 ? 'checked="checked"' : ''); ?>><i></i><span>2-3</span></label>
            <label class="radio"><input name="quiz-building" type="radio" value="3" <?php print ($quiz->getBuilding() == 3 ? 'checked="checked"' : ''); ?>><i></i><span>3-5</span></label>
            <label class="radio"><input name="quiz-building" type="radio" value="5" <?php print ($quiz->getBuilding() == 5 ? 'checked="checked"' : ''); ?>><i></i><span>5-7</span></label>
            <label class="radio"><input name="quiz-building" type="radio" value="7" <?php print ($quiz->getBuilding() == 7 ? 'checked="checked"' : ''); ?>><i></i><span>7-10</span></label>
        </div>
        <?php if ($complete) { ?>
            <div class="results">
                <p>Try to shoot for 3-5 students in order to benefit from a diversity of opinions, but not get bogged down by having too many people competing for air time.</p>
            </div>
        <?php } ?>
        <h3>What role should be rotated every week when the study group meets?</h3>
        <div class="questions">
            <label class="input"><input name="quiz-groupRole" type="text" value="<?php print $view->escape($quiz->getGroupRole()); ?>"></label>
        </div>
        <?php if ($complete) { ?>
            <div class="results">
                <p>The leader should rotate in order to keep everyone engaged in the group.</p>
            </div>
        <?php } ?>
        <h3>Study groups should take breaks too.</h3>
        <div class="questions">
            <label class="radio"><input name="quiz-groupBreaks" type="radio" value="1" <?php print ($quiz->getGroupBreaks() ? 'checked="checked"' : ''); ?>><i></i><span>True</span></label>
            <label class="radio"><input name="quiz-groupBreaks" type="radio" value="0" <?php print ($quiz->getGroupBreaks() === false ? 'checked="checked"' : ''); ?>><i></i><span>False</span></label>
        </div>
        <?php if ($complete) { ?>
            <div class="results">
                <p>True.  Try not to meet for too long or everyone will get exhausted and you will be less productive.  Taking breaks can help keep everyone fresh for longer.</p>
            </div>
        <?php } ?>
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>"/>
        <div class="highlighted-link <?php print ($complete ? ' valid' : ' invalid'); ?>">
            <div class="invalid-only">You must complete all fields before moving on.</div>
            <a href="#submit-quiz" class="more">Submit</a>
            <a href="<?php print $view['router']->generate('course3_group_study', ['_step' => 3]); ?>" class="more">Next</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php $view['slots']->stop(); ?>

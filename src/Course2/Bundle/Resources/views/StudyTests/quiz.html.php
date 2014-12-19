<?php
use Course2\Bundle\Entity\StudyTests;

$view->extend('Course2Bundle:Shared:layout.html.php');

/** @var StudyTests $quiz */
$complete = !empty($quiz->getTypesTests()) && !empty($quiz->getMostImportant()) && !empty($quiz->getOpenTips1()) &&
    !empty($quiz->getOpenTips2());

 $view['slots']->start('body'); ?>
<div class="panel-pane course2 step2 <?php print ($complete ? ' right' : ''); ?>" id="course2_study_tests-step2">
    <div class="pane-content">
        <h2>Now let's see how much you remember</h2>
        <h3>Which of the following types of tests are objective?</h3>
        <div class="questions">
            <label class="checkbox"><input name="quiz-types-tests" type="checkbox" value="essay" <?php print (in_array('essay', $quiz->getTypesTests()) ? 'checked="checked"' : ''); ?>><i></i><span>Essay</span></label>
            <label class="checkbox"><input name="quiz-types-tests" type="checkbox" value="short" <?php print (in_array('short', $quiz->getTypesTests()) ? 'checked="checked"' : ''); ?>><i></i><span>Short Answer</span></label>
            <label class="checkbox"><input name="quiz-types-tests" type="checkbox" value="math" <?php print (in_array('math', $quiz->getTypesTests()) ? 'checked="checked"' : ''); ?>><i></i><span>Math &amp; Science</span></label>
            <label class="checkbox"><input name="quiz-types-tests" type="checkbox" value="multiple" <?php print (in_array('multiple', $quiz->getTypesTests()) ? 'checked="checked"' : ''); ?>><i></i><span>Multiple Choice</span></label>
            <label class="checkbox"><input name="quiz-types-tests" type="checkbox" value="true" <?php print (in_array('true', $quiz->getTypesTests()) ? 'checked="checked"' : ''); ?>><i></i><span>True False</span></label>
        </div>
        <?php if($complete) { ?>
        <div class="results">
            <p>Math & Science, multiple choice, and true/false tests are all objective - meaning they have a definite right answer.</p>
        </div>
        <?php } ?>
        <h3>What is the most important thing in studying for tests?</h3>
        <div class="questions">
            <label class="input"><span>A:</span><input name="quiz-most-important" type="text" value="<?php print $view->escape($quiz->getMostImportant()); ?>"></label>
        </div>
        <?php if($complete) { ?>
        <div class="results">
            <p>Space out your studying!</p>
        </div>
        <?php } ?>
        <h3>What are two tips for open notes tests?</h3>
        <div class="questions">
            <label class="input"><span>1:</span><input name="quiz-open-tips-1" type="text" value="<?php print $view->escape($quiz->getOpenTips1()); ?>"></label>
            <label class="input"><span>2:</span><input name="quiz-open-tips-2" type="text" value="<?php print $view->escape($quiz->getOpenTips2()); ?>"></label>
        </div>
        <?php if($complete) { ?>
        <div class="results">
            <p>First, you still have to study.  In fact, you may need to study more.  Second, spending a little extra time organizing your notes is well worth the effort.  You need to be able to get to the most important information immediately during the test.</p>
        </div>
        <?php } ?>
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>"/>
        <div class="highlighted-link <?php print ($complete ? ' valid' : ' invalid'); ?>">
            <a href="#submit-quiz" class="more">Submit</a>
            <a href="<?php print $view['router']->generate('course2_study_tests', ['_step' => 3]); ?>" class="more">Next</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php $view['slots']->stop(); ?>

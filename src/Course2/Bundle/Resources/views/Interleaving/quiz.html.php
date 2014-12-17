<?php
use Course2\Bundle\Entity\Interleaving;

$view->extend('Course2Bundle:Shared:layout.html.php');

/** @var Interleaving $quiz */
$complete = !empty($quiz->getMultipleSessions()) && !empty($quiz->getOtherName()) && !empty($quiz->getTypesCourses());

 $view['slots']->start('body'); ?>
<div class="panel-pane course2 step2 <?php print ($complete ? ' right' : ''); ?>" id="course2_interleaving-step2">
    <div class="pane-content">
        <h2>To help us better tailor Study Sauce to you, please answer the below questions.</h2>
        <h3>What is it called when you study the same class material for multiple study session?</h3>
        <div class="questions">
            <label class="input"><span>A:</span><input name="quiz-multiple-sessions" type="text" value="<?php print $quiz->getMultipleSessions(); ?>"></label>
        </div>
        <?php if($complete) { ?>
        <div class="results">
            <p>Blocked practice.</p>
        </div>
        <?php } ?>
        <h3>What is another name for interleaving?</h3>
        <div class="questions">
            <label class="input"><span>A:</span><input name="quiz-other-name" type="text" value="<?php print $quiz->getOtherName(); ?>"></label>
        </div>
        <?php if($complete) { ?>
        <div class="results">
            <p>Varied practice.</p>
        </div>
        <?php } ?>
        <h3>When interleaving, alternating similar types of courses is most effective because your brain is already in the right mode.</h3>
        <div class="questions">
            <label class="radio"><input name="quiz-types-courses" type="radio" value="1" <?php print ($quiz->getTypesCourses() === true ? 'checked="checked"' : ''); ?>><i></i><span>True</span></label>
            <label class="radio"><input name="quiz-types-courses" type="radio" value="0" <?php print ($quiz->getTypesCourses() === false ? 'checked="checked"' : ''); ?>><i></i><span>False</span></label>
        </div>
        <?php if($complete) { ?>
        <div class="results">
            <p>False, try to alternate very different types of subjects if possible. Activating different parts of the brain helps you to remember more information.</p>
        </div>
        <?php } ?>
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>"/>
        <div class="highlighted-link <?php print ($complete ? ' valid' : ' invalid'); ?>">
            <a href="#submit-quiz" class="more">Submit</a>
            <a href="<?php print $view['router']->generate('course2_interleaving', ['_step' => 3]); ?>" class="more">Next</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php $view['slots']->stop(); ?>

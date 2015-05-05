<?php
use Course1\Bundle\Entity\Quiz5;

$view->extend('Course1Bundle:Shared:layout.html.php');

/** @var Quiz5 $quiz */
$complete = $quiz->getBed() !== null && $quiz->getBreaks() !== null && $quiz->getMozart() !== null && $quiz->getNature() !== null;

$view['slots']->start('body'); ?>
<div class="panel-pane course1 step2 <?php print ($complete ? ' right' : ''); ?>" id="course1_environment-step2">
    <div class="pane-content">
        <h2>Now let's see how much you remember</h2>
        <h3>Your bed is a great place to study since getting comfortable is critical to memory retention.</h3>
        <div class="questions">
            <label class="radio"><input name="quiz-bed" type="radio" value="1" <?php print ($quiz->getBed() ? 'checked="checked"' : ''); ?>><i></i><span>True</span></label>
            <label class="radio"><input name="quiz-bed" type="radio" value="0" <?php print ($quiz->getBed() === false ? 'checked="checked"' : ''); ?>><i></i><span>False</span></label>
        </div>
        <?php if($complete) { ?>
        <div class="results">
            <p>False - Your brain associates your bed with sleeping, so studying on your bed can lead to increased drowsiness.</p>
        </div>
        <?php } ?>
        <h3>Listening to Mozart is proven to help you study better.</h3>
        <div class="questions">
            <label class="radio"><input name="quiz-mozart" type="radio" value="1" <?php print ($quiz->getMozart() ? 'checked="checked"' : ''); ?>><i></i><span>True</span></label>
            <label class="radio"><input name="quiz-mozart" type="radio" value="0" <?php print ($quiz->getMozart() === false ? 'checked="checked"' : ''); ?>><i></i><span>False</span></label>
        </div>
        <?php if($complete) { ?>
        <div class="results">
            <p>False - The research does not conclusively prove this.  However, Mozart and other soothing instrumental music is better than listening to music with lyrics.</p>
        </div>
        <?php } ?>
        <h3>A nature walk can be an effective way to take a break between study sessions.</h3>
        <div class="questions">
            <label class="radio"><input name="quiz-nature" type="radio" value="1" <?php print ($quiz->getNature() ? 'checked="checked"' : ''); ?>><i></i><span>True</span></label>
            <label class="radio"><input name="quiz-nature" type="radio" value="0" <?php print ($quiz->getNature() === false ? 'checked="checked"' : ''); ?>><i></i><span>False</span></label>
        </div>
        <?php if($complete) { ?>
        <div class="results">
            <p>True - Research shows that taking a walk in natural surroundings can actually improve your ability to remember what you are studying.</p>
        </div>
        <?php } ?>
        <h3>Your study sessions should last a minimum of 1 hour and ideally you should stick with a topic for several hours to get the greatest benefit of prolonged focus.</h3>
        <div class="questions">
            <label class="radio"><input name="quiz-breaks" type="radio" value="1" <?php print ($quiz->getBreaks() ? 'checked="checked"' : ''); ?>><i></i><span>True</span></label>
            <label class="radio"><input name="quiz-breaks" type="radio" value="0" <?php print ($quiz->getBreaks() === false ? 'checked="checked"' : ''); ?>><i></i><span>False</span></label>
        </div>
        <?php if($complete) { ?>
            <div class="results">
                <p>False - Taking breaks is a critical component of studying.  Try to study for 50-60 minutes before taking a 10 minute break.  Alternatively, try to study for 25-30 minutes with a 5 minute break if you find the shorter sessions are more effective for you.</p>
            </div>
        <?php } ?>
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>"/>
        <div class="highlighted-link <?php print ($complete ? ' valid' : ' invalid'); ?>">
            <div class="invalid-only">You must complete all fields before moving on.</div>
            <a href="#submit-quiz" class="more">Submit</a>
            <a href="<?php print $view['router']->generate('course1_environment', ['_step' => 3]); ?>" class="more">Next</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php $view['slots']->stop(); ?>

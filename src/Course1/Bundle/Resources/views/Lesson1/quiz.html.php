<?php
use Course1\Bundle\Entity\Quiz1;

 $view->extend('Course1Bundle:Shared:layout.html.php');

/** @var Quiz1 $quiz */
$completed = !empty($quiz->getEducation()) &&
    !empty($quiz->getMindset()) && !empty($quiz->getTimeManagement()) && !empty($quiz->getDevices()) &&
    !empty($quiz->getStudyMuch());

 $view['slots']->start('body'); ?>
<div class="panel-pane course1 step2 <?php print ($completed ? ' right' : ''); ?>" id="lesson1-step2">
    <div class="pane-content">
        <h2>To help us better tailor Study Sauce to you, please answer the below questions.</h2>
        <h3>What grade are you in?</h3>
        <div class="questions">
            <label class="radio"><input name="quiz-education" type="radio" value="highschool" <?php print ($quiz->getEducation() == 'highschool' ? 'checked="checked"' : ''); ?>><i></i><span>High school student</span></label>
            <label class="radio"><input name="quiz-education" type="radio" value="college-freshman" <?php print ($quiz->getEducation() == 'college-freshman' ? 'checked="checked"' : ''); ?>><i></i><span>College Freshman</span></label>
            <label class="radio"><input name="quiz-education" type="radio" value="college-sophomore" <?php print ($quiz->getEducation() == 'college-sophomore' ? 'checked="checked"' : ''); ?>><i></i><span>College Sophomore</span></label>
            <label class="radio"><input name="quiz-education" type="radio" value="college-junior" <?php print ($quiz->getEducation() == 'college-junior' ? 'checked="checked"' : ''); ?>><i></i><span>College Junior</span></label>
            <label class="radio"><input name="quiz-education" type="radio" value="college-senior" <?php print ($quiz->getEducation() == 'college-senior' ? 'checked="checked"' : ''); ?>><i></i><span>College Senior</span></label>
            <label class="radio"><input name="quiz-education" type="radio" value="graduate" <?php print ($quiz->getEducation() == 'graduate' ? 'checked="checked"' : ''); ?>><i></i><span>Graduate student</span></label>
        </div>
        <div class="results">
            <p>We are excited to help you learn how to study more effectively.</p>
        </div>
        <h3>Which do you agree with more?</h3>
        <div class="questions">
            <label class="radio"><input name="quiz-mindset" type="radio" value="born" <?php print ($quiz->getMindset() == 'born' ? 'checked="checked"' : ''); ?>><i></i><span>Some people are born good at academics.</span></label>
            <label class="radio"><input name="quiz-mindset" type="radio" value="practice" <?php print ($quiz->getMindset() == 'practice' ? 'checked="checked"' : ''); ?>><i></i><span>People become good at academics through experience and building skills.</span></label>
        </div>
        <div class="results">
            <p>We will talk about this concept in great detail later in the course.</p>
        </div>
        <h3>How do you manage your time studying for exams?</h3>
        <div class="questions">
            <label class="radio"><input name="quiz-time-management" type="radio" value="advance" <?php print ($quiz->getTimeManagement() == 'advance' ? 'checked="checked"' : ''); ?>><i></i><span>I have to space out my studying far in advance of my exam. Otherwise, I get too stressed out.</span></label>
            <label class="radio"><input name="quiz-time-management" type="radio" value="cram" <?php print ($quiz->getTimeManagement() == 'cram' ? 'checked="checked"' : ''); ?>><i></i><span>I try to space it out, but usually end up cramming a day or two before my exam.</span></label>
            <label class="radio"><input name="quiz-time-management" type="radio" value="pressure" <?php print ($quiz->getTimeManagement() == 'pressure' ? 'checked="checked"' : ''); ?>><i></i><span>I do my best work under pressure and plan to cram before each exam.</span></label>
        </div>
        <div class="results">
            <p>Our study tools will help you break the procrastination habit.</p>
        </div>
        <h3>How do you manage your electronic devices when you study?</h3>
        <div class="questions">
            <label class="radio"><input name="quiz-devices" type="radio" value="on" <?php print ($quiz->getDevices() == 'on' ? 'checked="checked"' : ''); ?>><i></i><span>I keep them nearby. I will respond to texts, etc. if I get them, and then I right back to work.</span></label>
            <label class="radio"><input name="quiz-devices" type="radio" value="off" <?php print ($quiz->getDevices() == 'off' ? 'checked="checked"' : ''); ?>><i></i><span>I turn them off or put them somewhere so they won't distract me.</span></label>
        </div>
        <div class="results">
            <p>Get ready to learn how your electronic devices are killing your ability to study effectively.</p>
        </div>
        <h3>How much do you study per day?</h3>
        <div class="questions">
            <label class="radio"><input name="quiz-study-much" type="radio" value="one" <?php print ($quiz->getStudyMuch() == 'one' ? 'checked="checked"' : ''); ?>><i></i><span>0-1 hour</span></label>
            <label class="radio"><input name="quiz-study-much" type="radio" value="two" <?php print ($quiz->getStudyMuch() == 'two' ? 'checked="checked"' : ''); ?>><i></i><span>1-2 hours</span></label>
            <label class="radio"><input name="quiz-study-much" type="radio" value="four" <?php print ($quiz->getStudyMuch() == 'four' ? 'checked="checked"' : ''); ?>><i></i><span>2-4 hours</span></label>
            <label class="radio"><input name="quiz-study-much" type="radio" value="more" <?php print ($quiz->getStudyMuch() == 'more' ? 'checked="checked"' : ''); ?>><i></i><span>4+ hours</span></label>
        </div>
        <div class="results">
            <p>We will help you develop a plan to make sure that you are spending the right amount of time studying.</p>
        </div>
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>"/>
        <div class="highlighted-link invalid">
            <a href="#submit-quiz" class="more">Submit</a>
            <a href="<?php print $view['router']->generate('lesson1', ['_step' => 3]); ?>" class="more">Next</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php $view['slots']->stop(); ?>

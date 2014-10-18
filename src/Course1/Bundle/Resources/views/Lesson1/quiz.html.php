<?php use Symfony\Component\HttpKernel\Controller\ControllerReference;

 $view->extend('Course1Bundle:Shared:layout.html.php');

 $view['slots']->start('body'); ?>
<div class="panel-pane course1 step2" id="lesson1-step2">

    <div class="pane-content">

        <h2>To help us better tailor Study Sauce to you, please answer the below questions.</h2>

        <h3>What grade are you in?</h3>
        <div class="questions">
            <label class="radio"><input name="quiz-education" type="radio" value="highschool"><i></i><span>High school student</span></label>
            <label class="radio"><input name="quiz-education" type="radio" value="college-freshman"><i></i><span>College Freshman</span></label>
            <label class="radio"><input name="quiz-education" type="radio" value="college-sophomore"><i></i><span>College Sophomore</span></label>
            <label class="radio"><input name="quiz-education" type="radio" value="college-junior"><i></i><span>College Junior</span></label>
            <label class="radio"><input name="quiz-education" type="radio" value="college-senior"><i></i><span>College Senior</span></label>
            <label class="radio"><input name="quiz-education" type="radio" value="graduate"><i></i><span>Graduate student</span></label>
        </div>
        <div class="results">
            <p>We are excited to help you learn how to study more effectively.</p>
        </div>
        <h3>Which do you agree with more?</h3>
        <div class="questions">
            <label class="radio"><input name="quiz-mindset" type="radio" value="born"><i></i><span>Some people are born good at academics.</span></label>
            <label class="radio"><input name="quiz-mindset" type="radio" value="practice"><i></i><span>People become good at academics through experience and building skills.</span></label>
        </div>
        <div class="results">
            <p>We will talk about this concept in great detail later in the course.</p>
        </div>
        <h3>How do you manage your time studying for exams?</h3>
        <div class="questions">
            <label class="radio"><input name="quiz-time-management" type="radio" value="advance"><i></i><span>I have to space out my studying far in advance of my exam. Otherwise, I get too stressed out.</span></label>
            <label class="radio"><input name="quiz-time-management" type="radio" value="cram"><i></i><span>I try to space it out, but usually end up cramming a day or two before my exam.</span></label>
            <label class="radio"><input name="quiz-time-management" type="radio" value="pressure"><i></i><span>I do my best work under pressure and plan to cram before each exam.</span></label>
        </div>
        <div class="results">
            <p>Our study tools will help you break the procrastination habit.</p>
        </div>
        <h3>How do you manage your electronic devices when you study?</h3>
        <div class="questions">
            <label class="radio"><input name="quiz-devices" type="radio" value="on"><i></i><span>I keep them nearby. I will respond to texts, etc. if I get them, and then I right back to work.</span></label>
            <label class="radio"><input name="quiz-devices" type="radio" value="off"><i></i><span>I turn them off or put them somewhere so they won't distract me.</span></label>
        </div>
        <div class="results">
            <p>Get ready to learn how your electronic devices are killing your ability to study effectively.</p>
        </div>
        <h3>How much do you study per day?</h3>
        <div class="questions">
            <label class="radio"><input name="quiz-study-much" type="radio" value="one"><i></i><span>0-1 hour</span></label>
            <label class="radio"><input name="quiz-study-much" type="radio" value="two"><i></i><span>1-2 hours</span></label>
            <label class="radio"><input name="quiz-study-much" type="radio" value="four"><i></i><span>2-4 hours</span></label>
            <label class="radio"><input name="quiz-study-much" type="radio" value="more"><i></i><span>4+ hours</span></label>
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

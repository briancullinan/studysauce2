<?php
use Course2\Bundle\Entity\TestTaking;

$view->extend('Course2Bundle:Shared:layout.html.php');

/** @var TestTaking $quiz */
$complete = true;

$view['slots']->start('body'); ?>
<div class="panel-pane course2 step2 <?php print ($complete ? ' right' : ''); ?>" id="course2_test_taking-step2">
    <div class="pane-content">
        <h2>Now let's see how much you remember</h2>
        <h3>You have short and long term memory. What are these two types of memory also called?</h3>
        <div class="questions">
            <label class="input"><span>1</span><input name="quiz-memory-A" type="text" value="<?php /*print $view->escape($quiz->getActiveMemory());*/ ?>"></label>
            <label class="input"><span>2</span><input name="quiz-memory-R" type="text" value="<?php /*print $view->escape($quiz->getReferenceMemory());*/ ?>"></label>
        </div>
        <?php if ($complete) { ?>
            <div class="results">
                <p>Your brain has two types of memory, much like a computer has RAM and a hard drive as its short and
                    long term memory. Short term memory is also known as &ldquo;active memory&rdquo; while long term
                    memory is known as &ldquo;reference memory.&rdquo;</p>
            </div>
        <?php } ?>
        <h3>What is the goal of studying?</h3>
        <div class="questions">
            <label class="input"><span>1</span><input name="quiz-study-goal" type="text" value="<?php /*print $view->escape($quiz->getStudyGoal());*/ ?>"></label>
        </div>
        <?php if ($complete) { ?>
            <div class="results">
                <p>The goal of studying is not to do well on your next exam. The goal of studying is to retain the
                    information that you are studying. Acquiring this knowledge is the reason that you are in school. In
                    order to do this, you must commit things to your long term memory</p>
            </div>
        <?php } ?>
        <h3>What is the solution to stopping the procrastination to cramming cycle?</h3>
        <div class="questions">
            <label class="input"><span>1</span><input name="quiz-stop-procrastinating" type="text" value="<?php /*print $view->escape($quiz->getProcrastinating());*/ ?>"></label>
        </div>
        <?php if ($complete) { ?>
            <div class="results">
                <p>Fortunately, there is a simple and extremely effective solution to ending the procrastination to
                    cramming cycle. Space out your studying. This will allow your brain to retain more information and
                    will help you avoid cramming sessions after which you forget everything anyway.</p>
            </div>
        <?php } ?>
        <h3>What are two tools that you can use to help reduce procrastination?</h3>
        <div class="questions">
            <label class="input"><span>1</span><input name="quiz-reduce-procrastination-D" type="text" value="<?php /*print $view->escape($quiz->getDeadlines());*/ ?>"></label>
            <label class="input"><span>2</span><input name="quiz-reduce-procrastination-P" type="text" value="<?php /*print $view->escape($quiz->getPlan());*/ ?>"></label>
        </div>
        <?php if ($complete) { ?>
            <div class="results">
                <p>There are many techniques that will help you to reduce procrastination, but two of the most effective
                    tools are creating and analyzing your deadlines and building a good study plan.</p>
            </div>
        <?php } ?>
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>"/>
        <div class="highlighted-link <?php print ($complete ? ' valid' : ' invalid'); ?>">
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

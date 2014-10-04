
<?php $view->extend('Course1Bundle:Lesson1:layout.html.php') ?>

<?php $view['slots']->start('body'); ?>
<div class="panel-pane course1 step2" id="lesson1-step2">

    <div class="pane-content">

        <h2>Now let's see how much you remember</h2>
        Grade level
        Mindset question
        Time management question
        Electronic devices
        Study hours per day

        <div class="questions">
            <h3>Which do you agree with more?</h3>
            <label class="radio"><input name="quiz-1" type="radio" value="born"><i></i><span>Some people are born good at academics.</span></label>
            <label class="radio"><input name="quiz-1" type="radio" value="practice"><i></i><span>People become good at academics through experience and building skills.</span></label>
        </div>
        <div class="results">
            <h3>Correct!</h3>
            <p>Simply varying the location of where you study has been proven to dramatically improve information retention.</p>
        </div>
        <hr />
        <div class="questions">
            <h3>How do your manage your weekends?</h3>
            <label class="radio"><input name="quiz-2" type="radio" value="hit_hard"><i></i><span>Hit hard, keep weeks open</span></label>
            <label class="radio"><input name="quiz-2" type="radio" value="light_work"><i></i><span>Light work, focus during the week</span></label>
        </div>
        <div class="results">
            <h3>Wrong.</h3>
            <p>It turns out that highlighting and underlining are some of the least effective study methods.&nbsp; Don't spend too much time doing them.&nbsp; Instead, quickly identify the important material and then create flash cards.&nbsp; Flash cards are a very effective way to train your brain to remember important information.</p>
        </div>

        <div class="highlighted-link">
            <a href="#submit-quiz" class="more">Submit</a>
            <a href="<?php print $view['router']->generate('lesson1', ['_step' => 3]); ?>" class="more">Next</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php $view['slots']->stop(); ?>

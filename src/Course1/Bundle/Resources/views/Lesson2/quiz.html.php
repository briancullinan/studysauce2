
<?php $view->extend('Course1Bundle:Lesson1:layout.html.php');

 $view['slots']->start('body'); ?>
<div class="panel-pane course1 step2" id="lesson2-step2">

    <div class="pane-content">

        <h2>Now let's see how much you remember</h2>

        <h3>How much more likely are you to perform at a higher level if you set specific and challenging goals?<span class="answer">Correct!</span></h3>
        <div class="questions">
            <label class="radio"><input name="quiz-goal-performance" type="radio" value="20"><i></i><span>20%</span></label>
            <label class="radio"><input name="quiz-goal-performance" type="radio" value="40"><i></i><span>40%</span></label>
            <label class="radio"><input name="quiz-goal-performance" type="radio" value="60"><i></i><span>60%</span></label>
            <label class="radio"><input name="quiz-goal-performance" type="radio" value="90"><i></i><span>90%</span></label>
        </div>
        <div class="results">
            <p>You are 90% more likely to perform at a higher level if you set specific and challenging goals.</p>
        </div>
        <h3>What does the SMART acronym stand for?<span class="answer">Wrong.</span></h3>
        <div class="questions">
            <label class="input"><span>S</span><input name="quiz-smart-acronym-S" type="text" value=""></label>
            <label class="input"><span>M</span><input name="quiz-smart-acronym-M" type="text" value=""></label>
            <label class="input"><span>A</span><input name="quiz-smart-acronym-A" type="text" value=""></label>
            <label class="input"><span>R</span><input name="quiz-smart-acronym-R" type="text" value=""></label>
            <label class="input"><span>T</span><input name="quiz-smart-acronym-T" type="text" value=""></label>
        </div>
        <div class="results">
            <p>Answers are specific, measurable, achievable, relevant, time-bound</p>
        </div>
        <h3>What are the two types of motivation?<span class="answer">Correct!</span></h3>
        <div class="questions">
            <label class="input"><span>1</span><input name="quiz-motivation-I" type="text" value=""></label>
            <label class="input"><span>2</span><input name="quiz-motivation-E" type="text" value=""></label>
        </div>
        <div class="results">
            <p>The two types of motivation are intrinsic and extrinsic motivation.  Intrinsic motivation is motivation that comes from within.  Ex. studying because you want the satisfaction of learning something new.  Extrinsic motivation is a reward that comes externally.  Ex. studying in order to get a good grade.</p>
        </div>

        <div class="highlighted-link">
            <a href="#submit-quiz" class="more">Submit</a>
            <a href="<?php print $view['router']->generate('lesson2', ['_step' => 3]); ?>" class="more">Next</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php $view['slots']->stop(); ?>

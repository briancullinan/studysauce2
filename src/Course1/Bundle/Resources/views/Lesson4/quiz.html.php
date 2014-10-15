
<?php $view->extend('Course1Bundle:Lesson1:layout.html.php');

 $view['slots']->start('body'); ?>
<div class="panel-pane course1 step2" id="lesson4-step2">

    <div class="pane-content">

        <h2>Now let's see how much you remember</h2>

        <h3>True or False.  You are excellent at multitasking.<span class="answer">Correct!</span></h3>
        <div class="questions">
            <label class="radio"><input name="quiz-multitasker" type="radio" value="1"><i></i><span>True</span></label>
            <label class="radio"><input name="quiz-multitasker" type="radio" value="0"><i></i><span>False</span></label>
        </div>
        <div class="results">
            <p>You are not good at multitasking despite what you believe.  No one is good at multitasking because that is not how the brain works.</p>
        </div>
        <h3>Which of the following is <i style="text-decoration: underline">not</i> a downside of multitasking?<span class="answer">Wrong.</span></h3>
        <div class="questions">
            <label class="radio"><input name="quiz-downside" type="radio" value="tired"><i></i><span>Get tired more easily</span></label>
            <label class="radio"><input name="quiz-downside" type="radio" value="shorter"><i></i><span>Shorter memory of material</span></label>
            <label class="radio"><input name="quiz-downside" type="radio" value="remember"><i></i><span>Remember less</span></label>
            <label class="radio"><input name="quiz-downside" type="radio" value="longer"><i></i><span>Takes longer to study</span></label>
        </div>
        <div class="results">
            <p>The downside of multitasking includes: getting tired more easily, remembering less when you study, and taking longer to study.</p>
        </div>
        <h3>How much lower do students interrupted by technology score on tests (in research studies)?<span class="answer">Correct!</span></h3>
        <div class="questions">
            <label class="radio"><input name="quiz-lower-score" type="radio" value="10"><i></i><span>10%</span></label>
            <label class="radio"><input name="quiz-lower-score" type="radio" value="20"><i></i><span>20%</span></label>
            <label class="radio"><input name="quiz-lower-score" type="radio" value="30"><i></i><span>30%</span></label>
            <label class="radio"><input name="quiz-lower-score" type="radio" value="40"><i></i><span>40%</span></label>
        </div>
        <div class="results">
            <p>Research shows that students that have technological distractions score 20% lower on tests than their peers without distractions.  That is like dropping from an A to a C!</p>
        </div>
        <h3>How long can a text message distract you from your optimal study state?<span class="answer">Correct!</span></h3>
        <div class="questions">
            <label class="radio"><input name="quiz-long-distract" type="radio" value="3"><i></i><span>1-3 minutes</span></label>
            <label class="radio"><input name="quiz-long-distract" type="radio" value="15"><i></i><span>5-15 minutes</span></label>
            <label class="radio"><input name="quiz-long-distract" type="radio" value="40"><i></i><span>25-40 minutes</span></label>
            <label class="radio"><input name="quiz-long-distract" type="radio" value="60"><i></i><span>45-60 minutes</span></label>
        </div>
        <div class="results">
            <p>Turn off your phone when you study!!!  It may seem like you can get back into the swing of things in 1-3 minutes, but the research shows that it takes 25-40 minutes.  The phone is your greatest enemy while studying and whatever is on it can wait until you are ready to take a study break.</p>
        </div>

        <div class="highlighted-link">
            <a href="#submit-quiz" class="more">Submit</a>
            <a href="<?php print $view['router']->generate('lesson4', ['_step' => 3]); ?>" class="more">Next</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php $view['slots']->stop(); ?>

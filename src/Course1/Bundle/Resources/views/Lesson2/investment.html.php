
<?php $view->extend('Course1Bundle:Lesson1:layout.html.php');

 $view['slots']->start('body'); ?>
<div class="panel-pane course1" id="lesson2-step4">

    <div class="pane-content">

        <h2>Great job!</h2>
        <h3>Assignment:</h3>
        <p>Before you move on to the next study module, take a few minutes to set up your study goals.  Our Goals study tool will help you focus on the right types of goals and will allow you to set up rewards for achieving those goals.</p>
        <div class="highlighted-link">
            <a href="<?php print $view['router']->generate('schedule'); ?>" class="more">Set up my goals</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php $view['slots']->stop(); ?>

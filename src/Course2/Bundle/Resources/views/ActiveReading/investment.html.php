<?php

$view->extend('Course2Bundle:Shared:layout.html.php');

 $view['slots']->start('body'); ?>
<div class="panel-pane course2 step4" id="course2_active_reading-step4">
    <div class="pane-content">
        <h2>Take active reading for a test drive</h2>
        <div class="grid_6">
            <h3>Assignment:</h3>
            <p>Head to your study plan tab and complete an active reading study strategy.  Before you start a reading assignment, take a few minutes to think about what you are about to read.  Your goal is to think of questions that you want to answer as you read.  This takes some practice, so stick with it!</p>
        </div>
        <div class="grid_6">
            <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/complication_compressed.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                <img width="200" height="200" src="<?php echo $view->escape($url) ?>" alt="Complication"/>
            <?php endforeach; ?>
        </div>
        <div class="highlighted-link">
            <a href="<?php print $view['router']->generate('plan'); ?>" class="more">Study plan</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php $view['slots']->stop(); ?>

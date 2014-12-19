<?php

$view->extend('Course2Bundle:Shared:layout.html.php');

 $view['slots']->start('body'); ?>
<div class="panel-pane course2 step4" id="course2_study_plan-step4">
    <div class="pane-content">
        <h2>Build your study plan</h2>
        <div class="grid_6">
            <h3>Assignment:</h3>
            <p>If you haven't already, spin through your personalized study plan and tweak it until it works for you.  Don't be alarmed by the number of study sessions.  You won't always need to use all of them.  Just remember to space out your studying.</p>
        </div>
        <div class="grid_6">
            <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/situation_compressed.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                <img width="200" height="200" src="<?php echo $view->escape($url) ?>" alt="Situation"/>
            <?php endforeach; ?>
        </div>
        <div class="highlighted-link">
            <a href="<?php print $view['router']->generate('plan'); ?>" class="more">Study plan</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php $view['slots']->stop(); ?>

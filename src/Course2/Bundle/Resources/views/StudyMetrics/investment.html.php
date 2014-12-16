<?php

$view->extend('Course2Bundle:Shared:layout.html.php');

 $view['slots']->start('body'); ?>
<div class="panel-pane course2 step4" id="course2_study_metrics-step4">
    <div class="pane-content">
        <h2>Check in</h2>
        <div class="grid_6">
            <h3>Assignment:</h3>
            <p>Now, let's check in for a study session.  Try to study for 60 minutes and then take a 10 minute break afterwards.  By using the "check in" tab, we will stream some good music for you to listen to during your studies.</p>
        </div>
        <div class="grid_6">
            <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/situation_compressed.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                <img width="200" height="200" src="<?php echo $view->escape($url) ?>" alt="Situation"/>
            <?php endforeach; ?>
        </div>
        <div class="highlighted-link">
            <a href="<?php print $view['router']->generate('checkin'); ?>" class="more">Check in</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php $view['slots']->stop(); ?>

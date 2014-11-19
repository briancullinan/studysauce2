<?php

$view->extend('Course1Bundle:Shared:layout.html.php');

 $view['slots']->start('body'); ?>
<div class="panel-pane course1 step4" id="course1_procrastination-step4">

    <div class="pane-content">

        <h2>Nice work.  Let's take to first step on reducing your procrastination.</h2>
        <div class="grid_6">
            <h3>Assignment:</h3>
            <p>Before you move on to the next study module, take a few minutes to set up your deadlines.   Look for times when your deadlines bunch up so that you can avoid cramming by planning ahead.  Click the link below to go to our Deadlines reminder tool.</p>
        </div>
        <div class="grid_6">
            <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/complication_compressed.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                <img width="200" height="200" src="<?php echo $view->escape($url) ?>" alt="Complication"/>
            <?php endforeach; ?>
        </div>
        <div class="highlighted-link">
            <a href="<?php print $view['router']->generate('deadlines'); ?>" class="more">Set up deadlines</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php $view['slots']->stop(); ?>

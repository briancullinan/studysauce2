<?php

$view->extend('Course2Bundle:Shared:layout.html.php');

 $view['slots']->start('body'); ?>
<div class="panel-pane course2 step4" id="course2_test_taking-step4">
    <div class="pane-content">
        <h2>Take a deep breath.</h2>
        <div class="grid_6">
            <h3>Assignment:</h3>
            <p>Take a few seconds and try out the four part breathing exercise.  For extra credit, check out how it affects your pulse (it should drop quite a bit).</p>
        </div>
        <div class="grid_6">
            <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/complication_compressed.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                <img width="200" height="200" src="<?php echo $view->escape($url) ?>" alt="Complication"/>
            <?php endforeach; ?>
        </div>
        <div class="highlighted-link">
            <a href="<?php print $view['router']->generate('home'); ?>" class="more">Go home</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php $view['slots']->stop(); ?>

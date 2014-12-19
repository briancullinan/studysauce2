<?php

$view->extend('Course2Bundle:Shared:layout.html.php');

 $view['slots']->start('body'); ?>
<div class="panel-pane course2 step4" id="course2_strategies-step4">
    <div class="pane-content">
        <h2>Let's get started learning a few different strategies</h2>
        <div class="grid_6">
            <h3>Assignment:</h3>
            <p>Instead of taking a one-size fits all approach, you are about to learn how to study for different types of classes.  Follow the link and identify the types of classes that you are taking.  This will help you zero in on how to study for them once you complete the next few videos.</p>
        </div>
        <div class="grid_6">
            <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/complication_compressed.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                <img width="200" height="200" src="<?php echo $view->escape($url) ?>" alt="Complication"/>
            <?php endforeach; ?>
        </div>
        <div class="highlighted-link">
            <a href="<?php print $view['router']->generate('customization'); ?>" class="more">Customize classes</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php $view['slots']->stop(); ?>

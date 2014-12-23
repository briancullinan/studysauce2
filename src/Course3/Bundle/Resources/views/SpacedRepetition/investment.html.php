<?php

$view->extend('Course3Bundle:Shared:layout.html.php');

 $view['slots']->start('body'); ?>
<div class="panel-pane course3 step4" id="course3_spaced_repetition-step4">
    <div class="pane-content">
        <h2>Congratulations, you have finished the course!!!</h2>
        <div class="grid_6">
            <h3>Assignment:</h3>
            <p>We hope you enjoyed the course.  You now know how to study effectively.  Remember to use your study tools and feel free to revisit some of the videos from time to time.  Please let us know what you thought of the course.  We are particularly interested in your feedback and your suggestions for new videos and study tools that you would like to see us make.  Thanks!!!</p>
        </div>
        <div class="grid_6">
            <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/complication_compressed.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                <img width="200" height="200" src="<?php echo $view->escape($url) ?>" alt="Complication"/>
            <?php endforeach; ?>
        </div>
        <div class="highlighted-link">
            <a href="<?php print $view['router']->generate('home'); ?>" class="more">Way to go...me</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php $view['slots']->stop(); ?>

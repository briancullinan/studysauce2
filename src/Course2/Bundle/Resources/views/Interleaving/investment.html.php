<?php
use Course2\Bundle\Entity\Course2;


/** @var Course2 $course */

$view->extend('Course2Bundle:Shared:layout.html.php');

$view['slots']->start('body'); ?>
<div class="panel-pane course2 step4" id="course2_interleaving-step4">
    <div class="pane-content">
        <h2>Check in</h2>
        <div class="grid_6">
            <h3>Assignment:</h3>
            <p>Try an interleaved study session.  Study for one hour for one subject, then study for another hour for another subject.</p>
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

<?php

use Course2\Bundle\Entity\Course2;

/** @var Course2 $course */

$view->extend('Course2Bundle:Shared:layout.html.php');

 $view['slots']->start('body'); ?>
<div class="panel-pane course2 step4" id="course2_group_study-step4">
    <div class="pane-content">
        <h2>Take the next step in group studying</h2>
        <div class="grid_6">
            <h3>Assignment:</h3>
            <p>Now that you have watched the group study video, write down any goals that you want to try in a group setting.</p>
            <label class="input">
                <span>Are there classes that you want to form a group for?  Do you want to speak up more in your group?</span>
                <textarea placeholder="" cols="60" rows="2"><?php print $view->escape($course->getGroupGoals()); ?></textarea>
            </label>
        </div>
        <div class="grid_6">
            <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/complication_compressed.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                <img width="200" height="200" src="<?php echo $view->escape($url) ?>" alt="Complication"/>
            <?php endforeach; ?>
        </div>
        <div class="highlighted-link invalid">
            <a href="<?php print $view['router']->generate('course2_teaching', ['_step' => 0]); ?>" class="more">Next course</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php $view['slots']->stop(); ?>

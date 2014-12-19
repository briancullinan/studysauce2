<?php

use Course2\Bundle\Entity\Course2;

/** @var Course2 $course */

$view->extend('Course2Bundle:Shared:layout.html.php');

 $view['slots']->start('body'); ?>
<div class="panel-pane course2 step4" id="course2_study_tests-step4">
    <div class="pane-content">
        <h2>What kind of tests do you have?</h2>
        <div class="grid_6">
            <h3>Assignment:</h3>
            <p>Think about your classes and exams.  Are they objective or subjective?</p>
            <label class="input">
                <span>Below, write which of your classes are objective and which are subjective.</span>
                <textarea placeholder="" cols="60" rows="2"><?php print $view->escape($course->getTestTypes()); ?></textarea>
            </label>
        </div>
        <div class="grid_6">
            <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/resolution_compressed.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                <img width="200" height="200" src="<?php echo $view->escape($url) ?>" alt="Resolution"/>
            <?php endforeach; ?>
        </div>
        <div class="highlighted-link invalid">
            <a href="<?php print $view['router']->generate('home'); ?>" class="more">Go home</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php $view['slots']->stop(); ?>

<?php
use Course2\Bundle\Entity\Course2;


/** @var Course2 $course */

$view->extend('Course2Bundle:Shared:layout.html.php');

$view['slots']->start('body'); ?>
<div class="panel-pane course2 step4" id="course2_interleaving-step4">
    <div class="pane-content">
        <h2>Great job!</h2>
        <div class="grid_6">
            <h3>Finally, before we get started, we have one last question for you.</h3>
            <br />
            <label class="input">
                <span>Why do you want to become better at studying?</span>
                <textarea placeholder="" cols="60" rows="2"><?php print $view->escape($course->getWhyStudy()); ?></textarea>
            </label>
        </div>
        <div class="grid_6">
            <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/situation_compressed.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                <img width="200" height="200" src="<?php echo $view->escape($url) ?>" alt="Situation"/>
            <?php endforeach; ?>
        </div>
        <div class="highlighted-link invalid">
            <a href="<?php print $view['router']->generate('course2_setting_goals', ['_step' => 0]); ?>" class="more">Next step</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php $view['slots']->stop(); ?>

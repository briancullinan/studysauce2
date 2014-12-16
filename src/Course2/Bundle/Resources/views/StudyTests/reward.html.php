<?php
use Symfony\Bundle\FrameworkBundle\Templating\TimedPhpEngine;

/** @var TimedPhpEngine $view */
$view->extend('Course2Bundle:Shared:layout.html.php');

$view['slots']->start('body'); ?>
<div class="panel-pane course2 step3" id="course2_study_tests-step3">
    <div class="pane-content">
        <h2>Don't let others distract you...</h2>
        <?php foreach ($view['assetic']->image(['@Course2Bundle/Resources/public/images/reward4.gif'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
            <img width="100%" src="<?php echo $view->escape($url) ?>" alt="LOGO" />
        <?php endforeach; ?>
        <div class="award">
            <h3>Badge awarded:</h3>
            <span class="badge squirrel">&nbsp;</span>
            <strong>Master of focus</strong>
            <div class="description">You are now impervious to distractions and can....oh look....a squirrel...</div>
        </div>
        <div class="highlighted-link">
            <a href="<?php print $view['router']->generate('course2_study_tests', ['_step' => 4]); ?>" class="more">Next</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php $view['slots']->stop(); ?>


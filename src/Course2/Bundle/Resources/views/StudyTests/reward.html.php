<?php
use Symfony\Bundle\FrameworkBundle\Templating\TimedPhpEngine;

/** @var TimedPhpEngine $view */
$view->extend('Course2Bundle:Shared:layout.html.php');

$view['slots']->start('body'); ?>
<div class="panel-pane course2 step3" id="course2_study_tests-step3">
    <div class="pane-content">
        <h2>Relax, you've got this!</h2>
        <?php foreach ($view['assetic']->image(['@Course2Bundle/Resources/public/images/reward4.gif'], [], ['output' => 'bundles/course2/images/*']) as $url): ?>
            <img width="100%" src="<?php echo $view->escape($url) ?>" alt="LOGO" />
        <?php endforeach; ?>
        <div class="award">
            <h3>Badge awarded:</h3>
            <span class="medal turtle">&nbsp;</span>
            <strong>Slow and steady</strong>
            <div class="description">The key to studying for tests is to start early and to pace yourself.  By spacing out your studies, you will remember more and be far less stressed.</div>
        </div>
        <div class="highlighted-link">
            <a href="<?php print $view['router']->generate('course2_study_tests', ['_step' => 4]); ?>" class="more">Next</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php $view['slots']->stop(); ?>


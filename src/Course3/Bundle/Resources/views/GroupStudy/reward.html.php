<?php
use Symfony\Bundle\FrameworkBundle\Templating\TimedPhpEngine;

/** @var TimedPhpEngine $view */
$view->extend('Course3Bundle:Shared:layout.html.php');

$view['slots']->start('body'); ?>
<div class="panel-pane course3 step3" id="course3_group_study-step3">
    <div class="pane-content">
        <h2>Choose your group wisely.</h2>
        <?php foreach ($view['assetic']->image(['@Course3Bundle/Resources/public/images/reward7.gif'], [], ['output' => 'bundles/course3/images/*']) as $url): ?>
            <img width="100%" src="<?php echo $view->escape($url) ?>" alt="LOGO" />
        <?php endforeach; ?>
        <div class="award">
            <h3>Badge awarded:</h3>
            <span class="medal ants">&nbsp;</span>
            <strong>Many hands make light work</strong>
            <p class="description">You can now safely put people management on your resume...congrats.</p>
        </div>
        <div class="highlighted-link">
            <a href="<?php print $view['router']->generate('course3_group_study', ['_step' => 4]); ?>" class="more">Next</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php $view['slots']->stop(); ?>



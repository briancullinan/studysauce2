<?php
use Symfony\Bundle\FrameworkBundle\Templating\TimedPhpEngine;

/** @var TimedPhpEngine $view */
$view->extend('Course3Bundle:Shared:layout.html.php');

$view['slots']->start('body'); ?>
<div class="panel-pane course3 step3" id="course3_active_reading-step3">
    <div class="pane-content">
        <h2>Curiosity is the key</h2>
        <?php foreach ($view['assetic']->image(['@Course3Bundle/Resources/public/images/reward9.gif'], [], ['output' => 'bundles/course3/images/*']) as $url): ?>
            <img width="100%" src="<?php echo $view->escape($url) ?>" alt="LOGO" />
        <?php endforeach; ?>
        <div class="award">
            <h3>Badge awarded:</h3>
            <span class="medal setup-hours">&nbsp;</span>
            <strong>Book worm</strong>
            <p class="description">Your days of head-bobbing while getting through your reading are over!!!  Well, maybe.  Use active reading as a tool to defeat the sleep monster and actually remember what you are reading.</p>
        </div>
        <div class="highlighted-link">
            <a href="<?php print $view['router']->generate('course3_active_reading', ['_step' => 4]); ?>" class="more">Next</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php $view['slots']->stop(); ?>



<?php
use Symfony\Bundle\FrameworkBundle\Templating\TimedPhpEngine;

/** @var TimedPhpEngine $view */
$view->extend('Course1Bundle:Shared:layout.html.php');

$view['slots']->start('body'); ?>
<div class="panel-pane course1 step3" id="lesson2-step3">
    <div class="pane-content">
        <h2>Set goals like ninja!</h2>
        <div class="award">
            <h3>Badge awarded:</h3>
            <span class="badge setup-hours">&nbsp;</span>
            <strong>Setup Goals</strong>
            <div class="description">Setting goals help you change your study behavior.</div>
        </div>
        <?php foreach ($view['assetic']->image(['@Course1Bundle/Resources/public/images/reward2.gif'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
            <img width="100%" src="<?php echo $view->escape($url) ?>" alt="LOGO" />
        <?php endforeach; ?>
        <div class="highlighted-link">
            <a href="<?php print $view['router']->generate('lesson2', ['_step' => 4]); ?>" class="more">Next</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php $view['slots']->stop(); ?>

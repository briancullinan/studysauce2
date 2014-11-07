<?php
use Symfony\Bundle\FrameworkBundle\Templating\TimedPhpEngine;

/** @var TimedPhpEngine $view */
$view->extend('Course1Bundle:Shared:layout.html.php');

$view['slots']->start('body'); ?>
<div class="panel-pane course1 step3" id="lesson3-step3">
    <div class="pane-content">
        <h2>Baby goat is tired of your procrastination excuses.</h2>
        <div class="award">
            <h3>Badge awarded:</h3>
            <span class="badge beaver">&nbsp;</span>
            <strong>Eager Beaver</strong>
            <p class="description">We know you don't want to waste another minute <i style="text-decoration: line-through;">not</i> studying. We have many more tools for you to help stop procrastinating.</p>
        </div>
        <?php foreach ($view['assetic']->image(['@Course1Bundle/Resources/public/images/reward3.gif'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
            <img width="100%" src="<?php echo $view->escape($url) ?>" alt="LOGO" />
        <?php endforeach; ?>
        <div class="highlighted-link">
            <a href="<?php print $view['router']->generate('lesson3', ['_step' => 4]); ?>" class="more">Next</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php $view['slots']->stop(); ?>



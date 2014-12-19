<?php
use Symfony\Bundle\FrameworkBundle\Templating\TimedPhpEngine;

/** @var TimedPhpEngine $view */
$view->extend('Course1Bundle:Shared:layout.html.php');

$view['slots']->start('body'); ?>
<div class="panel-pane course1 step3" id="course1_environment-step3">
    <div class="pane-content">
        <h2>Leave your distractions behind you!</h2>
        <?php foreach ($view['assetic']->image(['@Course1Bundle/Resources/public/images/reward5.gif'], [], ['output' => 'bundles/course1/images/*']) as $url): ?>
            <img width="100%" src="<?php echo $view->escape($url) ?>" alt="LOGO" />
        <?php endforeach; ?>
        <div class="award">
            <h3>Badge awarded:</h3>
            <span class="medal beginner-brain">&nbsp;</span>
            <strong>Big brain</strong>
            <p class="description">Clearly your brain is swelling with knowledge now.  You now have the knowledge to set up an effective study environment.</p>
        </div>
        <div class="highlighted-link">
            <a href="<?php print $view['router']->generate('course1_environment', ['_step' => 4]); ?>" class="more">Next</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php $view['slots']->stop(); ?>

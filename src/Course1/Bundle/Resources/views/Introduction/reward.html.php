<?php
use Symfony\Bundle\FrameworkBundle\Templating\TimedPhpEngine;

/** @var TimedPhpEngine $view */
$view->extend('Course1Bundle:Shared:layout.html.php');

$view['slots']->start('body'); ?>
<div class="panel-pane course1 step3" id="course1_introduction-step3">
    <div class="pane-content">
        <h2>Clearly you have some swag. Let's get started!</h2>
        <?php foreach ($view['assetic']->image(['@Course1Bundle/Resources/public/images/reward1.gif'],[],['output' => 'bundles/studysauce/images/*']) as $url): ?>
            <img width="100%" src="<?php echo $view->escape($url) ?>" alt="LOGO"/>
        <?php endforeach; ?>
        <div class="award">
            <h3>Badge awarded:</h3>
            <span class="badge">&nbsp;</span>
            <strong>Pulse Detected</strong>

            <div class="description">Yep, you are alive. Now let's get started.</div>
        </div>
        <div class="highlighted-link">
            <a href="<?php print $view['router']->generate('course1_introduction', ['_step' => 4]); ?>" class="more">Next</a>
        </div>
        <ul class="tab-tracker">
            <li>&bullet;</li>
            <li>&bullet;</li>
            <li>&bullet;</li>
            <li>&bullet;</li>
            <li>&bullet;</li>
        </ul>
    </div>
</div>
<?php $view['slots']->stop(); ?>

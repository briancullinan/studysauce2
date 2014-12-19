<?php
use Symfony\Bundle\FrameworkBundle\Templating\TimedPhpEngine;

/** @var TimedPhpEngine $view */
$view->extend('Course2Bundle:Shared:layout.html.php');

$view['slots']->start('body'); ?>
<div class="panel-pane course2 step3" id="course2_spaced_repetition-step3">
    <div class="pane-content">
        <h2>Study, Rinse, Repeat.</h2>
        <?php foreach ($view['assetic']->image(['@Course2Bundle/Resources/public/images/reward10.gif'], [], ['output' => 'bundles/course2/images/*']) as $url): ?>
            <img width="100%" src="<?php echo $view->escape($url) ?>" alt="LOGO" />
        <?php endforeach; ?>
        <div class="award">
            <h3>Badge awarded:</h3>
            <span class="medal beginner-checklist">&nbsp;</span>
            <strong>Space it out</strong>
            <p class="description">You have now learned the science behind why it is so important to space out your studies.  Make Ebbinghaus proud and defeat the Forgetting Curve.</p>
        </div>
        <div class="highlighted-link">
            <a href="<?php print $view['router']->generate('course2_spaced_repetition', ['_step' => 4]); ?>" class="more">Next</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php $view['slots']->stop(); ?>



<?php
use Symfony\Bundle\FrameworkBundle\Templating\TimedPhpEngine;

/** @var TimedPhpEngine $view */
$view->extend('Course2Bundle:Shared:layout.html.php');

$view['slots']->start('body'); ?>
<div class="panel-pane course2 step3" id="course2_teaching-step3">
    <div class="pane-content">
        <h2>Just remember to teach well!</h2>
        <?php foreach ($view['assetic']->image(['@Course2Bundle/Resources/public/images/reward8.gif'], [], ['output' => 'bundles/course2/images/*']) as $url): ?>
            <img width="100%" src="<?php echo $view->escape($url) ?>" alt="LOGO" />
        <?php endforeach; ?>
        <div class="award">
            <h3>Badge awarded:</h3>
            <span class="medal yoda">&nbsp;</span>
            <strong>Teach or teach not</strong>
            <p class="description">Use your powers of influence wisely.  Teaching can be extraordinarily effective as a study strategy.</p>
        </div>
        <div class="highlighted-link">
            <a href="<?php print $view['router']->generate('course2_teaching', ['_step' => 4]); ?>" class="more">Next</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php $view['slots']->stop(); ?>



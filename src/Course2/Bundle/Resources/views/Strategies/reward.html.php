<?php
use Symfony\Bundle\FrameworkBundle\Templating\TimedPhpEngine;

/** @var TimedPhpEngine $view */
$view->extend('Course2Bundle:Shared:layout.html.php');

$view['slots']->start('body'); ?>
<div class="panel-pane course2 step3" id="course2_strategies-step3">
    <div class="pane-content">
        <h2>Having a good strategy is key to studying</h2>
        <?php foreach ($view['assetic']->image(['@Course2Bundle/Resources/public/images/reward6.gif'], [], ['output' => 'bundles/course2/images/*']) as $url): ?>
            <img width="100%" src="<?php echo $view->escape($url) ?>" alt="LOGO" />
        <?php endforeach; ?>
        <div class="award">
            <h3>Badge awarded:</h3>
            <span class="medal beginner-sponsored">&nbsp;</span>
            <strong>Strategery</strong>
            <p class="description">Get ready to become a brilliant strategerist....er, strategist.</p>
        </div>
        <div class="highlighted-link">
            <a href="<?php print $view['router']->generate('course2_strategies', ['_step' => 4]); ?>" class="more">Next</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php $view['slots']->stop(); ?>



<?php
use Symfony\Bundle\FrameworkBundle\Templating\TimedPhpEngine;

/** @var TimedPhpEngine $view */
$view->extend('Course2Bundle:Shared:layout.html.php');

$view['slots']->start('body'); ?>
<div class="panel-pane course2 step3" id="course2_interleaving-step3">
    <div class="pane-content">
        <h2>Train like an elite athlete.</h2>
        <?php foreach ($view['assetic']->image(['@Course2Bundle/Resources/public/images/reward1.gif'],[],['output' => 'bundles/course2/images/*']) as $url): ?>
            <img width="100%" src="<?php echo $view->escape($url) ?>" alt="LOGO"/>
        <?php endforeach; ?>
        <div class="award">
            <h3>Badge awarded:</h3>
            <span class="medal beginner-mix">&nbsp;</span>
            <strong>Mix It Up</strong>
            <p class="description">Studying different topics sequentially is an important tool in maximizing your brain's effectiveness to retain information.  For maximum benefit, select very different subjects to give parts of your brain a break.</p>
        </div>
        <div class="highlighted-link">
            <a href="<?php print $view['router']->generate('course2_interleaving', ['_step' => 4]); ?>" class="more">Next</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>
<?php $view['slots']->stop(); ?>

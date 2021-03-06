<?php
use Symfony\Bundle\FrameworkBundle\Templating\TimedPhpEngine;

/** @var TimedPhpEngine $view */
$view->extend('Course1Bundle:Shared:layout.html.php');

$view['slots']->start('body'); ?>
<div class="panel-pane course1 step3" id="course1_partners-step3">
    <div class="pane-content">
        <h2>Sometimes your best friend can be a poor choice...</h2>
        <?php foreach ($view['assetic']->image(['@Course1Bundle/Resources/public/images/reward6.gif'], [], ['output' => 'bundles/course1/images/*']) as $url): ?>
            <img width="100%" src="<?php echo $view->escape($url) ?>" alt="LOGO" />
        <?php endforeach; ?>
        <div class="award">
            <h3>Badge awarded:</h3>
            <span class="medal setup-linked">&nbsp;</span>
            <strong>Partner pair</strong>
            <p class="description">Using an accountability partner can give you a great boost in school.  You have learned about the concept, now invite someone to help!</p>
        </div>
        <div class="highlighted-link">
            <a href="<?php print $view['router']->generate('course1_partners', ['_step' => 4]); ?>" class="more">Next</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php $view['slots']->stop(); ?>

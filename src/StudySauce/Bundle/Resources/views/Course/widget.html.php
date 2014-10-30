<?php
use Course1\Bundle\Entity\Course1;
/** @var Course1 $course */
$percent = empty($course) ? 0 : round($course->getStep()/5.0*100);
?>
<div class="widget-wrapper">
    <div class="widget course-widget">
        <h3><?php print $percent; ?>% of course complete</h3>
        <div class="percent">
            <?php foreach ($view['assetic']->image(
                ['@StudySauceBundle/Resources/public/images/logo_middle_transparent.png'],
                [],
                ['output' => 'bundles/studysauce/images/*']
            ) as $url): ?>
                <img width="150" height="150" src="<?php echo $view->escape($url) ?>" alt="LOGO"/>
            <?php endforeach; ?>
            <div class="percent-background">&nbsp;</div>
            <div class="percent-bars" style="height:<?php print $percent; ?>%;">&nbsp;</div>
        </div>
        <div class="highlighted-link"><a href="<?php print $view['router']->generate('lesson1', ['_step' => empty($course) ? 0 : $course->getStep()]); ?>" class="more">Next module</a></div>
    </div>
</div>
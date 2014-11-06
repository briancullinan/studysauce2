<?php
use Course1\Bundle\Entity\Course1;
/** @var Course1 $course */
$level = empty($course) || /* base 1 index */ empty($course->getLevel()) ? 1 : $course->getLevel();
$step = empty($course) ? 0 : $course->getStep();
// add one becuse we always go to the next step
if($step == 4) {
    $step = 0;
    $level++;
}
else
    $step++;
$percent = empty($course) ? 0 : round($step/5.0*100);
$overall = round((($level - 1) * 5 + $step) / 20 * 100);
?>
<div class="widget-wrapper">
    <div class="widget course-widget">
        <h3><?php print $overall; ?>% of course complete</h3>
        <div class="percent">
            <?php foreach ($view['assetic']->image(
                ['@StudySauceBundle/Resources/public/images/logo_middle_transparent.png'],
                [],
                ['output' => 'bundles/studysauce/images/*']
            ) as $url): ?>
                <img width="150" height="150" src="<?php echo $view->escape($url) ?>" alt="LOGO"/>
            <?php endforeach; ?>
            <div class="percent-background">&nbsp;</div>
            <?php if($level > 4) { ?>
                <div class="percent-bars" style="height:100%; background-color:#F90;">&nbsp;</div>
            <?php } else { ?>
                <div class="percent-bars" style="height:<?php print $percent; ?>%;">&nbsp;</div>
            <?php } ?>
        </div>
        <div class="highlighted-link">
            <?php if($level > 4) { ?>
                <h4>Complete!</h4>
            <?php } else { ?>
                <a href="<?php print $view['router']->generate('lesson' . $level, ['_step' => $step]); ?>" class="more">Next module</a>
            <?php } ?>
        </div>
    </div>
</div>
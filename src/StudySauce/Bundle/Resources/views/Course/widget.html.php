<?php
use Course1\Bundle\Course1Bundle;
use Course1\Bundle\Entity\Course1;
/** @var Course1 $course */
$level = empty($course) ? 0 : $course->getLevel();
$step = empty($course) ? 0 : $course->getStep();
// add one because we always go to the next step
if($step == 4) {
    $step = 0;
    $level++;
}
else
    $step++;
//$percent = empty($course) ? 0 : round($step/5.0*100);
$overall = round((empty($level) ? 0.0 : ($level - 1.0)) * 100.0 / Course1Bundle::COUNT_LEVEL);
?>
<div class="widget-wrapper">
    <div class="widget course-widget">
        <h3><?php print $overall; ?>% of course complete</h3>
        <div class="percent">
            <?php foreach ($view['assetic']->image(
                ['@StudySauceBundle/Resources/public/images/logo_middle_transparent_compressed.png'],
                [],
                ['output' => 'bundles/studysauce/images/*']
            ) as $url): ?>
                <img width="150" height="150" src="<?php echo $view->escape($url) ?>" alt="LOGO"/>
            <?php endforeach; ?>
            <div class="percent-background">&nbsp;</div>
            <?php if($level > Course1Bundle::COUNT_LEVEL) { ?>
                <div class="percent-bars" style="height:100%; background-color:#F90;">&nbsp;</div>
            <?php } else { ?>
                <div class="percent-bars" style="height:<?php print $overall; ?>%;">&nbsp;</div>
            <?php } ?>
        </div>
        <div class="highlighted-link">
            <?php if($level > Course1Bundle::COUNT_LEVEL) { ?>
                <h4>Complete!</h4>
            <?php } else { ?>
                <a href="<?php print $view['router']->generate('_welcome'); ?>course/1/lesson/<?php print (empty($level) ? 1 : $level); ?>/step/<?php print (empty($level) ? 0 : $step); ?>" class="more">Next module</a>
            <?php } ?>
        </div>
    </div>
</div>
<?php
use Course1\Bundle\Course1Bundle;
use Course1\Bundle\Entity\Course1;
use Course2\Bundle\Course2Bundle;

/** @var Course1 $course1 */
$completed = 0;
$next = 1;
if(!empty($course1) && $course1->getLesson1() === 4) {
    $completed++;
    if($next === 1) $next = 2;
}
if(!empty($course1) && $course1->getLesson2() === 4) {
    $completed++;
    if($next === 2) $next = 3;
}
if(!empty($course1) && $course1->getLesson3() === 4) {
    $completed++;
    if($next === 3) $next = 4;
}
if(!empty($course1) && $course1->getLesson4() === 4) {
    $completed++;
    if($next === 4) $next = 5;
}
if(!empty($course1) && $course1->getLesson5() === 4) {
    $completed++;
    if($next === 5) $next = 6;
}
if(!empty($course1) && $course1->getLesson6() === 4) {
    $completed++;
    if($next === 6) $next = 7;
}
if(!empty($course1) && $course1->getLesson7() === 4) {
    $completed++;
    // next go to course 2
}

$completed2 = 0;
$next2 = 1;
if(!empty($course2) && $course2->getLesson1() === 4) {
    $completed++;
    if($next === 1) $next = 2;
}
if(!empty($course2) && $course2->getLesson2() === 4) {
    $completed++;
    if($next === 2) $next = 3;
}
if(!empty($course2) && $course2->getLesson3() === 4) {
    $completed++;
    if($next === 3) $next = 4;
}
if(!empty($course2) && $course2->getLesson4() === 4) {
    $completed++;
    if($next === 4) $next = 5;
}
if(!empty($course2) && $course2->getLesson5() === 4) {
    $completed++;
    if($next === 5) $next = 6;
}
$overall = $completed >= Course1Bundle::COUNT_LEVEL
    ? round($completed * 100.0 / Course1Bundle::COUNT_LEVEL)
    : round($completed2 * 100.0 / Course2Bundle::COUNT_LEVEL);
?>
<div class="widget-wrapper">
    <div class="widget course-widget">
        <h3><?php print $overall; ?>% of course complete</h3>
        <div class="percent">
            <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/logo_middle_transparent_compressed.png'],[],['output' => 'bundles/studysauce/images/*']) as $url): ?>
                <img width="150" height="150" src="<?php echo $view->escape($url) ?>" alt="LOGO"/>
            <?php endforeach; ?>
            <div class="percent-background">&nbsp;</div>
            <div class="percent-bars" style="height:<?php print $overall; ?>%;">&nbsp;</div>
        </div>
        <div class="highlighted-link">
            <?php if($completed >= Course1Bundle::COUNT_LEVEL && $completed2 >= Course2Bundle::COUNT_LEVEL) { ?>
                <h4>Complete!</h4>
            <?php } elseif($completed < Course1Bundle::COUNT_LEVEL) { ?>
                <a href="<?php print $view['router']->generate('_welcome'); ?>course/1/lesson/<?php print $next; ?>/step/0" class="more">Next module</a>
            <?php } else { ?>
                <a href="<?php print $view['router']->generate('_welcome'); ?>course/2/lesson/<?php print $next2; ?>/step/0" class="more">Next module</a>
            <?php } ?>
        </div>
    </div>
</div>
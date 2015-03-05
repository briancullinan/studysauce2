<?php
use Course1\Bundle\Entity\Course1;
use Course2\Bundle\Entity\Course2;
use Course3\Bundle\Entity\Course3;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;

/** @var GlobalVariables $app */

/** @var User $user */
$user = $app->getUser();

/** @var Course1 $course1 */
/** @var Course2 $course2 */
/** @var Course3 $course3 */
$next = 1;
$course = 1;
if(!empty($course1) && $course1->getLesson1() === 4) {
    if($next === 1) $next = 2;
}
if(!empty($course1) && $course1->getLesson2() === 4) {
    if($next === 2) $next = 3;
}
if(!empty($course1) && $course1->getLesson3() === 4) {
    if($next === 3) $next = 4;
}
if(!empty($course1) && $course1->getLesson4() === 4) {
    if($next === 4) $next = 5;
}
if(!empty($course1) && $course1->getLesson5() === 4) {
    if($next === 5) $next = 6;
}
if(!empty($course1) && $course1->getLesson6() === 4) {
    if($next === 6) {
        if($course1->getUser()->hasRole('ROLE_PAID')) {
            $next = 1;
            $course = 2;
        }
        else {
            $next = 7;
        }
    }
}
// don't include this in the count
if(!empty($course1) && $course1->getLesson7() === 4 && !$course1->getUser()->hasRole('ROLE_PAID')) {
    // next go to course 2
    if($next === 7) {
        $next = 1;
        $course = 2;
    }
}

// course 2
if(!empty($course2) && $course2->getLesson1() === 4) {
    if($next === 1) $next = 2;
}
if(!empty($course2) && $course2->getLesson2() === 4) {
    if($next === 2) $next = 3;
}
if(!empty($course2) && $course2->getLesson3() === 4) {
    if($next === 3) $next = 4;
}
if(!empty($course2) && $course2->getLesson4() === 4) {
    if($next === 4) $next = 5;
}
if(!empty($course2) && $course2->getLesson5() === 4) {
    // go to course 3
    if($next === 5) {
        $next = 1;
        $course = 3;
    }
}

// course 3
if(!empty($course3) && $course3->getLesson1() === 4) {
    if($next === 1) $next = 2;
}
if(!empty($course3) && $course3->getLesson2() === 4) {
    if($next === 2) $next = 3;
}
if(!empty($course3) && $course3->getLesson3() === 4) {
    if($next === 3) $next = 4;
}
if(!empty($course3) && $course3->getLesson4() === 4) {
    if($next === 4) $next = 5;
}
if(!empty($course3) && $course3->getLesson5() === 4) {
    // TODO: done?
}
?>
<div class="widget-wrapper">
    <div class="widget course-widget">
        <h3><?php print $user->getCompleted(); ?>% of course complete</h3>
        <div class="percent">
            <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/logo_middle_transparent_compressed.png'],[],['output' => 'bundles/studysauce/images/*']) as $url): ?>
                <img width="150" height="150" src="<?php echo $view->escape($url) ?>" alt="LOGO"/>
            <?php endforeach; ?>
            <div class="percent-background">&nbsp;</div>
            <div class="percent-bars" style="height:<?php print $user->getCompleted(); ?>%;">&nbsp;</div>
        </div>
        <div class="highlighted-link">
            <?php if($user->getCompleted() == 100) { ?>
                <h4>Complete!</h4>
            <?php } else { ?>
                <a href="<?php print $view['router']->generate('_welcome'); ?>course/<?php print $course; ?>/lesson/<?php print $next; ?>/step/0" class="more">Next module</a>
            <?php } ?>
        </div>
    </div>
</div>
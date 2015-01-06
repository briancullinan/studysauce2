<?php

use Course1\Bundle\Entity\Course1;
use Course2\Bundle\Entity\Course2;
/** @var Course1 $course1 */
/** @var Course2 $course2 */

/** @var @var User $user */
$user = $app->getUser();

$complete = !empty($course1) && $course1->getLesson1() == 4 && $course1->getLesson2() == 4 && $course1->getLesson3() == 4 &&
    $course1->getLesson4() == 4 && $course1->getLesson5() == 4 && $course1->getLesson6() == 4 &&
    $course1->getLesson7() == 4;

?>
<aside id="left-panel" class="collapsed">
    <nav>
        <a href="#expand"><span class="navbar-toggle"><span class="icon-bar"></span><span class="icon-bar"></span>
            <span class="icon-bar"></span></span></a>

        <ul id="course1-menu" class="main-menu accordion">
            <li><a href="#collapse">Hide</a><h3>Course</h3></li>
            <li class="accordion-group panel">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse" data-target="#level1" data-parent="#course1-menu"><span>1</span>Level 1</a>
                </div>
                <ul id="level1" class="accordion-body collapse <?php print ($complete ? '' : 'in'); ?>">
                    <li class="<?php print (!empty($course1) && $course1->getLesson1() === 4 ? 'complete' : ''); ?>">
                        <a href="<?php print $view['router']->generate('course1_introduction', ['_step' => 0]); ?>"><span>&bullet;</span>Introduction to Study Sauce</a></li>
                    <li class="<?php print (!empty($course1) && $course1->getLesson2() === 4 ? 'complete' : ''); ?>">
                        <a href="<?php print $view['router']->generate('course1_setting_goals', ['_step' => 0]); ?>"><span>&bullet;</span>Setting goals</a></li>
                    <li class="<?php print (!empty($course1) && $course1->getLesson3() === 4 ? 'complete' : ''); ?>">
                        <a href="<?php print $view['router']->generate('course1_distractions', ['_step' => 0]); ?>"><span>&bullet;</span>Distractions</a></li>
                    <li class="<?php print (!empty($course1) && $course1->getLesson4() === 4 ? 'complete' : ''); ?>">
                        <a href="<?php print $view['router']->generate('course1_procrastination', ['_step' => 0]); ?>"><span>&bullet;</span>Procrastination</a></li>
                    <li class="<?php print (!empty($course1) && $course1->getLesson5() === 4 ? 'complete' : ''); ?>">
                        <a href="<?php print $view['router']->generate('course1_environment', ['_step' => 0]); ?>"><span>&bullet;</span>Study environment</a></li>
                    <li class="<?php print (!empty($course1) && $course1->getLesson6() === 4 ? 'complete' : ''); ?>">
                        <a href="<?php print $view['router']->generate('course1_partners', ['_step' => 0]); ?>"><span>&bullet;</span>Partners</a></li>
                    <?php if(!$user->hasRole('ROLE_PAID')) { ?>
                    <li class="<?php print (!empty($course1) && $course1->getLesson7() === 4 ? 'complete' : ''); ?>">
                        <a href="<?php print $view['router']->generate('course1_upgrade', ['_step' => 0]); ?>"><span>&bullet;</span>End of Level 1</a></li>
                    <?php } ?>
                </ul>
            </li>
            <li class="accordion-group panel">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse" data-target="#level2" data-parent="#course1-menu"><span>2</span>Level 2</a>
                </div>
                <ul id="level2" class="accordion-body collapse <?php print ($complete ? 'in' : ''); ?>">
                    <li class="<?php print (!empty($course2) && $course2->getLesson1() === 4 ? 'complete' : ''); ?>">
                        <a href="<?php print $view['router']->generate('course2_study_metrics', ['_step' => 0]); ?>"><span>&bullet;</span>Study metrics</a></li>
                    <li class="<?php print (!empty($course2) && $course2->getLesson2() === 4 ? 'complete' : ''); ?>">
                        <a href="<?php print $view['router']->generate('course2_study_plan', ['_step' => 0]); ?>"><span>&bullet;</span>Study plan</a></li>
                    <li class="<?php print (!empty($course2) && $course2->getLesson3() === 4 ? 'complete' : ''); ?>">
                        <a href="<?php print $view['router']->generate('course2_interleaving', ['_step' => 0]); ?>"><span>&bullet;</span>Interleaving</a></li>
                    <li class="<?php print (!empty($course2) && $course2->getLesson4() === 4 ? 'complete' : ''); ?>">
                        <a href="<?php print $view['router']->generate('course2_study_tests', ['_step' => 0]); ?>"><span>&bullet;</span>Studying for tests</a></li>
                    <li class="<?php print (!empty($course2) && $course2->getLesson5() === 4 ? 'complete' : ''); ?>">
                        <a href="<?php print $view['router']->generate('course2_test_taking', ['_step' => 0]); ?>"><span>&bullet;</span>Test-taking</a></li>
                </ul>
            </li>
            <li class="accordion-group panel">
                <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse" data-target="#level3" data-parent="#course1-menu"><span>3</span>Level 3</a>
                </div>
                <ul id="level3" class="accordion-body collapse">
                    <li class="<?php print (!empty($course3) && $course3->getLesson1() === 4 ? 'complete' : ''); ?>">
                        <a href="<?php print $view['router']->generate('course3_strategies', ['_step' => 0]); ?>"><span>&bullet;</span>Intro to strategies</a></li>
                    <li class="<?php print (!empty($course3) && $course3->getLesson2() === 4 ? 'complete' : ''); ?>">
                        <a href="<?php print $view['router']->generate('course3_group_study', ['_step' => 0]); ?>"><span>&bullet;</span>Group study</a></li>
                    <li class="<?php print (!empty($course3) && $course3->getLesson3() === 4 ? 'complete' : ''); ?>">
                        <a href="<?php print $view['router']->generate('course3_teaching', ['_step' => 0]); ?>"><span>&bullet;</span>Teach to learn</a></li>
                    <li class="<?php print (!empty($course3) && $course3->getLesson4() === 4 ? 'complete' : ''); ?>">
                        <a href="<?php print $view['router']->generate('course3_active_reading', ['_step' => 0]); ?>"><span>&bullet;</span>Active reading</a></li>
                    <li class="<?php print (!empty($course3) && $course3->getLesson5() === 4 ? 'complete' : ''); ?>">
                        <a href="<?php print $view['router']->generate('course3_spaced_repetition', ['_step' => 0]); ?>"><span>&bullet;</span>Spaced repetition</a></li>
                </ul>
            </li>
        </ul>
    </nav>
</aside>

<?php

use StudySauce\Bundle\Controller\ScheduleController;
use StudySauce\Bundle\Entity\Course;
use StudySauce\Bundle\Entity\Schedule;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

/** @var $demo \StudySauce\Bundle\Entity\Schedule */
/** @var $view \Symfony\Bundle\FrameworkBundle\Templating\PhpEngine */
/** @var $app \Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables */
/** @var $view \Symfony\Bundle\FrameworkBundle\Templating\PhpEngine */


$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');
foreach ($view['assetic']->stylesheets(['@StudySauceBundle/Resources/public/css/schedule.css'],[],['output' => 'bundles/studysauce/css/*.css']) as $url): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>" />
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts(['@StudySauceBundle/Resources/public/js/schedule.js'],[],['output' => 'bundles/studysauce/js/*.js']) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('body'); ?>
<div class="panel-pane <?php print (count($schedules) <= 1 ? 'single' : 'multi'); ?> <?php print ($needsNew ? 'needs-new-schedule' : ''); ?>" id="schedule">
    <div class="pane-content">
    <?php if($app->getRequest()->get('_format') == 'funnel') {
        echo $view->render('StudySauceBundle:Buy:funnel.html.php');
    } else { ?>
        <h2>Enter your class below</h2>
        <div class="schedule-history">
            <a href="#manage-terms" class="subtle" data-toggle="modal">Create a new schedule</a>
            <a href="#prev-schedule" class="subtle disabled"><span></span> Previous</a>
            <h3 class="term-label"></h3>
            <a href="#next-schedule" class="subtle">Next <span></span></a>
        </div>
    <?php } ?>
    <form action="<?php print $view['router']->generate('update_schedule'); ?>" method="post" novalidate="novalidate">
        <?php foreach($schedules as $schedule) {
            /** @var $schedule Schedule */
            ?>
            <div class="term-row schedule-id-<?php print $schedule->getId(); ?>">
                <?php
                $isDemo = false;
                $courses = array_values($schedule->getClasses()->toArray());
                $others = array_values($schedule->getOthers()->toArray());
                if(empty($courses)) {
                    $isDemo = true;
                    $schedule = $demoSchedules[0];
                    $courses = array_values($schedule->getClasses()->toArray());
                } ?>
                <input type="hidden" name="term-name" value="<?php print ($isDemo ? '' : (empty($schedule->getTerm()) ? $schedule->getCreated()->format('n/Y') : $schedule->getTerm()->format('n/Y'))); ?>" />
                <div class="university">
                    <label class="input">
                        School name
                        <input type="text" placeholder="Enter the full name" data-data="<?php print (!empty($schedule)
                            ? htmlentities(json_encode(ScheduleController::getInstitutions(!$isDemo ? $schedule->getUniversity() : '')->first()), ENT_QUOTES)
                            : ''); ?>" value="<?php print (!empty($schedule) && !$isDemo ? $schedule->getUniversity() : ''); ?>" autocomplete="off">
                    </label>
                </div>
                <header>
                    <label>&nbsp;</label>
                    <div class="day-of-the-week">
                        <label>M</label>
                        <label>Tu</label>
                        <label>W</label>
                        <label>Th</label>
                        <label>F</label>
                        <label>Sa</label>
                        <label>Su</label>
                    </div>
                    <label>Class Time</label>
                    <label>Class Date</label>
                </header>
                <div class="schedule clearfix">
                    <?php
                    foreach ($courses as $i => $c) {
                        if ($isDemo && $i > 5) {
                            break;
                        }
                        /** @var $c Course */
                        $daysOfTheWeek = $c->getDotw();
                        $startDate = null;
                        $endDate = null;
                        if (!empty($c->getStartTime())) {
                            $startDate = strtotime($c->getStartTime()->format('Y/m/d H:i:s'));
                        }
                        if (!empty($c->getEndTime())) {
                            $endDate = strtotime($c->getEndTime()->format('Y/m/d H:i:s'));
                        }
                        ?>
                        <div class="class-row clearfix<?php
                        print ($isDemo ? ' edit valid blank' : ' read-only');
                        print ' course-id-' . ($isDemo ? '' : $c->getId()); ?>">
                            <div class="class-name">
                                <label class="input">
                                    <span>Class name</span>
                                    <i class="class<?php print $i; ?>"></i>
                                    <input type="text" value="<?php print (!$isDemo ? $c->getName() : ''); ?>"
                                           placeholder="<?php print ScheduleController::getRandomName(); ?>" autocomplete="off">
                                </label>
                            </div>
                            <div class="day-of-the-week">
                                <label class="checkbox"><span>M</span>
                                    <input type="checkbox" value="M" <?php print (!$isDemo && in_array('M', $daysOfTheWeek)
                                        ? 'checked="checked"'
                                        : ''); ?>><i></i></label>
                                <label class="checkbox"><span>Tu</span>
                                    <input type="checkbox" value="Tu" <?php print (!$isDemo && in_array('Tu',$daysOfTheWeek)
                                        ? 'checked="checked"'
                                        : ''); ?>><i></i></label>
                                <label class="checkbox"><span>W</span>
                                    <input type="checkbox" value="W" <?php print (!$isDemo && in_array('W',$daysOfTheWeek)
                                        ? 'checked="checked"'
                                        : ''); ?>><i></i></label>
                                <label class="checkbox"><span>Th</span>
                                    <input type="checkbox" value="Th" <?php print (!$isDemo && in_array('Th',$daysOfTheWeek)
                                        ? 'checked="checked"'
                                        : ''); ?>><i></i></label>
                                <label class="checkbox"><span>F</span>
                                    <input type="checkbox" value="F" <?php print (!$isDemo && in_array('F',$daysOfTheWeek)
                                        ? 'checked="checked"'
                                        : ''); ?>><i></i></label>
                                <label class="checkbox"><span>Sa</span>
                                    <input type="checkbox" value="Sa" <?php print (!$isDemo && in_array('Sa',$daysOfTheWeek)
                                        ? 'checked="checked"'
                                        : ''); ?>><i></i></label>
                                <label class="checkbox"><span>Su</span>
                                    <input type="checkbox" value="Su" <?php print (!$isDemo && in_array('Su',$daysOfTheWeek)
                                        ? 'checked="checked"'
                                        : ''); ?>><i></i></label>
                            </div>
                            <div class="start-time">
                                <label class="input">
                                    <span>Time</span>
                                    <input type="text" placeholder="Start" title="What time does your class begin?"
                                           autocomplete="off"
                                           value="<?php print ($isDemo || $startDate == null ? '' : date('H:i:s', $startDate)); ?>">
                                </label>
                                <label class="input mobile-only">
                                    <span>Time</span>
                                    <input type="time" title="What time does your class begin?"
                                           autocomplete="off"
                                           value="<?php print ($isDemo || $startDate == null ? '' : date('H:i:s', $startDate)); ?>">
                                </label>
                            </div>
                            <div class="end-time">
                                <label class="input">
                                    <span>&nbsp;</span>
                                    <input type="text" placeholder="End" title="What time does your class end?" autocomplete="off"
                                           value="<?php print ($isDemo || $endDate == null ? '' : date('H:i:s', $endDate)); ?>">
                                </label>
                                <label class="input mobile-only">
                                    <span>&nbsp;</span>
                                    <input type="time" title="What time does your class end?" autocomplete="off"
                                           value="<?php print ($isDemo || $endDate == null ? '' : date('H:i:s', $endDate)); ?>">
                                </label>
                            </div>
                            <div class="start-date">
                                <label class="input">
                                    <span>Date</span>
                                    <input type="text" placeholder="First class" title="What day does your academic term begin?"
                                           autocomplete="off"
                                           value="<?php print ($isDemo || $startDate == null ? '' : date('m/d/y', $startDate)); ?>">
                                </label>
                                <label class="input mobile-only">
                                    <span>Date</span>
                                    <input type="date" placeholder="First class" title="What day does your academic term begin?"
                                           autocomplete="off"
                                           value="<?php print ($isDemo || $startDate == null ? '' : date('Y-m-d', $startDate)); ?>">
                                </label>
                            </div>
                            <div class="end-date">
                                <label class="input">
                                    <span>&nbsp;</span>
                                    <input type="text" placeholder="Last class" title="What day does your academic term end?"
                                           autocomplete="off"
                                           value="<?php print ($isDemo || $endDate == null ? '' : date('m/d/y', $endDate)); ?>">
                                </label>
                                <label class="input mobile-only">
                                    <span>&nbsp;</span>
                                    <input type="date" placeholder="Last class" title="What day does your academic term end?"
                                           autocomplete="off"
                                           value="<?php print ($isDemo || $endDate == null ? '' : date('Y-m-d', $endDate)); ?>">
                                </label>
                            </div>
                            <input type="hidden" name="event-type" value="c">
                            <div class="read-only"><a href="#edit-class">&nbsp;</a><a href="#remove-class">&nbsp;</a></div>
                        </div>
                    <?php } ?>

                    <div class="form-actions highlighted-link clearfix invalid">
                        <a href="#add-class" class="big-add">Add <span>+</span> class</a>
                        <div class="invalid-times">Error - invalid class time</div>
                        <div class="overlaps-only">Error - classes cannot overlap</div>
                        <div class="invalid-only">Error - please make sure all class information is filled in</div>
                        <?php if($app->getRequest()->get('_format') == 'funnel') { ?>
                            <button type="submit" value="#save-class" class="more">Next</button>
                        <?php } else { ?>
                            <button type="submit" value="#save-class" class="more">Save</button>
                        <?php } ?>
                    </div>
                </div>
                <hr/>
                <h2>Enter work or other recurring obligations here</h2>
                <header>
                    <label>&nbsp;</label>
                    <div class="day-of-the-week">
                        <label>M</label>
                        <label>Tu</label>
                        <label>W</label>
                        <label>Th</label>
                        <label>F</label>
                        <label>Sa</label>
                        <label>Su</label>
                    </div>
                    <label>Time</label>
                    <label>&nbsp;Date</label>
                </header>
                <div class="schedule other">
                    <?php
                    if(empty($others)) {
                        $isDemo = true;
                        $schedule = $demoSchedules[0];
                        $others = array_values($schedule->getOthers()->toArray());
                    }
                    foreach ($others as $i => $c) {
                        if ($isDemo && $i > 0) {
                            break;
                        }
                        /** @var $c Course */
                        $daysOfTheWeek = $c->getDotw();
                        $startDate = null;
                        $endDate = null;
                        if (!empty($c->getStartTime())) {
                            $startDate = strtotime($c->getStartTime()->format('Y/m/d H:i:s'));
                        }
                        if (!empty($c->getEndTime())) {
                            $endDate = strtotime($c->getEndTime()->format('Y/m/d H:i:s'));
                        }
                        ?>
                        <div class="class-row clearfix <?php
                        print ($isDemo ? ' edit valid blank' : ' read-only');
                        print ' course-id-' . ($isDemo ? '' : $c->getId()); ?>">
                            <div class="class-name">
                                <label class="input">
                                    <span>Event title</span>
                                    <i></i><input type="text" value="<?php print (!$isDemo ? $c->getName() : ''); ?>"
                                                  placeholder="<?php print ScheduleController::getRandomOther(); ?>"
                                                  autocomplete="off">
                                </label>
                            </div>
                            <div class="day-of-the-week">
                                <label class="checkbox"><span>M</span>
                                    <input type="checkbox" value="M" <?php print (!$isDemo && in_array('M', $daysOfTheWeek)
                                        ? 'checked="checked"'
                                        : ''); ?>><i></i></label>
                                <label class="checkbox"><span>Tu</span>
                                    <input type="checkbox" value="Tu" <?php print (!$isDemo && in_array('Tu',$daysOfTheWeek)
                                        ? 'checked="checked"'
                                        : ''); ?>><i></i></label>
                                <label class="checkbox"><span>W</span>
                                    <input type="checkbox" value="W" <?php print (!$isDemo && in_array('W',$daysOfTheWeek)
                                        ? 'checked="checked"'
                                        : ''); ?>><i></i></label>
                                <label class="checkbox"><span>Th</span>
                                    <input type="checkbox" value="Th" <?php print (!$isDemo && in_array('Th',$daysOfTheWeek)
                                        ? 'checked="checked"'
                                        : ''); ?>><i></i></label>
                                <label class="checkbox"><span>F</span>
                                    <input type="checkbox" value="F" <?php print (!$isDemo && in_array('F',$daysOfTheWeek)
                                        ? 'checked="checked"'
                                        : ''); ?>><i></i></label>
                                <label class="checkbox"><span>Sa</span>
                                    <input type="checkbox" value="Sa" <?php print (!$isDemo && in_array('Sa',$daysOfTheWeek)
                                        ? 'checked="checked"'
                                        : ''); ?>><i></i></label>
                                <label class="checkbox"><span>Su</span>
                                    <input type="checkbox" value="Su" <?php print (!$isDemo && in_array('Su',$daysOfTheWeek)
                                        ? 'checked="checked"'
                                        : ''); ?>><i></i></label>
                            </div>
                            <div class="start-time">
                                <label class="input">
                                    <span>Time</span>
                                    <input type="text"  title="What time does your class begin?" placeholder="Start" autocomplete="off"
                                           value="<?php print ($isDemo || $startDate == null ? '' : date('H:i:s',$startDate)); ?>">
                                </label>
                                <label class="input mobile-only">
                                    <span>Time</span>
                                    <input type="time" title="What time does your class begin?" autocomplete="off"
                                           value="<?php print ($isDemo || $startDate == null ? '' : date('H:i:s',$startDate)); ?>">
                                </label>
                            </div>
                            <div class="end-time">
                                <label class="input">
                                    <span>&nbsp;</span>
                                    <input type="text" title="What time does your class end?" placeholder="End" autocomplete="off"
                                           value="<?php print ($isDemo || $endDate == null ? '' : date('H:i:s', $endDate)); ?>">
                                </label>
                                <label class="input mobile-only">
                                    <span>&nbsp;</span>
                                    <input type="time" title="What time does your class end?" autocomplete="off"
                                           value="<?php print ($isDemo || $endDate == null ? '' : date('H:i:s', $endDate)); ?>">
                                </label>
                            </div>
                            <div class="start-date">
                                <label class="input">
                                    <span>Date</span>
                                    <input type="text" placeholder="Start" title="What day does your academic term begin?"
                                           autocomplete="off"
                                           value="<?php print ($isDemo || $startDate == null ? '' : date('m/d/y', $startDate)); ?>">
                                </label>
                                <label class="input mobile-only">
                                    <span>Date</span>
                                    <input type="date" placeholder="Start" title="What day does your academic term begin?"
                                           autocomplete="off"
                                           value="<?php print ($isDemo || $startDate == null ? '' : date('Y-m-d', $startDate)); ?>">
                                </label>
                            </div>
                            <div class="end-date">
                                <label class="input">
                                    <span>&nbsp;</span>
                                    <input type="text" placeholder="End" title="What day does your academic term end?"
                                           autocomplete="off"
                                           value="<?php print ($isDemo || $endDate == null ? '' : date('m/d/y', $endDate)); ?>">
                                </label>
                                <label class="input mobile-only">
                                    <span>&nbsp;</span>
                                    <input type="date" placeholder="End" title="What day does your academic term end?"
                                           autocomplete="off"
                                           value="<?php print ($isDemo || $endDate == null ? '' : date('Y-m-d', $endDate)); ?>">
                                </label>
                            </div>
                            <div class="read-only"><a href="#edit-class">&nbsp;</a><a href="#remove-class">&nbsp;</a></div>
                            <input type="hidden" name="event-type" value="o">
                        </div>
                    <?php } ?>
                    <div class="form-actions highlighted-link clearfix invalid">
                        <a href="#add-class" class="big-add">Add <span>+</span> other event</a>
                        <div class="invalid-times">Error - invalid class time</div>
                        <div class="overlaps-only">Error - classes cannot overlap</div>
                        <div class="invalid-only">Error - please make sure all class information is filled in</div>
                        <?php if($app->getRequest()->get('_format') == 'funnel') { ?>
                            <button type="submit" value="#save-class" class="more">Next</button>
                        <?php } else { ?>
                            <button type="submit" value="#save-class" class="more">Save</button>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>"/>
    </form>
    </div>
    </div>
<?php $view['slots']->stop();

$view['slots']->start('sincludes');
print $this->render('StudySauceBundle:Dialogs:new-schedule.html.php', ['id' => 'new-schedule']);
print $this->render('StudySauceBundle:Dialogs:manage-terms.html.php', ['id' => 'manage-terms', 'schedule' => $schedules[0]]);
$view['slots']->stop();


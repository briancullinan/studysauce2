<?php

use StudySauce\Bundle\Controller\ScheduleController;
use StudySauce\Bundle\Entity\Course;
use StudySauce\Bundle\Entity\Schedule;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

/** @var $demo \StudySauce\Bundle\Entity\Schedule */
/** @var $view \Symfony\Bundle\FrameworkBundle\Templating\PhpEngine */
/** @var $app \Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables */
/** @var $schedule Schedule */
/** @var $view \Symfony\Bundle\FrameworkBundle\Templating\PhpEngine */


$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');
foreach ($view['assetic']->stylesheets(
    [
        '@StudySauceBundle/Resources/public/css/schedule.css'
    ],
    [],
    ['output' => 'bundles/studysauce/css/*.css']
) as $url):
    ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>" />
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts(
    [
        '@StudySauceBundle/Resources/public/js/schedule.js'
    ],
    [],
    ['output' => 'bundles/studysauce/js/*.js']
) as $url):
    ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('body'); ?>
<div class="panel-pane" id="schedule">
    <div class="pane-content">
    <?php if($app->getRequest()->get('_format') == 'funnel') {
        echo $view->render('StudySauceBundle:Buy:funnel.html.php');
    } else { ?>
        <h2>Enter your class below</h2>
    <?php } ?>
    <div class="university">
        <label class="input">
            School name
            <input type="text" placeholder="Enter the full name" data-data="<?php print (!empty($schedule)
                ? htmlentities(
                    json_encode(ScheduleController::getInstitutions($schedule->getUniversity())->first()),
                    ENT_QUOTES
                )
                : ''); ?>"
                   value="<?php print (!empty($schedule) ? $schedule->getUniversity() : ''); ?>" autocomplete="off">
        </label>
    </div>

    <header>
        <label>Class name</label>

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
        $isDemo = false; // || $app->getUser()->hasRole('ROLE_GUEST');
        if (empty($courses)) {
            $isDemo = true;
            $courses = $demoCourses;
        }
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
                </div>
                <div class="end-date">
                    <label class="input">
                        <span>&nbsp;</span>
                        <input type="text" placeholder="Last class" title="What day does your academic term end?"
                               autocomplete="off"
                               value="<?php print ($isDemo || $endDate == null ? '' : date('m/d/y', $endDate)); ?>">
                    </label>
                </div>
                <input type="hidden" name="event-type" value="c">

                <div class="read-only"><a href="#edit-class">&nbsp;</a><a href="#remove-class">&nbsp;</a></div>
            </div>
        <?php } ?>
    </div>

    <div class="form-actions highlighted-link clearfix invalid">
        <a href="#add-class">Add <span>+</span> class</a>
        <div class="invalid-times">Error - invalid class time</div>
        <div class="overlaps-only">Error - classes cannot overlap</div>
        <div class="invalid-only">Error - please make sure all class information is filled in</div>
        <?php if($app->getRequest()->get('_format') == 'funnel') { ?>
            <a href="#save-class" class="more">Next</a>
        <?php } else { ?>
            <a href="#save-class" class="more" style="<?php print (!$isDemo ? 'visibility:hidden;' : ''); ?>">Save</a>
            <a href="<?php print $view['router']->generate('customization'); ?>">Customize courses</a>
        <?php } ?>
    </div>
    <hr/>
    <h2>Enter work or other recurring obligations here</h2>

    <header>
        <label>Class name</label>

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
        if (empty($others)) {
            $isDemo = true;
            $others = $demoOthers;
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

                    <div class="recurring">
                        <label class="checkbox">
                            <input type="checkbox" value="Weekly" <?php print ($isDemo || in_array('Weekly',$daysOfTheWeek)
                                ? 'checked="checked"'
                                : ''); ?>><span>Recurring</span><i></i><span>Weekly</span></label>
                    </div>
                </div>
                <div class="start-time">
                    <div class="input">
                        <label><span>Time</span>
                            <input type="text" placeholder="Start" autocomplete="off"
                                   value="<?php print ($isDemo || $startDate == null ? '' : date(
                                       'H:i:s',
                                       $startDate
                                   )); ?>">
                        </label>
                    </div>
                    <div class="input mobile-only">
                        <label><span>Time</span>
                            <input type="time" title="What time does your class begin?" autocomplete="off"
                                   value="<?php print ($isDemo || $startDate == null ? '' : date(
                                       'H:i:s',
                                       $startDate
                                   )); ?>">
                        </label>
                    </div>
                </div>
                <div class="end-time">
                    <label class="input">
                        <span>&nbsp;</span>
                        <input type="text" placeholder="End" autocomplete="off"
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
                        <input type="text" placeholder="Start"
                               autocomplete="off"
                               value="<?php print ($isDemo || $startDate == null ? '' : date('m/d/y', $startDate)); ?>">
                    </label>
                </div>
                <div class="end-date">
                    <label class="input">
                        <span>&nbsp;</span>
                        <input type="text" placeholder="End"
                               autocomplete="off"
                               value="<?php print ($isDemo || $endDate == null ? '' : date('m/d/y', $endDate)); ?>">
                    </label>
                </div>
                <div class="read-only"><a href="#edit-class">&nbsp;</a><a href="#remove-class">&nbsp;</a></div>
                <input type="hidden" name="event-type" value="o">
            </div>
        <?php } ?>
    </div>
    <div class="form-actions highlighted-link clearfix invalid">
        <a href="#add-other">Add <span>+</span> other event</a>
        <div class="invalid-times">Error - invalid class time</div>
        <div class="overlaps-only">Error - classes cannot overlap</div>
        <div class="invalid-only">Error - please make sure all class information is filled in</div>
        <?php if($app->getRequest()->get('_format') == 'funnel') { ?>
            <a href="#save-class" class="more">Next</a>
        <?php } else { ?>
            <a href="#save-class" class="more" style="<?php print (!$isDemo ? 'visibility:hidden;' : ''); ?>">Save</a>
        <?php } ?>
    </div>
    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>"/>
    </div>
    </div>
<?php $view['slots']->stop();


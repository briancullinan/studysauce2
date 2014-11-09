<?php
use StudySauce\Bundle\Entity\Course;
use StudySauce\Bundle\Entity\Deadline;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

$isDemo = false;
if (empty($deadlines) || empty($courses)) {
    $isDemo = true;
    $deadlines = $demoDeadlines;
}

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');
foreach ($view['assetic']->stylesheets(
    [
        '@StudySauceBundle/Resources/public/css/deadlines.css'
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
        '@StudySauceBundle/Resources/public/js/deadlines.js'
    ],
    [],
    ['output' => 'bundles/studysauce/js/*.js']
) as $url):
    ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('row-headings'); ?>
<div class="highlighted-link form-actions invalid">
    <a href="#add-deadline">Add <span>+</span> class</a>
    <a href="#save-deadline" class="more">Save</a>
</div>
<div class="sort-by" <?php print($isDemo ? 'style="display:none;"' : ''); ?>>
    <label>Sort by: </label>
    <label class="radio"><input type="radio" name="deadlines-sort" value="date"
                                checked="checked"/><i></i>Date</label>
    <label class="radio"><input type="radio" name="deadlines-sort" value="class"><i></i>Class</label>
    <label class="checkbox" title="Click here to see deadlines that have already passed."><input
            type="checkbox"><i></i>Past deadlines</label>
</div>
<header <?php print($isDemo ? 'style="display:none;"' : ''); ?>>
    <label>&nbsp;</label>
    <label>Assignment</label>
    <label>Reminders</label>
    <label>Due date</label>
    <label>% of grade</label>
</header>
<?php $view['slots']->stop();

$view['slots']->start('body'); ?>
<div class="panel-pane <?php print ($isDemo ? ' empty' : ''); ?>" id="deadlines">
    <div class="pane-content">
        <h2>Enter important dates and we will send you email reminders</h2>
        <?php
        if(!$isDemo)
            $view['slots']->output('row-headings');
        $headStr = '';
        foreach ($deadlines as $i => $d) {
            if ($isDemo && $i > 1) {
                break;
            }

            /** @var $d Deadline */
            if (!$isDemo) {
                $newHeadStr = $d->getDueDate()->format('j F') . ' <span>' . $d->getDueDate()->format('Y') . '</span>';
                if ($headStr != $newHeadStr) {
                    $headStr = $newHeadStr;
                    $classes = [];
                    if ($d->getDueDate() < date_add(new \Datetime(), new \DateInterval('P1D'))) {
                        $classes[] = 'hide';
                    }
                    print '<div class="head ' . implode(' ', $classes) . '">' . $headStr . '</div>';
                }
                if ($d->getReminder() != null) {
                    $reminders = $d->getReminder();
                } else {
                    $reminders = [];
                }
            }

            $classI = array_search($d->getCourse(), $courses);

            ?>
            <div class="deadline-row first invalid <?php
            print ($isDemo ? 'edit' : 'read-only');
            print (!empty($d->getCourse()) ? (' course-id-' . $d->getCourse()->getId()) : '');
            print ($d->getDueDate() < date_add(new \Datetime(), new \DateInterval('P1D')) ? ' hide' : '');
            print (' deadline-id-' . $d->getId()); ?>">
            <div class="class-name">
                <label class="select">
                    <span>Class name</span>
                    <i class="class<?php print $classI; ?>"></i>
                    <select>
                        <option value="" <?php print ($isDemo || empty($d->getCourse()) ? 'selected="selected"' : ''); ?>>Select a class</option>
                        <?php
                        $found = false;
                        foreach ($courses as $c):
                            $found = true;
                            /** @var $c Course */
                            ?>
                            <option value="<?php print $c->getId(); ?>" <?php print (!$isDemo &&
                                $d->getCourse() == $c ? 'selected="selected"' : ''); ?>><?php print $c->getName(); ?></option>
                        <?php endforeach; ?>
                        <option value="Nonacademic" <?php print (!$isDemo && !empty($d->getAssignment()) &&
                            empty($d->getCourse()) ? 'selected="selected"' : ''); ?>>Nonacademic
                        </option>
                    </select>
                </label>
            </div>
            <div class="assignment">
                <label class="select">
                    <span>Assignment</span>
                    <input placeholder="Paper, exam, project, etc." type="text"
                           value="<?php print (!$isDemo ? $d->getAssignment() : ''); ?>" size="60" maxlength="255">
                </label>
            </div>
            <div class="reminder">
                <label>Reminders</label>
                <label class="checkbox"><input type="checkbox" value="1209600" <?php print (!$isDemo && in_array(
                        1209600,
                        $reminders
                    ) ? 'checked="checked"' : ''); ?>><i></i><br/>2 wk</label>
                <label class="checkbox"><input type="checkbox" value="604800" <?php print (!$isDemo && in_array(
                        604800,
                        $reminders
                    ) ? 'checked="checked"' : ''); ?>><i></i><br/>1 wk</label>
                <label class="checkbox"><input type="checkbox" value="345600" <?php print (!$isDemo && in_array(
                        345600,
                        $reminders
                    ) ? 'checked="checked"' : ''); ?>><i></i><br/>4 days</label>
                <label class="checkbox"><input type="checkbox" value="172800" <?php print (!$isDemo && in_array(
                        172800,
                        $reminders
                    ) ? 'checked="checked"' : ''); ?>><i></i><br/>2 days</label>
                <label class="checkbox"><input type="checkbox" value="86400" <?php print (!$isDemo && in_array(
                        86400,
                        $reminders
                    ) ? 'checked="checked"' : ''); ?>><i></i><br/>1 day</label>
            </div>
            <div class="due-date">
                <label class="input">
                    <span>Due date</span>
                    <input placeholder="Enter due date" type="text"
                           value="<?php print (!$isDemo ? $d->getDueDate()->format('m/d/Y') : ''); ?>" size="5"
                           maxlength="255">
                </label>
            </div>
            <div class="percent" <?php print (!$isDemo && !empty($d->getAssignment()) &&
                empty($d->getCourse()) ? 'style="visibility:hidden;"' : ''); ?>>
                <label class="input">
                    <span>% of grade</span>
                    <input type="text" placeholder="% of grade" value="<?php print (!$isDemo && $d->getPercent() > 0 ? $d->getPercent() : ''); ?>" size="2"
                           maxlength="255">
                </label>
            </div>
            <div class="read-only">
                <a href="#edit-deadline">&nbsp;</a><a href="#remove-deadline">&nbsp;</a>
            </div>
            </div>
        <?php }
        if($isDemo)
            $view['slots']->output('row-headings');
        ?>
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>"/>
        <div class="highlighted-link form-actions invalid">
            <a href="<?php print $view['router']->generate('schedule'); ?>">Edit schedule</a><a href="#save-deadline" class="more">Save</a>
        </div>
    </div>
</div>
<?php $view['slots']->stop();

$view['slots']->start('sincludes');
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:deadlinesEmpty'));
$view['slots']->stop();

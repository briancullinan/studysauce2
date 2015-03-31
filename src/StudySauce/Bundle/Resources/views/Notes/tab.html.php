<?php

use StudySauce\Bundle\Entity\Course;
use StudySauce\Bundle\Entity\Schedule;

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');
foreach ($view['assetic']->stylesheets(['@StudySauceBundle/Resources/public/css/notes.css'], [], ['output' => 'bundles/studysauce/css/*.css']) as $url): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts(['@StudySauceBundle/Resources/public/js/notes.js'], [], ['output' => 'bundles/studysauce/js/*.js']) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach; ?>
<?php $view['slots']->stop();

$view['slots']->start('body'); ?>
<div class="panel-pane" id="notes">
    <div class="pane-content">
        <h2>Study notes</h2>
        <?php
        $first = true;
        foreach($schedules as $s) {
        /** @var Schedule $s */
        ?>
        <div class="term-row schedule-id-<?php print $s->getId(); ?> <?php print ($first ? 'selected' : ''); ?>">
            <div class="term-name"><?php
                for($y = intval(date('Y')); $y > intval(date('Y')) - 20; $y--)
                {
                    foreach([11 => 'Winter', 8 => 'Fall', 1 => 'Spring', 6 => 'Summer'] as $m => $t)
                    {
                        // skip dates beyond the current term
                        if($y == date('Y') && $m > intval(date('n')) && (empty($s->getTerm()) || $s->getTerm()->format('n/Y') != $m . '/' . $y))
                            continue;

                        if(!empty($s->getTerm()) && $s->getTerm()->format('n/Y') == $m . '/' . $y) {
                            print $t . ' ' . $y;
                        }
                    }
                }
                ?>
            </div>
            <div class="term-editor">
                <?php
                $courses = array_values($s->getClasses()->toArray());
                foreach($courses as $i => $c) {
                /** @var Course $c */
                ?>
                <div class="class-row course-id-<?php print $c->getId(); ?> <?php print ($first && !$c->getGrades()->count() ? 'selected' : ''); ?>">
                    <div class="class-name read-only"><label class="input"><span>Class name</span><i class="class0"></i><input type="text" value="<?php print $c->getName(); ?>" placeholder="Class name"></label></div>
                    <div class="notes">
                        <div class="note-row">
                            <h4 class="note-name"></h4>
                            <div class="summary">this is a note summary</div>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
        <?php } ?>
    </div>
</div>
<?php $view['slots']->stop();

$view['slots']->start('sincludes');

$view['slots']->stop();

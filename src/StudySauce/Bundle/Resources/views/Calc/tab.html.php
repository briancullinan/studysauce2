<?php
use StudySauce\Bundle\Entity\Course;
use StudySauce\Bundle\Entity\Grade;
use StudySauce\Bundle\Entity\Schedule;

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');
foreach ($view['assetic']->stylesheets(['@StudySauceBundle/Resources/public/css/calc.css'],[],['output' => 'bundles/studysauce/css/*.css']) as $url): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts(['@StudySauceBundle/Resources/public/js/calc.js'],[],['output' => 'bundles/studysauce/js/*.js']) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
    foreach ($view['assetic']->javascripts(['@StudySauceBundle/Resources/public/js/schedule.js'],[],['output' => 'bundles/studysauce/js/*.js']) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach; ?>
<script type="text/javascript">
    window.presetScale = <?php print json_encode(\StudySauce\Bundle\Controller\CalcController::$presets); ?>;
</script>
<?php $view['slots']->stop();

$view['slots']->start('body'); ?>
    <div class="panel-pane <?php print (empty($schedules) || empty($schedules[0]->getClasses()->count()) ? 'empty' : ''); ?>" id="calculator">
        <div class="pane-content">
            <h2>Grade calculator</h2>
            <div class="highlighted-link"><a href="#what-if" data-toggle="modal" class="more">What if <small>?</small>?<strong>?</strong></a></div>
            <form action="<?php print $view['router']->generate('calculator_update'); ?>" method="post">
                <p>
                    <strong class="projected" title="Your GPA for the current term based on your grades to date."><?php print (empty($termGPA) ? '&bullet;' : $termGPA); ?></strong><span> Projected GPA (this term)</span>
                    <strong class="cumulative" title="Your GPA to date which excludes the current term."><?php print (empty($overallGPA) ? '&bullet;' : $overallGPA); ?></strong><span> Cumulative GPA</span>
                </p>
                <?php
                $first = true;
                foreach($schedules as $s) {
                    /** @var Schedule $s */
                    ?>
                <div class="term-row schedule-id-<?php print $s->getId(); ?> <?php print ($first ? 'selected' : ''); ?>">
                    <div class="term-name read-only"><label class="input"><select><?php
                        for($y = intval(date('Y')); $y > intval(date('Y')) - 20; $y--)
                        {
                            foreach([11 => 'Winter', 8 => 'Fall', 1 => 'Spring', 6 => 'Summer'] as $m => $t)
                            {
                                // skip dates beyond the current term
                                if($y == date('Y') && $m > intval(date('n')) && (empty($s->getTerm()) || $s->getTerm()->format('n/Y') != $m . '/' . $y))
                                    continue;

                                ?><option value="<?php print $m; ?>/<?php print $y; ?>" <?php
                                print (!empty($s->getTerm()) && $s->getTerm()->format('n/Y') == $m . '/' . $y
                                    ? 'selected="selected"'
                                    : ''); ?>><?php
                                print $t; ?> <?php print $y; ?></option><?php
                            }
                        }
                        ?></select></label></div>
                    <div class="gpa"><?php print (empty($s->getGPA()) ? '&bullet;' : $s->getGPA()); ?></div>
                    <div class="hours"><?php print (empty($s->getCreditHours()) ? '&bullet;' : $s->getCreditHours()); ?></div>
                    <div class="term-editor">
                        <label></label>
                        <header>
                            <label title="How many graded credit hours the class is worth.  Enter 0 if the class does not count toward your GPA (ex. classes taken pass/fail or non-credit courses)">Hours</label>
                            <label title="How much of your course grade the assignment is worth">% of grade</label>
                            <label title="Your grade on the assignment">Score</label>
                            <label title="Your letter grade based on your grading scale">Grade</label>
                            <label title="Your grade point for the class (calculates your GPA)">Grade point</label>
                        </header>
                        <?php
                        $courses = array_values($s->getClasses()->toArray());
                        foreach($courses as $i => $c) {
                            /** @var Course $c */
                            ?>
                            <div class="class-row course-id-<?php print $c->getId(); ?> <?php print ($first && !$c->getGrades()->count() ? 'selected' : ''); ?>">
                                <div class="class-name <?php print ($first ? 'read-only' : 'edit'); ?>"><label class="input"><span>Class name</span><i class="class<?php print $i; ?>"></i><input type="text" value="<?php print $c->getName(); ?>" placeholder="Class name" /></label></div>
                                <div class="hours <?php print (empty($c->getCreditHours()) ? 'edit' : 'read-only'); ?>" title="How many graded credit hours the class is worth.  Enter 0 if the class does not count toward your GPA (ex. classes taken pass/fail or non-credit courses)"><label class="input"><span>Hours</span><input type="text" value="<?php print $c->getCreditHours(); ?>" placeholder="&bullet;" /></label></div>
                                <div class="percent" title="How much of your course grade the assignment is worth"><?php print (empty($c->getPercent()) ? '&bullet;' : ($c->getPercent() . '%')); ?></div>
                                <div class="score" title="Your grade on the assignment"><?php print (empty($c->getScore()) ? '&bullet;' : number_format($c->getScore(), 2)); ?></div>
                                <div class="grade <?php print ($first ? 'read-only' : 'edit'); ?>" title="Your letter grade based on your grading scale"><label class="input"><span>Grade</span><select>
                                            <option value="" <?php print (empty($c->getGrade()) ? 'selected="selected"' : ''); ?>>&bullet;</option>
                                            <?php for($i = 0; $i < count($scale); $i++) {
                                            if (!empty($scale[$i]) && count($scale[$i]) == 4 && !empty($scale[$i][0])) { ?>
                                                <option value="<?php print $scale[$i][0]; ?>" <?php print ($c->getGrade() == $scale[$i][0] ? 'selected="selected"' : ''); ?>><?php print $scale[$i][0]; ?></option>
                                            <? }} ?>
                                        </select></label></div>
                                <div class="gpa" title="Your grade point for the class (calculates your GPA)"><?php print (empty($c->getGPA()) ? '&bullet;' : $c->getGPA()); ?></div>
                            </div>
                            <div class="grade-editor">
                                <?php
                                $isDemo = false;
                                if(empty($grades = $c->getGrades()->toArray()))
                                {
                                    $isDemo = true;
                                    for($k = 0; $k < 4; $k++)
                                    {
                                        if(count($grades) < 4)
                                            $grades[] = new Grade();
                                    }
                                }
                                foreach($grades as $j => $d) {
                                    /** @var Grade $d */
                                    if($isDemo && $j >= 4)
                                        break;
                                    ?>
                                    <div class="grade-row grade-id-<?php print (!$isDemo ? $d->getId() : ''); ?> <?php print ($isDemo || $d->getScore() === null || $d->getPercent() === null ? ' edit' : 'read-only'); ?>">
                                        <div class="assignment">
                                            <label class="input"><span>Assignment</span>
                                                <input type="text" value="<?php print (!$isDemo ? $d->getAssignment() : ''); ?>" placeholder="<?php print ($isDemo && !empty($d->getAssignment()) ? $d->getAssignment() : 'Assignment'); ?>" /></label></div>
                                        <div class="percent" title="How much of your course grade the assignment is worth"><label class="input"><span>%</span><input type="text" value="<?php print (!$isDemo && !empty($d->getPercent()) ? $d->getPercent() : ''); ?>" placeholder="&bullet;" /></label></div>
                                        <div class="score" title="Your grade on the assignment"><label class="input"><span>Score</span><input type="text" value="<?php print $d->getScore(); ?>" placeholder="&bullet;" /></label></div>
                                        <div class="grade" title="Your letter grade based on your grading scale"><span><?php print (empty($d->getGrade()) ? '&bullet;' : $d->getGrade()); ?></span></div>
                                        <div class="gpa" title="Your grade point for the class (calculates your GPA)"><?php print (empty($d->getGPA()) ? '&bullet;' : $d->getGPA()); ?></div>
                                        <div class="read-only"><a href="#edit-grade">&nbsp;</a><a href="#remove-grade">&nbsp;</a></div>
                                    </div>
                                <?php } ?>
                                <div class="highlighted-link form-actions invalid">
                                    <a href="#add-grade" class="big-add">Add <span>+</span> grade</a>
                                    <button type="submit" value="#save-grades" class="more">Save</button>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="highlighted-link form-actions invalid">
                            <div class="clearfix">
                                <div class="empty-hours">* Enter credit hours to calculate class GPA</div>
                                <div class="over-percent">* Enter assignments to total 100%</div>
                            </div>
                            <a href="#add-class" class="big-add">Add <span>+</span> class</a>
                            <a href="<?php print $view['router']->generate('schedule'); ?>">Edit schedule</a>
                            <button type="submit" value="#save-grades" class="more">Save</button>
                        </div>
                    </div>
                </div>
                <?php $first = false; } ?>
                <div class="highlighted-link form-actions invalid">
                    <a href="#add-schedule" class="big-add">Add <span>+</span> semester</a>
                    <a href="#grade-scale" data-toggle="modal">Change grade scale</a>
                    <div class="invalid-only">You must complete all fields before moving on.</div>
                    <button type="submit" value="#save-grades" class="more">Save</button>
                </div>
            </form>
        </div>
    </div>
<?php $view['slots']->stop();

$view['slots']->start('sincludes');
print $this->render('StudySauceBundle:Dialogs:grade-scale.html.php', ['id' => 'grade-scale', 'scale' => $scale]);
print $this->render('StudySauceBundle:Dialogs:calc-empty.html.php', ['id' => 'calc-empty']);
print $this->render('StudySauceBundle:Dialogs:what-if.html.php', ['id' => 'what-if', 'scale' => $scale]);
$view['slots']->stop();

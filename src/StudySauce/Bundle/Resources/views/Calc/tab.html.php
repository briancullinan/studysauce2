<?php
use StudySauce\Bundle\Entity\Course;
use StudySauce\Bundle\Entity\Deadline;
use StudySauce\Bundle\Entity\Grade;
use StudySauce\Bundle\Entity\ParentInvite;
use StudySauce\Bundle\Entity\Payment;
use StudySauce\Bundle\Entity\Schedule;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Templating\TimedPhpEngine;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');
foreach ($view['assetic']->stylesheets(
    ['@StudySauceBundle/Resources/public/css/calc.css'],
    [],
    ['output' => 'bundles/studysauce/css/*.css']
) as $url): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts(
    ['@StudySauceBundle/Resources/public/js/calc.js'],
    [],
    ['output' => 'bundles/studysauce/js/*.js']
) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('body'); ?>
    <div class="panel-pane" id="calculator">
        <div class="pane-content">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#gpa-calc" data-target="#gpa-calc" data-toggle="tab">My GPA</a></li>
                <li><a href="#what-if" data-target="#what-if" data-toggle="tab">What if <small>?</small>?<strong>?</strong></a></li>
            </ul>
            <div class="tab-content">
                <div id="gpa-calc" class="tab-pane active">
                    <form action="" method="post">
                        <h2>Grade calculator</h2>
                        <p>
                            <strong class="projected">3.70</strong><span> Projected GPA (this term)</span>
                            <strong class="cumulative">3.50</strong><span> Cumulative GPA (all past terms)</span>
                        </p>
                        <?php
                        $first = true;
                        foreach($schedules as $s) {
                            /** @var Schedule $s */
                            $name = 'Current term';
                            if(!$first) {
                                if(empty($s->getClasses()->count()))
                                    $start = $s->getCreated();
                                else
                                    $start = date_timestamp_set(new \DateTime(), array_sum($s->getClasses()->map(function (Course $c) {return $c->getStartTime()->getTimestamp();})->toArray()) / $s->getClasses()->count());
                                if($start->format('n') >= 11)
                                    $name = 'Winter ' . $start->format('Y');
                                elseif($start->format('n') >= 8)
                                    $name = 'Fall ' . $start->format('Y');
                                elseif($start->format('n') <= 5)
                                    $name = 'Spring ' . $start->format('Y');
                                else
                                    $name = 'Summer ' . $start->format('Y');
                            }
                            ?>
                        <div class="term-row <?php print ($first ? 'selected' : ''); ?>">
                            <div class="term-name"><?php print $name; ?></div>
                            <div class="gpa">3.6 (projected)</div>
                            <div class="percent">60%</div>
                            <div class="hours">9 hrs</div>
                            <div class="term-editor">
                                <header>
                                    <label></label>
                                    <label>Score</label>
                                    <label>Grade</label>
                                    <label>Grade point</label>
                                    <label>% of grade</label>
                                    <label>Hours</label>
                                </header>
                                <?php
                                $courses = array_values($s->getClasses()->toArray());
                                foreach($courses as $i => $c) {
                                    /** @var Course $c */
                                    ?>
                                <div class="class-row selected">
                                    <div class="class-name"><span class="class<?php print $i; ?>"></span><?php print $c->getName(); ?></div>
                                    <div class="score">94.5</div>
                                    <div class="grade"><span>A</span></div>
                                    <div class="gpa">4.0</div>
                                    <div class="percent">60%</div>
                                    <div class="hours"><label class="input"><input type="text" value="<?php print $c->getCreditHours(); ?>" placeholder="<?php print $c->getLength() * count($c->getDotw()) / 3600; ?>" /></label></div>
                                    <div class="grade-editor">
                                        <?php
                                        $isDemo = false;
                                        if(empty($grades = $c->getGrades()->toArray()))
                                        {
                                            $isDemo = true;
                                            $grades = $c->getDeadlines()->filter(function (Deadline $d) {return !empty($d->getCourse());})->toArray();
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
                                            <div class="grade-row <?php print ($isDemo ? ' edit' : ''); ?>">
                                                <div class="assignment">
                                                    <label class="input"><input type="text" value="<?php print (!$isDemo ? $d->getAssignment() : ''); ?>" placeholder="<?php print ($isDemo && !empty($d->getAssignment()) ? $d->getAssignment() : 'Assignment'); ?>" /></label></div>
                                                <div class="score"><label class="input"><input type="text" value="" /></label></div>
                                                <div class="grade"><span>A-</span></div>
                                                <div class="gpa">3.7</div>
                                                <div class="percent"><label class="input"><input type="text" value="<?php print (!$isDemo && !empty($d->getPercent()) ? $d->getPercent() : ''); ?>" placeholder="<?php print ($isDemo && !empty($d->getPercent()) ? $d->getPercent() : ''); ?>" /></label></div>
                                                <div class="read-only"><a href="#edit-grade">&nbsp;</a><a href="#remove-grade">&nbsp;</a></div>
                                            </div>
                                        <?php } ?>
                                        <div class="highlighted-link form-actions invalid">
                                            <a href="#add-grade" class="big-add">Add <span>+</span> grade</a>
                                            <button type="submit" value="#save-grades" class="more">Save</button>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                                <a href="<?php print $view['router']->generate('schedule'); ?>">Edit schedule</a>
                            </div>
                        </div>
                        <?php $first = false; } ?>
                        <div class="highlighted-link form-actions invalid">
                            <a href="#add-schedule" class="big-add">Add <span>+</span> semester</a>
                            <a href="#grade-scale" data-toggle="modal">Change grade scale</a>
                            <button type="submit" value="#save-grades" class="more">Save</button>
                        </div>
                    </form>
                </div>
                <div id="what-if" class="tab-pane">
                    <h2>Choose what you want to calculate</h2>

                </div>
            </div>
        </div>
    </div>
<?php $view['slots']->stop();

$view['slots']->start('sincludes');
print $this->render('StudySauceBundle:Dialogs:grade-scale.html.php', ['id' => 'grade-scale', 'scale' => empty($schedules) ? reset($schedules)->getGradeScale() : true]);
$view['slots']->stop();

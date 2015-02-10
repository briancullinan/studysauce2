<?php
use StudySauce\Bundle\Entity\ParentInvite;
use StudySauce\Bundle\Entity\Payment;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Templating\TimedPhpEngine;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');
foreach ($view['assetic']->stylesheets(['@StudySauceBundle/Resources/public/css/calc.css'], [], ['output' => 'bundles/studysauce/css/*.css']) as $url): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>" />
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts(['@StudySauceBundle/Resources/public/js/calc.js'], [], ['output' => 'bundles/studysauce/js/*.js']) as $url): ?>
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
                <div class="term-row selected">
                    <div class="term-name">Current term</div>
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
                        <div class="class-row selected">
                            <div class="class-name"><span class="class0"></span>Calc 101</div>
                            <div class="score">94.5</div>
                            <div class="grade"><span>A</span></div>
                            <div class="gpa">4.0</div>
                            <div class="percent">60%</div>
                            <div class="hours">3</div>
                            <div class="grade-editor">
                                <div class="grade-row">
                                    <div class="assignment">Exam #1</div>
                                    <div class="score">91.0</div>
                                    <div class="grade"><span>A-</span></div>
                                    <div class="gpa">3.7</div>
                                    <div class="percent">50%</div>
                                    <div class="hours"></div>
                                </div>
                                <div class="grade-row">
                                    <div class="assignment">Exam #2</div>
                                    <div class="score">91.0</div>
                                    <div class="grade"><span>A-</span></div>
                                    <div class="gpa">3.7</div>
                                    <div class="percent">50%</div>
                                    <div class="hours"></div>
                                </div>
                                <div class="grade-row">
                                    <div class="assignment">Final Exam</div>
                                    <div class="score">91.0</div>
                                    <div class="grade"><span>A-</span></div>
                                    <div class="gpa">3.7</div>
                                    <div class="percent">50%</div>
                                    <div class="hours"></div>
                                </div>
                                <div class="highlighted-link form-actions invalid">
                                    <a href="#add-grade" class="big-add">Add <span>+</span> grade</a>
                                    <button type="submit" value="#save-grades" class="more">Save</button>
                                </div>
                            </div>
                        </div>
                        <div class="class-row">
                            <div class="class-name"><span class="class1"></span>Econ 101</div>
                            <div class="score">91.0</div>
                            <div class="grade"><span>A-</span></div>
                            <div class="gpa">3.7</div>
                            <div class="percent">50%</div>
                            <div class="hours">3</div>
                        </div>
                        <div class="class-row">
                            <div class="class-name"><span class="class2"></span>Span 101</div>
                            <div class="score">87.0</div>
                            <div class="grade"><span>B+</span></div>
                            <div class="gpa">3.3</div>
                            <div class="percent">70%</div>
                            <div class="hours">3</div>
                        </div>
                        <a href="<?php print $view['router']->generate('schedule'); ?>">Edit schedule</a>
                    </div>
                </div>
                <div class="term-row">
                    <div class="term-name">Fall 2014</div>
                    <div class="gpa">3.6 (projected)</div>
                    <div class="percent">100%</div>
                    <div class="hours">15 hrs</div>
                </div>
                <div class="term-row">
                    <div class="term-name">Spring 2013</div>
                    <div class="gpa">3.2 (projected)</div>
                    <div class="percent">100%</div>
                    <div class="hours">12 hrs</div>
                </div>
                <div class="term-row">
                    <div class="term-name">Fall 2013</div>
                    <div class="gpa">3.4 (projected)</div>
                    <div class="percent">100%</div>
                    <div class="hours">13 hrs</div>
                </div>
                <div class="highlighted-link form-actions invalid">
                    <a href="#add-grade" class="big-add">Add <span>+</span> semester</a>
                    <a href="#grade-scale">Change grade scale</a>
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
<?php $view['slots']->stop(); ?>
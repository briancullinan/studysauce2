<?php
use StudySauce\Bundle\Entity\Goal;

/** @var $outcome Goal */
/** @var $milestone Goal */
/** @var $behavior Goal */

use Symfony\Component\HttpKernel\Controller\ControllerReference;

 $view->extend('StudySauceBundle:Shared:dashboard.html.php');

 $view['slots']->start('stylesheets');

 foreach ($view['assetic']->stylesheets([
        '@StudySauceBundle/Resources/public/css/goals.css'
    ], [], ['output' => 'bundles/studysauce/css/*.css']) as $url):
    ?><link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>" />
<?php endforeach;

 $view['slots']->stop();

 $view['slots']->start('javascripts');

 foreach ($view['assetic']->javascripts([
        '@StudySauceBundle/Resources/public/js/goals.js'
    ], [], ['output' => 'bundles/studysauce/js/*.js']) as $url):
    ?><script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;

 $view['slots']->stop();

 $view['slots']->start('body'); ?>

<div class="panel-pane" id="goals">

    <div class="pane-content">

        <h2>Set study goals for self-motivation</h2>

        <div class="split-description big-arrow">
            <h3>The Science of Setting Goals</h3>
            <img src="/sites/studysauce.com/themes/successinc/images/science.png">

            <p>According to the Incentive Theory of motivation, using rewards increases the likelihood of repeating
                the given activity. By incorporating this powerful psychological principle into study behavior,
                students can incentivize optimal study practices. Studies show that the sooner the reward is given,
                the stronger the positive association with the activity.</p>
        </div>
        <div class="split-description">
            <h3 style="margin-bottom:5px;">The Application</h3>
            <span class="site-name"><strong>Study</strong> Sauce</span>

            <p>Study Sauce combines the best study practices with the incentive to change. Knowledge of the harm or
                benefit of something doesn’t necessarily result in behavioral change (just ask someone that has
                struggled to quit smoking). Creating meaningful study rewards dramatically increases the likelihood
                that a student will adopt effective study habits.</p>
        </div>

        <header>
            <label>&nbsp;</label>
            <label>Goal</label>
            <label>Reward</label>
        </header>

        <div class="goal-row valid <?php print (empty($behavior) ? 'edit' : 'read-only'); ?>">
            <div class="type"><strong>Study Hours</strong></div>
            <div class="behavior">
                <label class="select">
                    <span>Goal</span>
                    <select>
                        <option value="_none">- None -</option>
                        <option value="30" selected="selected">30</option>
                        <option value="25">25</option>
                        <option value="20">20</option>
                        <option value="15">15</option>
                        <option value="10">10</option>
                        <option value="5">5</option>
                    </select>
                    hours per week
                </label>
            </div>
            <div class="reward">
                <label class="input">
                    <span>Reward</span>
                    <textarea placeholder="Ex. $25 gift card" cols="60" rows="2">Movie night</textarea>
                </label>
                <a href="#goal-edit">&nbsp;</a>
            </div>
            <div class="highlighted-link read-only">
                <a class="more" href="#claim">Brag</a>
            </div>
        </div>
        <div class="goal-row valid <?php print (empty($behavior) ? 'hide' : '');

 print (empty($milestone) ? 'edit' : 'read-only'); ?>">
            <div class="type"><strong>Study Milestone</strong></div>
            <div class="milestone">
                <label class="select">
                    <span>Goal</span>
                    <select>
                        <option value="_none">- None -</option>
                        <option value="A">A</option>
                        <option value="A-" selected="selected">A-</option>
                        <option value="B+">B+</option>
                        <option value="B">B</option>
                        <option value="B-">B-</option>
                        <option value="C+">C+</option>
                        <option value="C">C</option>
                    </select>
                    grade on exam/paper
                </label>
            </div>
            <div class="reward">
                <label class="input">
                    <span>Reward</span>
                    <textarea placeholder="Ex. $50 gift card" cols="60" rows="2">Frozen yogurt</textarea>
                </label>
                <a href="#goal-edit">&nbsp;</a>
            </div>
            <div class="highlighted-link read-only">
                <a class="more" href="#claim">Brag</a>
            </div>
        </div>
        <div class="goal-row valid <?php print (empty($milestone) ? 'hide' : '');

 print (empty($outcome) ? 'edit' : 'read-only'); ?>">
            <div class="type"><strong>Study Outcome</strong></div>
            <div class="outcome">
                <label class="select">
                    <span>Goal</span>
                    <select>
                        <option value="_none">- None -</option>
                        <option value="4">4.00</option>
                        <option value="3.75">3.75</option>
                        <option value="3.5" selected="selected">3.50</option>
                        <option value="3.25">3.25</option>
                        <option value="3">3.00</option>
                        <option value="2.75">2.75</option>
                        <option value="2.5">2.50</option>
                        <option value="2.25">2.25</option>
                        <option value="2">2.00</option>
                        <option value="1.75">1.75</option>
                        <option value="1.5">1.50</option>
                        <option value="1.25">1.25</option>
                        <option value="1">1.00</option>
                    </select>
                    Target GPA for the term
                </label>
            </div>
            <div class="reward">
                <label class="input">
                    <span>Reward</span>
                    <textarea placeholder="Ex. Fancy dinner" cols="60" rows="2">Celebration dinner</textarea>
                </label>
                <a href="#goal-edit">&nbsp;</a>
            </div>
            <div class="highlighted-link read-only">
                <a class="more" href="#claim" data-target="#claim">Brag</a>
            </div>
        </div>

        <p class="highlighted-link form-actions invalid">
            <a href="/partner" class="read-only">Now invite someone to help keep you accountable to your goals.</a>
            <a href="#save-goal" class="more">Save</a>
        </p>

        <div id="achievements">
            <div class="grid_6"><strong>July 17th, 2014</strong><img src="https://www.studysauce.com/sites/studysauce.com/files/styles/achievement/public/Exam%20grade%20picture.jpg?itok=5siJ9c4h"><p>A on my test!!!</p></div><p style="margin:0;clear:both;line-height:0;">&nbsp;</p></div>

        <div id="read-more-incentives">
            <img src="/sites/studysauce.com/themes/successinc/images/science.png">

            <h3>The Science of Setting Goals</h3>
            <a href="#read-more">read more</a>
        </div>

        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>" />
    </div>

</div>

<?php echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:achievement'), ['strategy' => 'sinclude']);

 $view['slots']->stop(); ?>

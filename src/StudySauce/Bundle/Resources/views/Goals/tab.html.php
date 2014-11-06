<?php
use StudySauce\Bundle\Entity\Goal;

/** @var $outcome Goal */
/** @var $milestone Goal */
/** @var $behavior Goal */

use Symfony\Component\HttpKernel\Controller\ControllerReference;

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');

foreach ($view['assetic']->stylesheets(
    [
        '@StudySauceBundle/Resources/public/css/goals.css'
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
        '@StudySauceBundle/Resources/public/js/goals.js'
    ],
    [],
    ['output' => 'bundles/studysauce/js/*.js']
) as $url):
    ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('body'); ?>

    <div class="panel-pane" id="goals">

        <div class="pane-content">

            <h2>Set study goals for self-motivation</h2>

            <div class="split-description big-arrow">
                <h3>The Science of Setting Goals</h3>

                <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/science.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                    <img width="48" height="48" src="<?php echo $view->escape($url) ?>" alt="LOGO" />
                <?php endforeach; ?>

                <p>According to the Incentive Theory of motivation, using rewards increases the likelihood of repeating
                    the given activity. By incorporating this powerful psychological principle into study behavior,
                    students can incentivize optimal study practices. Studies show that the sooner the reward is given,
                    the stronger the positive association with the activity.</p>
            </div>
            <div class="split-description">
                <h3>The Application</h3>

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

            <div class="goal-row valid <?php print (empty($behavior) ? 'edit' : 'read-only');
            print (empty($behavior) ? '' : (' gid' . $behavior->getId())); ?>">
                <div class="type"><strong>Study Hours</strong></div>
                <div class="behavior">
                    <label class="select">
                        <span>Goal</span>
                        <select>
                            <option value="_none" <?php print (empty($behavior) || empty($behavior->getGoal()) ? 'selected="selected"' : ''); ?>>None</option>
                            <option value="30" <?php print (!empty($behavior) && $behavior->getGoal() == 30 ? 'selected="selected"' : ''); ?>>30</option>
                            <option value="25" <?php print (!empty($behavior) && $behavior->getGoal() == 25 ? 'selected="selected"' : ''); ?>>25</option>
                            <option value="20" <?php print (!empty($behavior) && $behavior->getGoal() == 20 ? 'selected="selected"' : ''); ?>>20</option>
                            <option value="15" <?php print (!empty($behavior) && $behavior->getGoal() == 15 ? 'selected="selected"' : ''); ?>>15</option>
                            <option value="10" <?php print (!empty($behavior) && $behavior->getGoal() == 10 ? 'selected="selected"' : ''); ?>>10</option>
                            <option value="5" <?php print (!empty($behavior) && $behavior->getGoal() == 5 ? 'selected="selected"' : ''); ?>>5</option>
                        </select>
                        hours per week
                    </label>
                </div>
                <div class="reward">
                    <label class="input">
                        <span>Reward</span>
                        <textarea placeholder="Ex. $25 gift card" cols="60" rows="2"><?php print (empty($behavior) ? '' : $behavior->getReward()); ?></textarea>
                    </label>
                </div>
                <div class="highlighted-link read-only">
                    <a href="#goal-edit">&nbsp;</a>
                    <a class="more" href="#claim">Brag</a>
                </div>
            </div>
            <div class="goal-row valid <?php print (empty($behavior) ? ' hide' : '');
            print (empty($milestone) ? ' edit' : 'read-only');
            print (empty($milestone) ? '' : (' gid' . $milestone->getId())); ?>">
                <div class="type"><strong>Study Milestone</strong></div>
                <div class="milestone">
                    <label class="select">
                        <span>Goal</span>
                        <select>
                            <option value="_none" <?php print (empty($milestone) || empty($milestone->getGoal()) ? 'selected="selected"' : ''); ?>>None</option>
                            <option value="A" <?php print (!empty($milestone) && $milestone->getGoal() == 'A' ? 'selected="selected"' : ''); ?>>A</option>
                            <option value="A-" <?php print (!empty($milestone) && $milestone->getGoal() == 'A-' ? 'selected="selected"' : ''); ?>>A-</option>
                            <option value="B+" <?php print (!empty($milestone) && $milestone->getGoal() == 'B+' ? 'selected="selected"' : ''); ?>>B+</option>
                            <option value="B" <?php print (!empty($milestone) && $milestone->getGoal() == 'B' ? 'selected="selected"' : ''); ?>>B</option>
                            <option value="B-" <?php print (!empty($milestone) && $milestone->getGoal() == 'B-' ? 'selected="selected"' : ''); ?>>B-</option>
                            <option value="C+" <?php print (!empty($milestone) && $milestone->getGoal() == 'C+' ? 'selected="selected"' : ''); ?>>C+</option>
                            <option value="C" <?php print (!empty($milestone) && $milestone->getGoal() == 'C' ? 'selected="selected"' : ''); ?>>C</option>
                        </select>
                        grade on exam/paper
                    </label>
                </div>
                <div class="reward">
                    <label class="input">
                        <span>Reward</span>
                        <textarea placeholder="Ex. $50 gift card" cols="60" rows="2"><?php print (empty($milestone) ? '' : $milestone->getReward()); ?></textarea>
                    </label>
                </div>
                <div class="highlighted-link read-only">
                    <a href="#goal-edit">&nbsp;</a>
                    <a class="more" href="#claim">Brag</a>
                </div>
            </div>
            <div class="goal-row valid <?php print (empty($milestone) ? ' hide' : '');
            print (empty($outcome) ? ' edit' : 'read-only');
            print (empty($outcome) ? '' : (' gid' . $outcome->getId())); ?>">
                <div class="type"><strong>Study Outcome</strong></div>
                <div class="outcome">
                    <label class="select">
                        <span>Goal</span>
                        <select>
                            <option value="_none" <?php print (empty($outcome) || empty($outcome->getGoal()) ? 'selected="selected"' : ''); ?>>None</option>
                            <option value="4" <?php print (!empty($outcome) && $outcome->getGoal() == '4' ? 'selected="selected"' : ''); ?>>4.00</option>
                            <option value="3.75" <?php print (!empty($outcome) && $outcome->getGoal() == '3.75' ? 'selected="selected"' : ''); ?>>3.75</option>
                            <option value="3.5" <?php print (!empty($outcome) && $outcome->getGoal() == '3.5' ? 'selected="selected"' : ''); ?>>3.50</option>
                            <option value="3.25" <?php print (!empty($outcome) && $outcome->getGoal() == '3.25' ? 'selected="selected"' : ''); ?>>3.25</option>
                            <option value="3" <?php print (!empty($outcome) && $outcome->getGoal() == '3' ? 'selected="selected"' : ''); ?>>3.00</option>
                            <option value="2.75" <?php print (!empty($outcome) && $outcome->getGoal() == '2.75' ? 'selected="selected"' : ''); ?>>2.75</option>
                            <option value="2.5" <?php print (!empty($outcome) && $outcome->getGoal() == '2.5' ? 'selected="selected"' : ''); ?>>2.50</option>
                            <option value="2.25" <?php print (!empty($outcome) && $outcome->getGoal() == '2.25' ? 'selected="selected"' : ''); ?>>2.25</option>
                            <option value="2" <?php print (!empty($outcome) && $outcome->getGoal() == '2' ? 'selected="selected"' : ''); ?>>2.00</option>
                            <option value="1.75" <?php print (!empty($outcome) && $outcome->getGoal() == '1.75' ? 'selected="selected"' : ''); ?>>1.75</option>
                            <option value="1.5" <?php print (!empty($outcome) && $outcome->getGoal() == '1.5' ? 'selected="selected"' : ''); ?>>1.50</option>
                            <option value="1.25" <?php print (!empty($outcome) && $outcome->getGoal() == '1.25' ? 'selected="selected"' : ''); ?>>1.25</option>
                            <option value="1" <?php print (!empty($outcome) && $outcome->getGoal() == '1' ? 'selected="selected"' : ''); ?>>1.00</option>
                        </select>
                        target GPA for the term
                    </label>
                </div>
                <div class="reward">
                    <label class="input">
                        <span>Reward</span>
                        <textarea placeholder="Ex. Fancy dinner" cols="60" rows="2"><?php print (empty($outcome) ? '' : $outcome->getReward()); ?></textarea>
                    </label>
                </div>
                <div class="highlighted-link read-only">
                    <a href="#goal-edit">&nbsp;</a>
                    <a class="more" href="#claim" data-target="#claim">Brag</a>
                </div>
            </div>

            <p class="highlighted-link form-actions invalid">
                <a href="#save-goal" class="more">Save</a>
            </p>

            <div id="achievements" class="clearfix">
                <?php echo $view->render('StudySauceBundle:Goals:claims.html.php', ['claims' => $claims]); ?>
            </div>

            <!--
            <div id="read-more-incentives">
                <img src="/sites/studysauce.com/themes/successinc/images/science.png">

                <h3>The Science of Setting Goals</h3>
                <a href="#read-more">read more</a>
            </div>
            -->

            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>"/>
        </div>

    </div>

<?php $view['slots']->stop();

$view['slots']->start('sincludes');
echo $view['actions']->render(
    new ControllerReference('StudySauceBundle:Dialogs:achievement'),
    ['strategy' => 'sinclude']
);
$view['slots']->stop();

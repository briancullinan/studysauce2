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
        <?php echo $view->render('StudySauceBundle:Partner:partner-nav.html.php', ['user' => $user]); ?>
        <h2>Study goals</h2>
        <?php if(empty($behavior)): ?>
            <h3>Your student has not completed this section yet.</h3>
        <?php endif;
        if(!empty($behavior)): ?>
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
                        <option value="_none" <?php print (empty($behavior) || empty($behavior->getGoal()) ? 'selected="selected"' : ''); ?>>- None -</option>
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
        </div>
        <div class="goal-row valid <?php print (empty($behavior) ? ' hide' : '');
        print (empty($milestone) ? ' edit' : 'read-only');
        print (empty($milestone) ? '' : (' gid' . $milestone->getId())); ?>">
            <div class="type"><strong>Study Milestone</strong></div>
            <div class="milestone">
                <label class="select">
                    <span>Goal</span>
                    <select>
                        <option value="_none" <?php print (empty($milestone) || empty($milestone->getGoal()) ? 'selected="selected"' : ''); ?>>- None -</option>
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
        </div>
        <div class="goal-row valid <?php print (empty($milestone) ? ' hide' : '');
        print (empty($outcome) ? ' edit' : 'read-only');
        print (empty($outcome) ? '' : (' gid' . $outcome->getId())); ?>">
            <div class="type"><strong>Study Outcome</strong></div>
            <div class="outcome">
                <label class="select">
                    <span>Goal</span>
                    <select>
                        <option value="_none" <?php print (empty($outcome) || empty($outcome->getGoal()) ? 'selected="selected"' : ''); ?>>- None -</option>
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
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $view['slots']->stop();

$view['slots']->start('sincludes');
echo $view['actions']->render(
    new ControllerReference('StudySauceBundle:Dialogs:achievement'),
    ['strategy' => 'sinclude']
);
$view['slots']->stop();

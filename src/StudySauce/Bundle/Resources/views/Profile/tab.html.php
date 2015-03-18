<?php
use StudySauce\Bundle\Entity\Schedule;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$isDemo = false;
if(!$user->hasRole('ROLE_PAID'))
{
    $isDemo = true;
}

/** @var Schedule $schedule */
$view['slots']->start('stylesheets');
foreach ($view['assetic']->stylesheets(
    [
        '@StudySauceBundle/Resources/public/css/profile.css'
    ],
    [],
    ['output' => 'bundles/studysauce/css/*.css']
) as $url):
    ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts(
    [
        '@StudySauceBundle/Resources/public/js/profile.js'
    ],
    [],
    ['output' => 'bundles/studysauce/js/*.js']
) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('body'); ?>
<div class="panel-pane <?php print ($isDemo ? ' demo' : ''); ?>" id="profile">
    <div class="pane-content">
        <?php if($isDemo) {
            foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/profile_background.jpg'],[],['output' => 'bundles/studysauce/images/*']) as $url): ?>
                <img width="100%" height="100%" alt="Profile" src="<?php echo $view->escape($url) ?>"/>
            <?php endforeach;
        } else { ?>
            <?php if($app->getRequest()->get('_format') == 'funnel') {
                echo $view->render('StudySauceBundle:Buy:funnel.html.php');
            } else { ?>
                <h2>Please tell us more about your study preferences</h2>
            <?php } ?>
        <form action="<?php print $view['router']->generate('profile_update'); ?>" method="post">
            <div class="grades">
                <h3>What kind of grades do you want? <span>Q: 1/3</span></h3>
                <label class="radio"><input name="profile-grades" type="radio"
                                            value="as-only" <?php print ($schedule->getGrades() == 'as-only' ? 'checked="checked"' : ''); ?>><i></i><span>Nothing but As</span></label>
                <label class="radio"><input name="profile-grades" type="radio"
                                            value="has-life" <?php print ($schedule->getGrades() == 'has-life' ? 'checked="checked"' : ''); ?>><i></i><span>I want to do well, but I don't want to live in the library</span></label>
            </div>
            <div class="weekends">
                <h3>How do you manage your weekends? <span>Q: 2/3</span></h3>
                <label class="radio"><input name="profile-weekends" type="radio"
                                            value="hit-hard" <?php print ($schedule->getWeekends() == 'hit-hard' ? 'checked="checked"' : ''); ?>><i></i><span>Hit hard, keep weeks open</span></label>
                <label class="radio"><input name="profile-weekends" type="radio"
                                            value="light-work" <?php print ($schedule->getWeekends() == 'light-work' ? 'checked="checked"' : ''); ?>><i></i><span>Light work, focus during the week</span></label>
            </div>
            <div class="sharpness">
                <h3>On a scale of 0-5 (5 being the best), rate how mentally sharp you feel during the following time periods: <span>Q: 3/3</span></h3>
                <h4>6 AM - 11 AM</h4>
                <label class="radio"><input name="profile-11am" type="radio" value="0" <?php print ($schedule->getSharp6am11am() === 0 ? 'checked="checked"' : ''); ?>><i></i><span>0</span></label>
                <label class="radio"><input name="profile-11am" type="radio" value="1" <?php print ($schedule->getSharp6am11am() == 1 ? 'checked="checked"' : ''); ?>><i></i><span>1</span></label>
                <label class="radio"><input name="profile-11am" type="radio" value="2" <?php print ($schedule->getSharp6am11am() == 2 ? 'checked="checked"' : ''); ?>><i></i><span>2</span></label>
                <label class="radio"><input name="profile-11am" type="radio" value="3" <?php print ($schedule->getSharp6am11am() == 3 ? 'checked="checked"' : ''); ?>><i></i><span>3</span></label>
                <label class="radio"><input name="profile-11am" type="radio" value="4" <?php print ($schedule->getSharp6am11am() == 4 ? 'checked="checked"' : ''); ?>><i></i><span>4</span></label>
                <label class="radio"><input name="profile-11am" type="radio" value="5" <?php print ($schedule->getSharp6am11am() == 5 ? 'checked="checked"' : ''); ?>><i></i><span>5</span></label>
                <h4>11 AM - 4 PM</h4>
                <label class="radio"><input name="profile-4pm" type="radio" value="0" <?php print ($schedule->getSharp11am4pm() === 0 ? 'checked="checked"' : ''); ?>><i></i><span>0</span></label>
                <label class="radio"><input name="profile-4pm" type="radio" value="1" <?php print ($schedule->getSharp11am4pm() == 1 ? 'checked="checked"' : ''); ?>><i></i><span>1</span></label>
                <label class="radio"><input name="profile-4pm" type="radio" value="2" <?php print ($schedule->getSharp11am4pm() == 2 ? 'checked="checked"' : ''); ?>><i></i><span>2</span></label>
                <label class="radio"><input name="profile-4pm" type="radio" value="3" <?php print ($schedule->getSharp11am4pm() == 3 ? 'checked="checked"' : ''); ?>><i></i><span>3</span></label>
                <label class="radio"><input name="profile-4pm" type="radio" value="4" <?php print ($schedule->getSharp11am4pm() == 4 ? 'checked="checked"' : ''); ?>><i></i><span>4</span></label>
                <label class="radio"><input name="profile-4pm" type="radio" value="5" <?php print ($schedule->getSharp11am4pm() == 5 ? 'checked="checked"' : ''); ?>><i></i><span>5</span></label>
                <h4>4 PM - 9 PM</h4>
                <label class="radio"><input name="profile-9pm" type="radio" value="0" <?php print ($schedule->getSharp4pm9pm() === 0 ? 'checked="checked"' : ''); ?>><i></i><span>0</span></label>
                <label class="radio"><input name="profile-9pm" type="radio" value="1" <?php print ($schedule->getSharp4pm9pm() == 1 ? 'checked="checked"' : ''); ?>><i></i><span>1</span></label>
                <label class="radio"><input name="profile-9pm" type="radio" value="2" <?php print ($schedule->getSharp4pm9pm() == 2 ? 'checked="checked"' : ''); ?>><i></i><span>2</span></label>
                <label class="radio"><input name="profile-9pm" type="radio" value="3" <?php print ($schedule->getSharp4pm9pm() == 3 ? 'checked="checked"' : ''); ?>><i></i><span>3</span></label>
                <label class="radio"><input name="profile-9pm" type="radio" value="4" <?php print ($schedule->getSharp4pm9pm() == 4 ? 'checked="checked"' : ''); ?>><i></i><span>4</span></label>
                <label class="radio"><input name="profile-9pm" type="radio" value="5" <?php print ($schedule->getSharp4pm9pm() == 5 ? 'checked="checked"' : ''); ?>><i></i><span>5</span></label>
                <h4>9 PM - 2 AM</h4>
                <label class="radio"><input name="profile-2am" type="radio" value="0" <?php print ($schedule->getSharp9pm2am() === 0 ? 'checked="checked"' : ''); ?>><i></i><span>0</span></label>
                <label class="radio"><input name="profile-2am" type="radio" value="1" <?php print ($schedule->getSharp9pm2am() == 1 ? 'checked="checked"' : ''); ?>><i></i><span>1</span></label>
                <label class="radio"><input name="profile-2am" type="radio" value="2" <?php print ($schedule->getSharp9pm2am() == 2 ? 'checked="checked"' : ''); ?>><i></i><span>2</span></label>
                <label class="radio"><input name="profile-2am" type="radio" value="3" <?php print ($schedule->getSharp9pm2am() == 3 ? 'checked="checked"' : ''); ?>><i></i><span>3</span></label>
                <label class="radio"><input name="profile-2am" type="radio" value="4" <?php print ($schedule->getSharp9pm2am() == 4 ? 'checked="checked"' : ''); ?>><i></i><span>4</span></label>
                <label class="radio"><input name="profile-2am" type="radio" value="5" <?php print ($schedule->getSharp9pm2am() == 5 ? 'checked="checked"' : ''); ?>><i></i><span>5</span></label>
            </div>
            <div class="form-actions highlighted-link">
                <div class="invalid-only">You must complete all fields before moving on.</div>
                <?php if($app->getRequest()->get('_format') == 'funnel') { ?>
                    <button type="submit" value="#save-profile" class="more">Next</button>
                <?php } else { ?>
                    <button type="submit" value="#save-profile" class="more">Save</button>
                    <a href="<?php print $view['router']->generate('customization'); ?>">Customize courses</a>
                <?php } ?>
            </div>
            </form>
        <?php } ?>
    </div>
</div>
<?php $view['slots']->stop();

$view['slots']->start('sincludes');
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:profileUpgrade'));
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:billParents1'), ['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:billParents2'), ['strategy' => 'sinclude']);
$view['slots']->stop();

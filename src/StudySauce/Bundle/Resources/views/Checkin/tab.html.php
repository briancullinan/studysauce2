<?php
use StudySauce\Bundle\Entity\Course;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

$isDemo = false;
if (empty($courses)) {
    $isDemo = true;
    $courses = $demoCourses;
}

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');
foreach ($view['assetic']->stylesheets(
    [
        '@StudySauceBundle/Resources/public/css/clock.css',
        '@StudySauceBundle/Resources/public/css/checkin.css'
    ],
    [],
    ['output' => 'bundles/studysauce/css/*.css']
) as $url):
    ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>" />
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts(['@checkin_scripts'], [], ['output' => 'bundles/studysauce/js/*.js']) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('body'); ?>
<div class="panel-pane <?php print ($isDemo ? 'demo' : ''); ?>" id="checkin">
    <div class="pane-content">
        <h2>Check in.<span class="full-only"> &nbsp;Listen to Mozart. &nbsp;Track your progress.</span></h2>

        <p class="classes">
            <?php
            foreach ($courses as $i => $c) {
                /** @var $c Course */
                ?><a href="#class<?php print $i; ?>"
                     class="checkin class<?php print $i; ?> course-id-<?php print $c->getId(); ?>">
                <span><?php print $c->getName(); ?></span></a><?php
            }
            ?>
        </p>

        <div class="flip-counter flip-clock-wrapper">
            <h3>Take a 10 minute break in 1 hour</h3>
            <?php echo $view->render('StudySauceBundle:Checkin:digits.html.php'); ?>
            <input name="touchedMusic" type="hidden" value="0">

            <div class="player-ui">
                <div class="minplayer-default-big-play ui-state-default">
                    <a class="minplayer-default-play minplayer-default-button ui-state-default ui-corner-all"
                       title="Toggle music on/off">
                        <span class="ui-icon ui-icon-play"></span>
                    </a>
                    <a class="minplayer-default-pause minplayer-default-button ui-state-default ui-corner-all"
                       title="Toggle music on/off" style="display: none;">
                        <span class="ui-icon ui-icon-pause"></span>
                    </a>
                </div>
            </div>
            <h4 style="text-align:center;"><a href="#mozart-effect" data-toggle="modal">The Mozart Effect®</a></h4>
        </div>
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>"/>
        <a href="#add-study-hours" class="big-add" data-toggle="modal">Add <span>+</span> study hours</a>
        <a href="<?php print $view['router']->generate('schedule'); ?>"><span>Edit schedule</span></a>
    </div>
</div>
<?php $view['slots']->stop();

$view['slots']->start('sincludes');
print $this->render('StudySauceBundle:Dialogs:add-study-hours.html.php', ['id' => 'add-study-hours']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:checkinEmpty'));
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:sdsMessages'),['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:checklist'),['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:timerExpire'),['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:mozart'), ['strategy' => 'sinclude']);
$view['slots']->stop();

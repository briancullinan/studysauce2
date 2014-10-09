
<?php
use StudySauce\Bundle\Entity\Course;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

 $view['slots']->start('stylesheets');
 foreach ($view['assetic']->stylesheets([
        '@StudySauceBundle/Resources/public/css/clock.css',
        '@StudySauceBundle/Resources/public/css/checkin.css'
    ], [], ['output' => 'bundles/studysauce/css/*.css']) as $url):
    ?><link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>" />
<?php endforeach;
 $view['slots']->stop();

 $view['slots']->start('javascripts');
 foreach ($view['assetic']->javascripts([
        '@StudySauceBundle/Resources/public/js/checkin.js'
    ], [], ['output' => 'bundles/studysauce/js/*.js']) as $url):
    ?><script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('body'); ?>
<div class="panel-pane" id="checkin">
    <div class="pane-content">
        <h2>Check in. &nbsp;Listen to Mozart. &nbsp;Track your progress.</h2>

        <p class="classes">
            <?php
            $isDemo = false;
            if(empty($courses))
            {
                $courses = $demoCourses;
            }
            foreach($courses as $i => $c)
            {
                /** @var $c Course */
                ?><a href="#class<?php print $i; ?>" class="class<?php print $i; ?>" id="checkin-<?php print $c->getId(); ?>"><span><?php print $c->getName(); ?></span></a><?php
            }
            ?>
        </p>

        <div class="flip-counter clock flip-clock-wrapper">
            <h3>Take a 10 minute break in 1 hour</h3>
            <?php echo $view->render('StudySauceBundle:Checkin:digits.html.php'); ?>
            <input name="touchedMusic" type="hidden" value="0">
            <div class="player-ui">
                <div class="minplayer-default-big-play ui-state-default">
                    <a class="minplayer-default-play minplayer-default-button ui-state-default ui-corner-all" title="Toggle music on/off">
                        <span class="ui-icon ui-icon-play"></span>
                    </a>
                    <a class="minplayer-default-pause minplayer-default-button ui-state-default ui-corner-all" title="Toggle music on/off" style="display: none;">
                        <span class="ui-icon ui-icon-pause"></span>
                    </a>
                </div>
            </div>
            <h4 style="text-align:center;"><a href="#mozart-effect" data-toggle="modal">The Mozart Effect®</a></h4>
        </div>

        <div><a href="<?php print $view['router']->generate('schedule'); ?>"><span>Edit schedule</span></a></div>
    </div>
</div>
<?php $view['slots']->stop();

$view['slots']->start('sincludes');
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:sdsmessages'), ['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:checklist'), ['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:checkinempty'), ['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:timerexpire'), ['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:mozart'), ['strategy' => 'sinclude']);
$view['slots']->stop();

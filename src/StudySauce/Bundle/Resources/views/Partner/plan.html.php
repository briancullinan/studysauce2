<?php
use Doctrine\Common\Collections\ArrayCollection;
use StudySauce\Bundle\Entity\Course;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use StudySauce\Bundle\Entity\Event;

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');

foreach($jsonEvents as $i => $event) {
    $jsonEvents[$i]['editable'] = false;
}

foreach ($view['assetic']->stylesheets(['@StudySauceBundle/Resources/public/js/fullcalendar/fullcalendar.min.css'],[],['output' => 'bundles/studysauce/js/fullcalendar/*.css']) as $url):?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;

foreach ($view['assetic']->stylesheets(['@StudySauceBundle/Resources/public/css/plan.css'],[],['output' => 'bundles/studysauce/css/*.css']) as $url):?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts(['@plan_scripts'],[],['output' => 'bundles/studysauce/js/*.js']) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach; ?>
    <script type="text/javascript">
        // convert events array to object to keep track of keys better
        window.planEvents = <?php
        if($isDemo || $isEmpty) {
        print json_encode([]);
        }
        else {
        print (json_encode(array_values(array_map(function ($e) {
        $e['editable'] = false;
         $e['draggable'] = false;
          $e['resizable'] = false;
        return $e;}, $jsonEvents)), JSON_HEX_QUOT) ?: '[]');
        }
        ?>;
    </script>
<?php $view['slots']->stop();

$view['slots']->start('body'); ?>
    <div class="panel-pane <?php print ($isDemo ? ' demo' : ''); ?>" id="plan">
        <div class="pane-content">
            <?php echo $view->render('StudySauceBundle:Partner:partner-nav.html.php', ['user' => $user]); ?>
            <h2>Study schedule</h2>
            <?php if($isDemo) { ?>
                <h3>Your student has not filled out their schedule yet.</h3>
            <?php } else { ?>
                <div id="calendar" class="full-only fc fc-ltr fc-unthemed"></div>
            <?php } ?>
        </div>
    </div>
<?php $view['slots']->stop();

<?php
use StudySauce\Bundle\Entity\Course;
use StudySauce\Bundle\Entity\User;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use StudySauce\Bundle\Entity\Event;

/** @var array $courses */
/** @var User $user */
$user = $app->getUser();

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('includeNotes');
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Notes:index', ['_format' => 'tab']),['strategy' => 'sinclude']);
$view['slots']->stop();

$view['slots']->start('stylesheets');
foreach ($view['assetic']->stylesheets(['@StudySauceBundle/Resources/public/js/fullcalendar/fullcalendar.min.css'],[],['output' => 'bundles/studysauce/js/fullcalendar/*.css']) as $url):?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
foreach ($view['assetic']->stylesheets([
    '@StudySauceBundle/Resources/public/css/clock.css',
    '@StudySauceBundle/Resources/public/css/checkin.css'
], [], ['output' => 'bundles/studysauce/css/*.css']) as $url):
    ?><link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>" />
<?php endforeach;
foreach ($view['assetic']->stylesheets(['@StudySauceBundle/Resources/public/css/plan.css',],[],['output' => 'bundles/studysauce/css/*.css']) as $url):?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts'); ?>
<script type="text/javascript">
    // convert events array to object to keep track of keys better
    window.planEvents = <?php print (json_encode(array_values($jsonEvents), JSON_HEX_QUOT) ?: '[]'); ?>;
</script>
<?php foreach ($view['assetic']->javascripts(['@checkin_scripts',],[],['output' => 'bundles/studysauce/js/*.js']) as $url):?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
foreach ($view['assetic']->javascripts(['@plan_scripts',],[],['output' => 'bundles/studysauce/js/*.js']) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach; ?>
<?php $view['slots']->stop();

$view['slots']->start('body'); ?>
<div class="panel-pane <?php
    print ($isDemo ? ' demo' : '');
    print ($isEmpty ? ' empty-schedule' : '');
    print ($step !== false ? ' setup-mode' : '');
    print (!empty($user->getGcalAccessToken()) ? ' connected' : ' not-connected'); ?>" id="plan">
    <div class="pane-content">
        <div id="external-events">
            <h4>Draggable Events</h4>
            <div class="fc-event ui-draggable ui-draggable-handle event-type-sr class2"><div class="fc-title"><h4>SR</h4>ENG 102</div></div>
            <div class="fc-event ui-draggable ui-draggable-handle event-type-sr class2"><div class="fc-title"><h4>SR</h4>ENG 102</div></div>
            <div class="fc-event ui-draggable ui-draggable-handle event-type-sr class3"><div class="fc-title"><h4>SR</h4>MAT 101</div></div>
            <div class="fc-event ui-draggable ui-draggable-handle event-type-sr class3"><div class="fc-title"><h4>SR</h4>MAT 101</div></div>
            <div class="fc-event ui-draggable ui-draggable-handle event-type-sr class1"><div class="fc-title"><h4>SR</h4>BIO 103</div></div>
            <div class="highlighted-link">
                <a href="#save-plan" class="more">Save</a>
            </div>
        </div>
        <div id="calendar" class="full-only fc fc-ltr fc-unthemed"></div>
        <div class="session-strategy">
            <a href="#deselect" class="mobile-only">Back to calendar</a>
            <?php echo $view->render('StudySauceBundle:Checkin:mini-checkin.html.php'); ?>
            <h2 class="title"></h2>
            <h3 class="location"></h3>
            <h3 class="duration"></h3>
            <h3 class="strategy">Recommended strategy: </h3>
            <label class="input">
                <select name="strategy-select">
                    <option value="blank">Blank</option>
                    <option value="teach">Teach</option>
                    <option value="spaced">Spaced repetition</option>
                    <option value="active">Active reading</option>
                    <option value="prework">Prework</option>
                </select>
            </label>
            <div style="border-top:2px solid #555;padding-top:20px;margin-top:20px;">
                <a href="/notes" class="big-add">Create <span>+</span> new note</a>
            </div>
        </div>
        <a href="#plan-step-1" data-toggle="modal">Edit Study Plan Settings</a>
        <?php echo $view->render('StudySauceBundle:Plan:strategies.html.php'); ?>
    </div>
</div><?php
$view['slots']->stop();

$view['slots']->start('sincludes');
print $this->render('StudySauceBundle:Dialogs:plan-step-0.html.php', ['id' => 'plan-step-0', 'courses' => $courses, 'schedule' => $schedule, 'attributes' => 'data-backdrop="static" data-keyboard="false"']);
print $this->render('StudySauceBundle:Dialogs:plan-step-1.html.php', ['id' => 'plan-step-1', 'courses' => $courses, 'schedule' => $schedule, 'attributes' => 'data-backdrop="static" data-keyboard="false"']);
print $this->render('StudySauceBundle:Dialogs:plan-step-2.html.php', ['id' => 'plan-step-2', 'courses' => $courses, 'schedule' => $schedule, 'attributes' => 'data-backdrop="static" data-keyboard="false"']);
print $this->render('StudySauceBundle:Dialogs:plan-step-2-2.html.php', ['id' => 'plan-step-2-2', 'courses' => $courses, 'schedule' => $schedule, 'attributes' => 'data-backdrop="static" data-keyboard="false"']);
print $this->render('StudySauceBundle:Dialogs:plan-step-2-3.html.php', ['id' => 'plan-step-2-3', 'courses' => $courses, 'schedule' => $schedule, 'attributes' => 'data-backdrop="static" data-keyboard="false"']);
print $this->render('StudySauceBundle:Dialogs:plan-step-3.html.php', ['id' => 'plan-step-3', 'courses' => $courses, 'schedule' => $schedule, 'attributes' => 'data-backdrop="static" data-keyboard="false"']);
print $this->render('StudySauceBundle:Dialogs:plan-step-4.html.php', ['id' => 'plan-step-4', 'courses' => $courses, 'schedule' => $schedule, 'attributes' => 'data-backdrop="static" data-keyboard="false"']);
print $this->render('StudySauceBundle:Dialogs:plan-step-5.html.php', ['id' => 'plan-step-5', 'courses' => $courses, 'schedule' => $schedule, 'attributes' => 'data-backdrop="static" data-keyboard="false"']);
print $this->render('StudySauceBundle:Dialogs:plan-step-6.html.php', ['id' => 'plan-step-6', 'services' => $services, 'attributes' => 'data-backdrop="static" data-keyboard="false"']);
print $this->render('StudySauceBundle:Dialogs:plan-step-6-2.html.php', ['id' => 'plan-step-6-2']);
print $this->render('StudySauceBundle:Dialogs:plan-step-6-3.html.php', ['id' => 'plan-step-6-3']);

if($isEmpty) {
    print $this->render('StudySauceBundle:Dialogs:plan-empty-schedule.html.php', ['id' => 'plan-empty-schedule']);
}
else {
    echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:deferred', ['template' => 'plan-empty-schedule']));
}
if($isDemo) {
    print $this->render('StudySauceBundle:Dialogs:plan-upgrade.html.php', ['id' => 'plan-upgrade']);
}

print $this->render('StudySauceBundle:Dialogs:edit-event.html.php', ['id' => 'edit-event']);
print $this->render('StudySauceBundle:Dialogs:plan-science.html.php', ['id' => 'plan-science']);
print $this->render('StudySauceBundle:Dialogs:plan-drag.html.php', ['id' => 'plan-drag']);
if($step !== false) {
    print $this->render(
        'StudySauceBundle:Dialogs:plan-mobile.html.php',
        ['id' => 'plan-mobile', 'attributes' => 'data-backdrop="static" data-keyboard="false"']
    );
}
print $view['slots']->output('includeNotes');

echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:sdsMessages'), ['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:deferred', ['template' => 'checklist']), ['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:deferred', ['template' => 'timer-expire']), ['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:deferred', ['template' => 'bill-parents']), ['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:deferred', ['template' => 'bill-parents-confirm']), ['strategy' => 'sinclude']);
$view['slots']->stop();


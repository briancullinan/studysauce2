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
foreach ($view['assetic']->stylesheets(
    [
        '@StudySauceBundle/Resources/public/css/clock.css',
        '@StudySauceBundle/Resources/public/css/checkin.css',
        '@StudySauceBundle/Resources/public/css/plan.css'
    ],
    [],
    ['output' => 'bundles/studysauce/css/*.css']
) as $url):
    ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts'); ?>
<script type="text/javascript">
    CKEDITOR_BASEPATH = '<?php print $view['router']->generate('_welcome'); ?>bundles/admin/js/ckeditor/';
</script>
<?php foreach ($view['assetic']->javascripts(['@AdminBundle/Resources/public/js/ckeditor/ckeditor.js',],[],['output' => 'bundles/studysauce/js/*.js']) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
foreach ($view['assetic']->javascripts(['@StudySauceBundle/Resources/public/js/notes.js'],[],['output' => 'bundles/studysauce/js/*.js']) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
foreach ($view['assetic']->javascripts(['@checkin_scripts',],[],['output' => 'bundles/studysauce/js/*.js']) as $url):?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
foreach ($view['assetic']->javascripts(['@plan_scripts',],[],['output' => 'bundles/studysauce/js/*.js']) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
?>
<script type="text/javascript">
    // convert events array to object to keep track of keys better
    if(typeof(window.planEvents) == 'undefined')
        window.planEvents = [];
    window.planEvents = $.merge(window.planEvents, JSON.parse('<?php print json_encode(array_values($jsonEvents)); ?>'));
</script>
<?php $view['slots']->stop();

$view['slots']->start('body'); ?>
<div class="panel-pane <?php
    print ($isDemo ? ' demo' : '');
    print ($isEmpty ? ' empty-schedule' : '');
    print ($step !== false ? ' setup-mode' : '');
    print ($step === false ? ' session-selected' : ''); ?>" id="plan">
    <div class="pane-content">
        <h2>Personalized study plan for <?php print $user->getFirst(); ?></h2>
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
        <div id="calendar" class="full-only fc fc-ltr fc-unthemed">
            <?php echo $this->render('StudySauceBundle:Dialogs:plan-empty.html.php', ['id' => 'plan-empty', 'attributes' => 'data-backdrop="false"']) ?>
        </div>
        <?php echo $view->render('StudySauceBundle:Checkin:mini-checkin.html.php'); ?>
        <div class="session-strategy">
            <h3 style="margin-top:40px;">Recommended strategy</h3>
            <label class="input">
                <select name="strategy-select">
                    <option value="teach">Teach</option>
                    <option value="spaced">Spaced repetition</option>
                    <option value="active">Active reading</option>
                    <option value="prework">Prework</option>
                </select>
            </label>
            <div style="padding: 40px 0;">
                <a href="#add-note" class="big-add">Create <span>+</span> new note</a>
                <div class="notes-or"><span>Or</span></div>
                <h3 style="display:inline-block;">Open an existing note</h3>
            </div>
        </div>
        <a href="#plan-step-1" data-toggle="modal">Edit Study Plan Settings</a>
        <div id="editor2" contenteditable="true">This is note content</div>
        <div class="highlighted-link"><a href="#save-note" class="more">Save</a></div>
        <?php echo $view->render('StudySauceBundle:Plan:strategies.html.php'); ?>
    </div>
</div><?php
$view['slots']->stop();

$view['slots']->start('sincludes');
if($step === false)
    $step = 0;
print $this->render('StudySauceBundle:Dialogs:plan-step-' . $step . '.html.php', ['id' => 'plan-step-' . $step, 'courses' => $courses, 'schedule' => $schedule, 'attributes' => 'data-backdrop="static" data-keyboard="false"']);
if($step < 6) {
    print $this->render('StudySauceBundle:Dialogs:plan-step-' . ($step + 1) . '.html.php', ['id' => 'plan-step-' . ($step + 1), 'courses' => $courses, 'schedule' => $schedule, 'attributes' => 'data-backdrop="static" data-keyboard="false"']);
}
$steps = array_merge($step > 0 ? range(0, $step - 1) : [], $step < 5 ? range($step+2, 6) : []);
foreach($steps as $i) {
    print $this->render('StudySauceBundle:Dialogs:plan-step-' . $i . '.html.php', ['id' => 'plan-step-' . $i, 'courses' => $courses, 'schedule' => $schedule, 'attributes' => 'data-backdrop="static" data-keyboard="false"']);
}
print $this->render('StudySauceBundle:Dialogs:plan-step-2-2.html.php', ['id' => 'plan-step-2-2', 'courses' => $courses, 'schedule' => $schedule, 'attributes' => 'data-backdrop="static" data-keyboard="false"']);
print $this->render('StudySauceBundle:Dialogs:plan-step-2-3.html.php', ['id' => 'plan-step-2-3', 'courses' => $courses, 'schedule' => $schedule, 'attributes' => 'data-backdrop="static" data-keyboard="false"']);
if($isEmpty) {
    echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:deferred', ['template' => 'plan-empty-schedule']));
}
else if($isDemo) {
    echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:deferred', ['template' => 'plan-upgrade']));
}

print $this->render('StudySauceBundle:Dialogs:edit-event.html.php', ['id' => 'edit-event']);
print $view['slots']->output('includeNotes');

if($showPlanIntro) {
    echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:deferred', ['template' => 'plan-intro-1']));
    echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:deferred', ['template' => 'plan-intro-2']),['strategy' => 'sinclude']);
    echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:deferred', ['template' => 'plan-intro-3']),['strategy' => 'sinclude']);
    echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:deferred', ['template' => 'plan-intro-4']),['strategy' => 'sinclude']);
}
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:sdsMessages'), ['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:deferred', ['template' => 'checklist']), ['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:deferred', ['template' => 'timer-expire']), ['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:deferred', ['template' => 'bill-parents']), ['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:deferred', ['template' => 'bill-parents-confirm']), ['strategy' => 'sinclude']);
$view['slots']->stop();


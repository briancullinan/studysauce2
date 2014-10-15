
<?php use Symfony\Component\HttpKernel\Controller\ControllerReference;

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

 $view['slots']->start('stylesheets');

 foreach ($view['assetic']->stylesheets([
        '@StudySauceBundle/Resources/public/js/fullcalendar/fullcalendar.css'
    ], [], ['output' => 'bundles/studysauce/js/fullcalendar/*.css']) as $url):
    ?><link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>" />
<?php endforeach;

 foreach ($view['assetic']->stylesheets([
        '@StudySauceBundle/Resources/public/css/plan.css'
    ], [], ['output' => 'bundles/studysauce/css/*.css']) as $url):
    ?><link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>" />
<?php endforeach;
$view['slots']->stop();

 $view['slots']->start('javascripts');
 foreach ($view['assetic']->javascripts([
        '@StudySauceBundle/Resources/public/js/plan.js',
        '@StudySauceBundle/Resources/public/js/fullcalendar/lib/moment.min.js',
        '@StudySauceBundle/Resources/public/js/fullcalendar/fullcalendar.js'
    ], [], ['output' => 'bundles/studysauce/js/*.js']) as $url):
    ?><script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
 $view['slots']->stop();

 $view['slots']->start('body'); ?>

<div class="panel-pane" id="plan">

    <div class="pane-content">

        <h2>Personalized study plan for <?php ; ?></h2>

        <div id="calendar" class="full-only fc fc-ltr fc-unthemed"></div>
        <div class="sort-by clearfix">
            <label>Sort by: </label>
            <label class="radio"><input type="radio" name="plan-sort" value="date"
                                        checked="checked"/><i></i>Date</label>
            <label class="radio"><input type="radio" name="plan-sort" value="class"><i></i>Class</label>
            <a href="#expand">Expand</a>
            <label class="checkbox" title="Click here to see sessions that have already passed.">
                <input type="checkbox"><i></i>Past session</label>
        </div>
        <div class="head ">18 August</div>
        <div class="session-row  event-type-o  class cid default-other " id="eid-645283">
            <div class="class-name">
                <span class="class">&nbsp;</span>

                <div class="read-only">Work</div>
            </div>
            <div class="field-type-text field-name-field-assignment field-widget-text-textfield ">
                <div class="read-only">Work</div>
            </div>
            <div class="field-type-number-integer field-name-field-percent field-widget-number ">
                <div class="read-only"></div>
            </div>
            <div class="completed">
                <label class="checkbox"><input type="checkbox" name="plan-sort" value="class"><i></i></label>
            </div>
        </div>

        <a class="return-to-top" href="#return-to-top">Top</a>

    </div>

</div>

<?php echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:planintro1'));

 $view['slots']->stop();

$view['slots']->start('sincludes');
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:planintro2'), ['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:planintro3'), ['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:planintro4'), ['strategy' => 'sinclude']);
$view['slots']->stop();


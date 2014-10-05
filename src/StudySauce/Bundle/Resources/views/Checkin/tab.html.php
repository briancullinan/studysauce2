
<?php use Symfony\Component\HttpKernel\Controller\ControllerReference;

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

        <p class="classes"><a href="#class0" class="class0 cid25677"><span>&nbsp;</span>MAT 100</a></p>

        <div class="flip-counter clock flip-clock-wrapper">
            <h3>Take a 10 minute break in 1 hour</h3>
            <ul class="flip">
                <li data-digit="0">0</li>
                <li data-digit="1">1</li>
                <li data-digit="2">2</li>
                <li data-digit="3">3</li>
                <li data-digit="4">4</li>
                <li data-digit="5">5</li>
                <li data-digit="6">6</li>
                <li data-digit="7">7</li>
                <li data-digit="8">8</li>
                <li data-digit="9">9</li>
            </ul>
            <ul class="flip">
                <li data-digit="0">0</li>
                <li data-digit="1">1</li>
                <li data-digit="2">2</li>
                <li data-digit="3">3</li>
                <li data-digit="4">4</li>
                <li data-digit="5">5</li>
                <li data-digit="6">6</li>
                <li data-digit="7">7</li>
                <li data-digit="8">8</li>
                <li data-digit="9">9</li>
            </ul>
            <span class="flip-clock-divider minutes"><span class="flip-clock-dot top"></span><span class="flip-clock-dot bottom"></span></span>
            <ul class="flip">
                <li data-digit="0">0</li>
                <li data-digit="1">1</li>
                <li data-digit="2">2</li>
                <li data-digit="3">3</li>
                <li data-digit="4">4</li>
                <li data-digit="5">5</li>
                <li data-digit="6">6</li>
                <li data-digit="7">7</li>
                <li data-digit="8">8</li>
                <li data-digit="9">9</li>
            </ul>
            <ul class="flip">
                <li data-digit="0">0</li>
                <li data-digit="1">1</li>
                <li data-digit="2">2</li>
                <li data-digit="3">3</li>
                <li data-digit="4">4</li>
                <li data-digit="5">5</li>
                <li data-digit="6">6</li>
                <li data-digit="7">7</li>
                <li data-digit="8">8</li>
                <li data-digit="9">9</li>
            </ul>
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
            <h4 style="text-align:center;"><a href="#mozart-effect">The Mozart Effect®</a></h4>
        </div>


        <div><a href="#schedule"><span>Edit schedule</span></a></div>
    </div>

</div>

<?php $view['slots']->stop();

$view['slots']->start('sincludes');
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:checkinempty'), ['strategy' => 'sinclude']);
$view['slots']->stop();

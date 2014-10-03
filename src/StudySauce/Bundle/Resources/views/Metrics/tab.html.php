
<?php use Symfony\Component\HttpKernel\Controller\ControllerReference;

$view->extend('StudySauceBundle:Shared:dashboard.html.php') ?>

<?php $view['slots']->start('stylesheets'); ?>
<?php foreach ($view['assetic']->stylesheets([
        '@StudySauceBundle/Resources/public/css/metrics.css'
    ], [], ['output' => 'bundles/studysauce/css/*.css']) as $url):
    ?><link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>" />
<?php endforeach; ?>
<?php $view['slots']->stop() ?>

<?php $view['slots']->start('javascripts'); ?>
<?php foreach ($view['assetic']->javascripts([
        '@StudySauceBundle/Resources/public/js/metrics.js'
    ], [], ['output' => 'bundles/studysauce/js/*.js']) as $url):
    ?><script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach; ?>
<?php $view['slots']->stop() ?>

<?php $view['slots']->start('body'); ?>

<div class="panel-pane" id="metrics">

<div class="pane-content">

<h2>Study metrics</h2>

<div class="centrify">
    <div id="legend">
        <ol>
            <li><span class="class0">&nbsp;</span>MAT 100</li>
        </ol>
    </div>
    <div id="timeline">
        <h3>Study hours by week</h3>
        <h4 style="margin:5px 0; color:#555;">Goal: 25 hours</h4>
        <svg width="539" height="404.25">
            <g transform="translate(0,30)">
                <g class="symbol">
                    <rect x="161.70000000000002" y="322.2068367989918" width="30" height="2.0431632010082126"
                          style="fill: rgb(255, 17, 0); fill-opacity: 1;"></rect>
                    <rect x="161.70000000000002" y="268.7781190926276" width="30" height="53.42871770636418"
                          style="fill: rgb(255, 17, 0); fill-opacity: 1;"></rect>
                    <rect x="161.70000000000002" y="259.61793740810754" width="30" height="9.160181684520069"
                          style="fill: rgb(255, 17, 0); fill-opacity: 1;"></rect>
                    <rect x="161.70000000000002" y="256.0083490863264" width="30" height="3.609588321781132"
                          style="fill: rgb(255, 17, 0); fill-opacity: 1;"></rect>
                    <rect x="161.70000000000002" y="246.2692711615207" width="30" height="9.739077924805713"
                          style="fill: rgb(255, 17, 0); fill-opacity: 1;"></rect>
                    <rect x="161.70000000000002" y="241.29757403906746" width="30" height="4.971697122453293"
                          style="fill: rgb(255, 17, 0); fill-opacity: 1;"></rect>
                    <rect x="161.70000000000002" y="225.9397973114892" width="30" height="15.357776727578255"
                          style="fill: rgb(255, 17, 0); fill-opacity: 1;"></rect>
                    <rect x="161.70000000000002" y="180.13888888888889" width="30" height="45.80090842260029"
                          style="fill: rgb(255, 17, 0); fill-opacity: 1;"></rect>
                    <rect x="269.5" y="245.1114786809494" width="30" height="79.1385213190506"
                          style="fill: rgb(255, 17, 0); fill-opacity: 1;"></rect>
                    <rect x="269.5" y="240.1397815584961" width="30" height="4.971697122453293"
                          style="fill: rgb(255, 17, 0); fill-opacity: 1;"></rect>
                    <rect x="269.5" y="238.09661835748796" width="30" height="2.0431632010082126"
                          style="fill: rgb(255, 17, 0); fill-opacity: 1;"></rect>
                    <rect x="269.5" y="122.5897920604915" width="30" height="115.50682629699642"
                          style="fill: rgb(255, 17, 0); fill-opacity: 1;"></rect>
                    <rect x="269.5" y="91.36344780508296" width="30" height="31.22634425540855"
                          style="fill: rgb(255, 17, 0); fill-opacity: 1;"></rect>
                    <rect x="269.5" y="5.684341886080802e-14" width="30" height="91.36344780508296"
                          original-title="" style="fill: rgb(255, 17, 0); fill-opacity: 1;"></rect>
                </g>
                <g class="x axis" transform="translate(0,324.25)">
                    <g class="tick" transform="translate(53.900000000000006,0)" style="opacity: 1;">
                        <line y2="6" x2="0"></line>
                        <text y="9" x="0" dy=".71em" style="text-anchor: middle;">8/31 -</text>
                    </g>
                    <g class="tick" transform="translate(161.70000000000002,0)" style="opacity: 1;">
                        <line y2="6" x2="0"></line>
                        <text y="9" x="0" dy=".71em" style="text-anchor: middle;">9/7 -</text>
                    </g>
                    <g class="tick" transform="translate(269.5,0)" style="opacity: 1;">
                        <line y2="6" x2="0"></line>
                        <text y="9" x="0" dy=".71em" style="text-anchor: middle;">9/14 -</text>
                    </g>
                    <g class="tick" transform="translate(377.3,0)" style="opacity: 1;">
                        <line y2="6" x2="0"></line>
                        <text y="9" x="0" dy=".71em" style="text-anchor: middle;">9/21 -</text>
                    </g>
                    <g class="tick" transform="translate(485.1,0)" style="opacity: 1;">
                        <line y2="6" x2="0"></line>
                        <text y="9" x="0" dy=".71em" style="text-anchor: middle;">9/28 -</text>
                    </g>
                    <path class="domain" d="M0,6V0H539V6"></path>
                </g>
                <g class="x axis2" transform="translate(0,344.25)">
                    <g class="tick" transform="translate(53.900000000000006,0)" style="opacity: 1;">
                        <line y2="6" x2="0"></line>
                        <text y="9" x="0" dy=".71em" style="text-anchor: middle;">9/6</text>
                    </g>
                    <g class="tick" transform="translate(161.70000000000002,0)" style="opacity: 1;">
                        <line y2="6" x2="0"></line>
                        <text y="9" x="0" dy=".71em" style="text-anchor: middle;">9/13</text>
                    </g>
                    <g class="tick" transform="translate(269.5,0)" style="opacity: 1;">
                        <line y2="6" x2="0"></line>
                        <text y="9" x="0" dy=".71em" style="text-anchor: middle;">9/20</text>
                    </g>
                    <g class="tick" transform="translate(377.3,0)" style="opacity: 1;">
                        <line y2="6" x2="0"></line>
                        <text y="9" x="0" dy=".71em" style="text-anchor: middle;">9/27</text>
                    </g>
                    <g class="tick" transform="translate(485.1,0)" style="opacity: 1;">
                        <line y2="6" x2="0"></line>
                        <text y="9" x="0" dy=".71em" style="text-anchor: middle;">10/4</text>
                    </g>
                </g>
                <g class="x axisT" transform="translate(0,-30)">
                    <g class="tick" transform="translate(53.900000000000006,0)" style="opacity: 1;">
                        <line y2="6" x2="0"></line>
                        <text y="9" x="0" dy=".71em" transform="translate(15,324.25)"
                              style="text-anchor: middle;"></text>
                    </g>
                    <g class="tick" transform="translate(161.70000000000002,0)" style="opacity: 1;">
                        <line y2="6" x2="0"></line>
                        <text y="9" x="0" dy=".71em" transform="translate(15,180.13888888888889)"
                              style="text-anchor: middle;">1.2
                        </text>
                    </g>
                    <g class="tick" transform="translate(269.5,0)" style="opacity: 1;">
                        <line y2="6" x2="0"></line>
                        <text y="9" x="0" dy=".71em" transform="translate(15,5.684341886080802e-14)"
                              style="text-anchor: middle;">2.6
                        </text>
                    </g>
                    <g class="tick" transform="translate(377.3,0)" style="opacity: 1;">
                        <line y2="6" x2="0"></line>
                        <text y="9" x="0" dy=".71em" transform="translate(15,324.25)"
                              style="text-anchor: middle;"></text>
                    </g>
                    <g class="tick" transform="translate(485.1,0)" style="opacity: 1;">
                        <line y2="6" x2="0"></line>
                        <text y="9" x="0" dy=".71em" transform="translate(15,324.25)"
                              style="text-anchor: middle;"></text>
                    </g>
                </g>
            </g>
        </svg>
    </div>
    <div id="pie-chart">
        <h3>Study hours by class</h3>
        <h4 style="margin:5px 0; color:#555;">Total study hours: <strong id="study-total">3.8 hours</strong>
        </h4>
        <svg width="260" height="195">
            <g>
                <g class="symbol">
                    <path transform="translate(130,97.5)"
                          d="M0,97.5A97.5,97.5 0 1,1 0,-97.5A97.5,97.5 0 1,1 0,97.5M0,48.75A48.75,48.75 0 1,0 0,-48.75A48.75,48.75 0 1,0 0,48.75Z"
                          style="fill: rgb(255, 17, 0);"></path>
                </g>
            </g>
        </svg>
    </div>
</div>
<hr>
<script type="text/javascript">
    window.initialHistory = [{
        "time": 1410480325,
        "length": 60,
        "length0": 0,
        "class": "MAT 100"
    }, {"time": 1410562653, "length": 1569, "length0": 60, "class": "MAT 100"}, {
        "time": 1410564222,
        "length": 269,
        "length0": 1629,
        "class": "MAT 100"
    }, {"time": 1410564701, "length": 106, "length0": 1898, "class": "MAT 100"}, {
        "time": 1410564813,
        "length": 286,
        "length0": 2004,
        "class": "MAT 100"
    }, {"time": 1410565115, "length": 146, "length0": 2290, "class": "MAT 100"}, {
        "time": 1410565802,
        "length": 451,
        "length0": 2436,
        "class": "MAT 100"
    }, {"time": 1410578436, "length": 1345, "length0": 2887, "class": "MAT 100"}, {
        "time": 1410807649,
        "length": 2324,
        "length0": 0,
        "class": "MAT 100"
    }, {"time": 1410997730, "length": 146, "length0": 2324, "class": "MAT 100"}, {
        "time": 1410997896,
        "length": 60,
        "length0": 2470,
        "class": "MAT 100"
    }, {"time": 1411059555, "length": 3392, "length0": 2530, "class": "MAT 100"}, {
        "time": 1411086022,
        "length": 917,
        "length0": 5922,
        "class": "MAT 100"
    }, {"time": 1411086939, "length": 2683, "length0": 6839, "class": "MAT 100"}];
    window.classNames = ["MAT 100"];
</script>
<div id="checkins-list">
    <div class="heading row">
        <label class="class-name">Class</label>
        <label class="class-date"><span class="full-only">Check in date</span><span
                class="mobile-only">Date</span></label>
        <label class="class-time">Duration</label>
    </div>

    <div class="row">
        <div class="class-name"><span class="class0">&nbsp;</span>MAT 100</div>
        <div class="class-date"><span class="full-only">18 September</span><span
                class="mobile-only">18 Sep</span></div>
        <div class="class-time"><span class="full-only">45 minutes</span><span class="mobile-only">45 min</span>
        </div>
    </div>

    <div class="row">
        <div class="class-name"><span class="class0">&nbsp;</span>MAT 100</div>
        <div class="class-date"><span class="full-only">18 September</span><span
                class="mobile-only">18 Sep</span></div>
        <div class="class-time"><span class="full-only">15 minutes</span><span class="mobile-only">15 min</span>
        </div>
    </div>

    <div class="row">
        <div class="class-name"><span class="class0">&nbsp;</span>MAT 100</div>
        <div class="class-date"><span class="full-only">18 September</span><span
                class="mobile-only">18 Sep</span></div>
        <div class="class-time"><span class="full-only">57 minutes</span><span class="mobile-only">57 min</span>
        </div>
    </div>

    <div class="row">
        <div class="class-name"><span class="class0">&nbsp;</span>MAT 100</div>
        <div class="class-date"><span class="full-only">17 September</span><span
                class="mobile-only">17 Sep</span></div>
        <div class="class-time"><span class="full-only">1 minute</span><span class="mobile-only">1 min</span>
        </div>
    </div>

    <div class="row">
        <div class="class-name"><span class="class0">&nbsp;</span>MAT 100</div>
        <div class="class-date"><span class="full-only">17 September</span><span
                class="mobile-only">17 Sep</span></div>
        <div class="class-time"><span class="full-only">2 minutes</span><span class="mobile-only">2 min</span>
        </div>
    </div>

    <div class="row">
        <div class="class-name"><span class="class0">&nbsp;</span>MAT 100</div>
        <div class="class-date"><span class="full-only">15 September</span><span
                class="mobile-only">15 Sep</span></div>
        <div class="class-time"><span class="full-only">39 minutes</span><span class="mobile-only">39 min</span>
        </div>
    </div>

    <div class="row">
        <div class="class-name"><span class="class0">&nbsp;</span>MAT 100</div>
        <div class="class-date"><span class="full-only">12 September</span><span
                class="mobile-only">12 Sep</span></div>
        <div class="class-time"><span class="full-only">22 minutes</span><span class="mobile-only">22 min</span>
        </div>
    </div>

    <div class="row">
        <div class="class-name"><span class="class0">&nbsp;</span>MAT 100</div>
        <div class="class-date"><span class="full-only">12 September</span><span
                class="mobile-only">12 Sep</span></div>
        <div class="class-time"><span class="full-only">8 minutes</span><span class="mobile-only">8 min</span>
        </div>
    </div>

    <div class="row">
        <div class="class-name"><span class="class0">&nbsp;</span>MAT 100</div>
        <div class="class-date"><span class="full-only">12 September</span><span
                class="mobile-only">12 Sep</span></div>
        <div class="class-time"><span class="full-only">2 minutes</span><span class="mobile-only">2 min</span>
        </div>
    </div>

    <div class="row">
        <div class="class-name"><span class="class0">&nbsp;</span>MAT 100</div>
        <div class="class-date"><span class="full-only">12 September</span><span
                class="mobile-only">12 Sep</span></div>
        <div class="class-time"><span class="full-only">5 minutes</span><span class="mobile-only">5 min</span>
        </div>
    </div>

    <div class="row">
        <div class="class-name"><span class="class0">&nbsp;</span>MAT 100</div>
        <div class="class-date"><span class="full-only">12 September</span><span
                class="mobile-only">12 Sep</span></div>
        <div class="class-time"><span class="full-only">2 minutes</span><span class="mobile-only">2 min</span>
        </div>
    </div>

    <div class="row">
        <div class="class-name"><span class="class0">&nbsp;</span>MAT 100</div>
        <div class="class-date"><span class="full-only">12 September</span><span
                class="mobile-only">12 Sep</span></div>
        <div class="class-time"><span class="full-only">4 minutes</span><span class="mobile-only">4 min</span>
        </div>
    </div>

    <div class="row">
        <div class="class-name"><span class="class0">&nbsp;</span>MAT 100</div>
        <div class="class-date"><span class="full-only">12 September</span><span
                class="mobile-only">12 Sep</span></div>
        <div class="class-time"><span class="full-only">26 minutes</span><span class="mobile-only">26 min</span>
        </div>
    </div>

    <div class="row">
        <div class="class-name"><span class="class0">&nbsp;</span>MAT 100</div>
        <div class="class-date"><span class="full-only">11 September</span><span
                class="mobile-only">11 Sep</span></div>
        <div class="class-time"><span class="full-only">1 minute</span><span class="mobile-only">1 min</span>
        </div>
    </div>
</div>

</div>

</div>

<?php echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:metricsempty'), ['strategy' => 'sinclude']); ?>

<?php $view['slots']->stop(); ?>

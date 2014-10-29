<?php
use StudySauce\Bundle\Entity\Checkin;
use StudySauce\Bundle\Entity\Course;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

$shortTimeIntervals = [
    'years' => 'yr',
    'year' => 'yr',
    'months' => 'mo',
    'month' => 'mo',
    'days' => 'day',
    'day' => 'day',
    'hours' => 'hr',
    'hour' => 'hrs',
    'minutes' => 'min',
    'minute' => 'min',
    'seconds' => 'sec',
    'second' => 'sec'
];

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');
foreach ($view['assetic']->stylesheets([
        '@StudySauceBundle/Resources/public/css/tipsy.css',
        '@StudySauceBundle/Resources/public/css/metrics.css'
    ], [], ['output' => 'bundles/studysauce/css/*.css']
) as $url): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts([
        '@StudySauceBundle/Resources/public/js/d3.v3.min.js',
        '@StudySauceBundle/Resources/public/js/jquery.tipsy.js',
        '@StudySauceBundle/Resources/public/js/metrics.js'
    ], [], ['output' => 'bundles/studysauce/js/*.js']
) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach; ?>
<script type="text/javascript">
    window.initialHistory = JSON.parse('<?php print json_encode($times); ?>');
    window.classIds = JSON.parse('<?php print json_encode(array_map(function (Course $c) {return $c->getId();}, $courses)); ?>');
</script>
<?php $view['slots']->stop();

$view['slots']->start('body'); ?>

<div class="panel-pane" id="metrics">

    <div class="pane-content">

        <h2>Study metrics</h2>

        <div class="centrify">
            <div id="legend">
                <ol>
                    <?php
                    foreach ($courses as $i => $c) {
                        /** @var $c Course */
                        ?>
                        <li><span class="class<?php print $i; ?>">&nbsp;</span><?php print $c->getName(); ?></li><?php
                    }
                    ?>
                </ol>
            </div>
            <div id="timeline">
                <h3>Study hours by week</h3>
                <h4>Goal: <?php print $hours; ?> hour<?php print ($hours <> 1 ? 's' : ''); ?></h4>
            </div>
            <div id="pie-chart">
                <h3>Study hours by class</h3>
                <h4>Total study hours: <strong id="study-total"><?php print $total; ?>
                        hour<?php print ($total <> 1 ? 's' : ''); ?></strong></h4>
            </div>
        </div>
        <hr>
        <div id="checkins-list">
            <?php
            reset($checkouts);
            foreach ($checkins as $t => $c) {
                list($k) = each($checkouts);
                $length = $k - $t;
                if ($length <= 60) {
                    $length = 60;
                }

                $elapsedString = function ($etime) {
                    if ($etime < 1) {
                        return '0 seconds';
                    }

                    $a = [
                        12 * 30 * 24 * 60 * 60 => 'year',
                        30 * 24 * 60 * 60 => 'month',
                        24 * 60 * 60 => 'day',
                        60 * 60 => 'hour',
                        60 => 'minute',
                        1 => 'second'
                    ];

                    foreach ($a as $secs => $str) {
                        $d = ($etime * 1.0) / $secs;
                        if (round($d, 1) >= 1) {
                            $r = round($d);

                            return $r . ' ' . $str . ($r > 1 ? 's' : '');
                        }
                    }

                    return '';
                };

                $lengthStr = $elapsedString($length);

                /** @var $c Checkin */
                ?>
                <div class="checkin-row">
                    <div class="class-name"><span class="class<?php print array_search($c->getCourse(), $courses); ?>">&nbsp;</span>
                        <?php print $c->getCourse()->getName(); ?></div>
                    <div class="class-date"><span class="full-only"><?php print $c->getCheckin()->format('j F'); ?></span>
                        <span class="mobile-only"><?php print $c->getCheckin()->format('j M'); ?></span>
                    </div>
                    <div class="class-time"><span class="full-only"><?php print $lengthStr; ?></span>
                        <span class="mobile-only"><?php print str_replace(array_keys($shortTimeIntervals),
                                array_values($shortTimeIntervals),
                                $lengthStr); ?></span></div>
                </div>
            <?php
            }
            ?>
        </div>
    </div>
</div>
<?php $view['slots']->stop();

$view['slots']->start('sincludes');
echo $view['actions']->render(
    new ControllerReference('StudySauceBundle:Dialogs:metricsEmpty'), empty($times)
        ? []
        : ['strategy' => 'sinclude']
);
$view['slots']->stop();

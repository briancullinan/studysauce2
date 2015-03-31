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
foreach ($view['assetic']->stylesheets(['@StudySauceBundle/Resources/public/css/metrics.css'], [], ['output' => 'bundles/studysauce/css/*.css']) as $url): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts(['@metrics_scripts'], [], ['output' => 'bundles/studysauce/js/*.js']) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
foreach ($view['assetic']->javascripts(['@StudySauceBundle/Resources/public/js/metrics-add.js'], [], ['output' => 'bundles/studysauce/js/*.js']) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach; ?>
<script type="text/javascript">
    window.initialHistory = JSON.parse('<?php print json_encode($times); ?>');
    window.classIds = JSON.parse('<?php print json_encode(array_map(function (Course $c) {return $c->getId();}, $courses)); ?>');
</script>
<?php $view['slots']->stop();

$view['slots']->start('body'); ?>
<div class="panel-pane <?php print ($isDemo ? ' demo' : ''); ?>" id="metrics">
    <div class="pane-content">
        <h2>Study metrics</h2>
        <div class="centrify">
            <div id="legend">
                <ol>
                    <?php
                    foreach ($courses as $i => $c) {
                        /** @var $c Course */
                        ?>
                        <li class="course-id-<?php print $c->getId(); ?>"><span class="class<?php print $i; ?>">&nbsp;</span><?php print $c->getName(); ?></li>
                    <?php } ?>
                </ol>
            </div>
            <div id="timeline">
                <h3><span>Study hours by week<span></h3>
                <h4><span>Goal: <?php print $hours; ?> hour<?php print ($hours <> 1 ? 's' : ''); ?><span></h4>
            </div>
            <div id="pie-chart">
                <h3><span>Study hours by class<span></h3>
                <h4><span>Total study hours: <strong id="study-total"><?php print $total; ?></strong><span></h4>
            </div>
        </div>
        <div class="clearfix">
            <a href="#add-study-hours" class="big-add" data-toggle="modal">Add <span>+</span> study hours manually</a>
        </div>
        <hr>
        <div id="checkins-list">
            <?php
            reset($checkouts);
            foreach ($checkins as $t => $c) {
                list($k, $l) = each($checkouts);

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

                $lengthStr = $elapsedString($l);

                /** @var $c Checkin */
                ?>
                <div class="checkin-row <?php print ($c->getUtcCheckin() == $c->getUtcCheckout() ? 'manually-entered' : ''); ?>">
                    <div class="class-name" <?php print ($c->getUtcCheckin() == $c->getUtcCheckout() ? 'title="Manually entered study session"' : ''); ?>><span class="class<?php print array_search($c->getCourse(), $courses); ?>">&nbsp;</span>
                        <?php print $c->getCourse()->getName(); ?></div>
                    <div class="class-date"><span class="full-only"><?php print $c->getCheckin()->format('j F'); ?></span>
                        <span class="mobile-only"><?php print $c->getCheckin()->format('j M'); ?></span>
                    </div>
                    <div class="class-time"><span class="full-only"><?php print $lengthStr; ?></span>
                        <span class="mobile-only"><?php print str_replace(array_keys($shortTimeIntervals),
                                array_values($shortTimeIntervals),
                                $lengthStr); ?></span></div>
                </div>
            <?php } ?>
        </div>
        <div class="manually-entered">Manually entered study session</div>
    </div>
</div>
<?php $view['slots']->stop();

$view['slots']->start('sincludes');
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:metricsEmpty'), $isDemo ? []: ['strategy' => 'sinclude']);
print $this->render('StudySauceBundle:Dialogs:add-study-hours.html.php', ['id' => 'add-study-hours']);
$view['slots']->stop();

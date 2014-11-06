<?php
use StudySauce\Bundle\Entity\Course;
?>
<div class="widget-wrapper">
    <div class="widget metrics-widget">
        <h3>My progress</h3>
        <?php if(empty($courses) || empty($times)) { ?>
            <a href="<?php print $view['router']->generate('checkin'); ?>" class="cloak">Nothing set up yet.  Click <span class="reveal">here</span> to check in and start tracking your progress.</a>
            <script type="text/javascript">
                window.initialHistory = [];
                window.classIds = JSON.parse('<?php print json_encode(array_map(function (Course $c) {return $c->getId();}, $courses)); ?>');
            </script>
        <?php } ?>
        <div class="centrify">
            <div class="timeline">
                <h3><span>Study hours by week<span></h3>
                <h4><span>Goal: <?php print $hours; ?> hour<?php print ($hours <> 1 ? 's' : ''); ?><span></h4>
            </div>
            <div class="pie-chart">
                <h3><span>Study hours by class<span></h3>
                <h4><span>Total study hours: <strong id="study-total"><?php print $total; ?></strong><span></h4>
            </div>
        </div>
        <script type="text/javascript">
            window.initialHistory = JSON.parse('<?php print json_encode($times); ?>');
            window.classIds = JSON.parse('<?php print json_encode(array_map(function (Course $c) {return $c->getId();}, $courses)); ?>');
        </script>
    </div>
</div>


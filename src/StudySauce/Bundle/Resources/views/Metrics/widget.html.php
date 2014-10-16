<?php
use StudySauce\Bundle\Entity\Course;
?>
<div class="widget-wrapper">
    <div class="widget metrics-widget">
        <h3>My progress</h3>
        <div class="timeline"></div>
        <div class="pie-chart"></div>
        <script type="text/javascript">
            window.initialHistory = JSON.parse('<?php print json_encode($times); ?>');
            window.classNames = JSON.parse('<?php print json_encode(array_map(function (Course $c) {return $c->getName();}, $courses)); ?>');
        </script>

    </div>
</div>


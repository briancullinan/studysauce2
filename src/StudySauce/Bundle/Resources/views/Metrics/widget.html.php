<?php
use StudySauce\Bundle\Entity\Course;
?>
<div class="widget-wrapper">
    <div class="widget metrics-widget">
        <h3>My progress</h3>
        <div class="centrify">
            <div class="timeline"></div>
            <div class="pie-chart"></div>
        </div>
        <script type="text/javascript">
            window.initialHistory = JSON.parse('<?php print json_encode($times); ?>');
            window.classIds = JSON.parse('<?php print json_encode(array_map(function (Course $c) {return $c->getId();}, $courses)); ?>');
        </script>
    </div>
</div>


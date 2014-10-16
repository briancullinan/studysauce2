<?php
use StudySauce\Bundle\Entity\Course;
use StudySauce\Bundle\Entity\Deadline;

?>
<div class="widget-wrapper">
    <div class="widget deadlines-widget">
        <h3>Upcoming deadlines</h3>
        <?php foreach ($deadlines as $i => $d) {
            /** @var $d Deadline */
            $classI = array_search($d->getName(), array_map(function (Course $c) {return $c->getName(); }, $courses));
            ?>
            <div class="deadline-row">
                <i class="class<?php print $classI; ?>">&nbsp;</i>
                <strong><?php print $d->getDueDate()->format('j F'); ?></strong>
                <div><?php print $d->getAssignment(); ?></div>
            </div>
        <?php } ?>
    </div>
</div>


<?php
use StudySauce\Bundle\Entity\Event;
?>
<div class="widget-wrapper">
    <div class="widget plan-widget">
        <h3>Today's study plan</h3>

        <?php
        foreach($events as $i => $event)
        {
            /** @var Event $event */
            if($event->getType() == 'm' || $event->getType() == 'r' || $event->getType() == 'z')
                continue;

            $classI = array_search($event->getName(), $classes);
            if ($classI === false)
                $classI = '';
            if($event->getStart() > new \DateTime('today') && $event->getStart() < new \DateTime('tomorrow'))
            {
                ?>
            <div class="session-row  event-type-<?php print $event->getType(); ?>  cid default-other " id="eid-<?php print $event->getId(); ?>">
                <div class="class-name">
                    <span class="class<?php print $classI; ?>">&nbsp;</span> <?php print $event->getName(); ?>
                </div>
                <div class="date">
                    <div class="read-only"><?php print $event->getStart()->format('h:i:s') . '&nbsp;' . $event->getStart()->format('A'); ?> -
                        <?php print $event->getEnd()->format('h:i:s') . '&nbsp;' . $event->getEnd()->format('A'); ?></div>
                </div>
                <div class="completed">
                    <label class="checkbox"><input type="checkbox" name="plan-sort" value="class" <?php print ($event->getCompleted() ? 'checked="checked"' : ''); ?>><i></i></label>
                </div>
            </div><?php
            }
        } ?>
    </div>
</div>


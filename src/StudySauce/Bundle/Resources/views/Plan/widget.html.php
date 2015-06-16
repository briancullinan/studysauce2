<?php
use StudySauce\Bundle\Entity\Event;
?>
<div class="widget-wrapper">
    <div class="widget plan-widget">
        <h3>Study plan</h3>
        <?php
        if(!$user->hasRole('ROLE_PAID')) {
            ?>
            <a href="<?php print $view['router']->generate('premium'); ?>" class="cloak">Nothing set up yet.  Click <span class="reveal">here</span> to go premium.</a>
            <?php
        }
        elseif(empty($classes) || empty($events)) {
            ?>
            <a href="<?php print $view['router']->generate('schedule'); ?>" class="cloak">Nothing set up yet. Click <span
                    class="reveal">here</span> to set up your schedule.</a>
        <?php
        }
        else {
            $count = 0;
            foreach($events as $i => $event) {
                /** @var Event $event */
                if ($event->getType() == 'm' || $event->getType() == 'r' || $event->getType() == 'z') {
                    continue;
                }
                if ($count > 10)
                    break;
                ?>
                <div class="session-row <?php
                    print ' event-type-' . $event->getType();
                    print ' event-id-' . $event->getId();
                    print ($event->getCompleted() ? ' done' : ''); ?>">
                    <div class="class-name">
                        <span class="class<?php print (!empty($event->getCourse())
                            ? $event->getCourse()->getIndex()
                            : ''); ?>">&nbsp;</span> <?php print $event->getName(); ?>
                    </div>
                    <div class="date">
                        <div class="read-only"><?php print $event->getStart()->format('g:i') .
                                '&nbsp;' . $event->getStart()->format('A'); ?> -
                            <?php print $event->getEnd()->format('g:i') .
                                '&nbsp;' . $event->getEnd()->format('A'); ?></div>
                    </div>
                    <div class="completed">
                        <label class="checkbox"><input type="checkbox" value="true" <?php print ($event->getCompleted()
                                ? 'checked="checked"'
                                : ''); ?>><i></i></label>
                    </div>
                </div><?php
            }
        } ?>
    </div>
</div>


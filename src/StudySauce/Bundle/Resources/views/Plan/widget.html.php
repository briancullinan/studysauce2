<?php
use StudySauce\Bundle\Entity\Event;
?>
<div class="widget-wrapper">
    <div class="widget plan-widget">
        <h3>Study plan</h3>
        <?php
        if($isDemo || empty($events)) {
            ?>
            <a href="<?php print $view['router']->generate('schedule'); ?>" class="cloak">Nothing set up yet. Click <span
                    class="reveal">here</span> to set up your schedule.</a>
        <?php
        }
        else {
            $count = 0;
            foreach($events as $i => $event) {
                if (strpos($event['className'], 'event-type-m')
                    || strpos($event['className'], 'event-type-r')
                    || strpos($event['className'], 'event-type-z')
                    || strpos($event['className'], 'event-type-h')) {
                    continue;
                }

                if (new \DateTime($event['start']) < new \DateTime('today') || new \DateTime($event['start']) > new \DateTime('tomorrow'))
                    continue;
                ?>
                <div class="session-row <?php
                    print preg_replace('/(class[0-9])/i', '', $event['className']);
                    print ' event-id-' . $event['eventId'];
                    print ($event['completed'] ? ' done' : ''); ?>">
                    <div class="class-name">
                        <span class="<?php print preg_replace('/.*(class[0-9]).*/i', '$1', $event['className']); ?>">&nbsp;</span> <?php print preg_replace('/<h4>(.*?)<\/h4>/i', '', $event['title']); ?>
                    </div>
                    <div class="date">
                        <div class="read-only"><?php print date_timezone_set(new \DateTime($event['start']), new \DateTimeZone(date_default_timezone_get()))->format('g:i') .
                                '&nbsp;' . date_timezone_set(new \DateTime($event['start']), new \DateTimeZone(date_default_timezone_get()))->format('A'); ?> -
                            <?php print date_timezone_set(new \DateTime($event['end']), new \DateTimeZone(date_default_timezone_get()))->format('g:i') .
                                '&nbsp;' . date_timezone_set(new \DateTime($event['end']), new \DateTimeZone(date_default_timezone_get()))->format('A'); ?></div>
                    </div>
                    <div class="completed">
                        <label class="checkbox"><input type="checkbox" value="true" <?php print ($event['completed']
                                ? 'checked="checked"'
                                : ''); ?>><i></i></label>
                    </div>
                </div><?php
                $count++;
            }
            if(empty($count)) { ?><a href="<?php print $view['router']->generate('plan'); ?>" class="cloak">Nothing to do today. Click <span class="reveal">here</span> to go to your study plan.</a><?php }
        } ?>
    </div>
</div>


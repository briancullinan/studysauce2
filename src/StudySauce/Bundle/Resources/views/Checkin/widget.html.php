
<div class="widget-wrapper">
    <div class="widget checkin-widget">
        <h3>Study now
            <a class="minplayer-default-play minplayer-default-button ui-state-default ui-corner-all"
               title="Toggle music on/off">
                <span class="ui-icon ui-icon-play"></span>
            </a>
            <a class="minplayer-default-pause minplayer-default-button ui-state-default ui-corner-all"
               title="Toggle music on/off" style="display: none;">
                <span class="ui-icon ui-icon-pause"></span>
            </a>
        </h3>
        <?php if(empty($courses)) { ?>
            <a href="<?php print $view['router']->generate('schedule'); ?>" class="cloak">Nothing set up yet.  Click <span class="reveal">here</span> to enter your class schedule.</a>
        <?php } else { ?>
            <div class="classes">
                <div class="flip-counter flip-clock-wrapper">
                    <?php echo $view->render('StudySauceBundle:Checkin:digits.html.php'); ?>
                </div>
                <?php foreach ($courses as $i => $c) {
                    ?><a href="#class<?php print $i; ?>"
                         class="checkin class<?php print $i; ?> course-id-<?php print $c->getId(); ?>">
                    <span><?php print $c->getName(); ?></span></a>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</div>


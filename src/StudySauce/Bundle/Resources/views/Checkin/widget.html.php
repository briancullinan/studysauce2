
<div class="widget-wrapper">
    <div class="widget checkin-widget">
        <h3>Study now</h3>
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


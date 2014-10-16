
<div class="widget-wrapper">
    <div class="widget checkin-widget">
        <h3>Study now</h3>
        <div class="flip-counter clock flip-clock-wrapper">
            <?php echo $view->render('StudySauceBundle:Checkin:digits.html.php'); ?>
        </div>
        <?php foreach ($courses as $i => $c) {
            ?><a href="#class<?php print $i; ?>" class="class<?php print $i; ?>" id="checkin-<?php print $c->getId(); ?>"><span><?php print $c->getName(); ?></span></a><?php
        } ?>
    </div>
</div>


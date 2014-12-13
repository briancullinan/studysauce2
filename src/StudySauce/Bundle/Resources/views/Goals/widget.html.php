
<div class="widget-wrapper">
    <div class="widget goals-widget">
        <h3>My goals</h3>
        <?php if(empty($behavior)) { ?>
            <a href="<?php print $view['router']->generate('goals'); ?>" class="cloak">Nothing set up yet.  Click <span class="reveal">here</span> to set your goals.</a>
        <?php } else { ?>
        <div class="behavior">
            <strong><?php print $behavior->getGoal(); ?></strong> hours per week
        </div>
        <?php }
        if(!empty($milestone)) { ?>
        <div class="milestone">
            <strong><?php print $milestone->getGoal(); ?></strong> grade on exam/paper
        </div>
        <?php }
        if(!empty($outcome)) { ?>
        <div class="outcome">
            <strong><?php print number_format($outcome->getGoal(), 2); ?></strong> target GPA for the term
        </div>
        <?php } ?>
    </div>
</div>


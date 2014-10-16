<div class="widget-wrapper">
    <div class="widget goals-widget">
        <h3>My goals</h3>
        <div class="behavior">
            <strong><?php print (!empty($behavior) ? $behavior->getGoal() : 'Unset'); ?></strong> hours per week
        </div>
        <div class="milestone">
            <strong><?php print (!empty($milestone) ? $milestone->getGoal() : 'Unset'); ?></strong> grade on exam/paper
        </div>
        <div class="outcome">
            <strong><?php print (!empty($outcome) ? $outcome->getGoal() : 'Unset'); ?></strong> target GPA for the term
        </div>
    </div>
</div>


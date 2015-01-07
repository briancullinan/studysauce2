<?php

use Course1\Bundle\Entity\Course1;

$view->extend('Course1Bundle:Shared:layout.html.php');

/** @var Course1 $course */

 $view['slots']->start('body'); ?>
<div class="panel-pane course1 step4" id="course1_upgrade-step4">
    <div class="pane-content">
        <h2>Level 1 complete</h2>
        <div class="grid_6">
            <p>Well done. You have completed the first part of the course. Upgrade to premium or bill your parents to keep going!</p>
            <p>&nbsp;</p>
            <p>How likely are you to recommend Study Sauce for a friend?</p>
            <div class="net-promoter">
                <label class="radio"><input type="radio" name="investment-net-promoter" value="0" <?php print ($course->getNetPromoter() === 0 ? 'checked="checked"' : ''); ?>/><i></i><br/><span>0</span></label>
                <label class="radio"><input type="radio" name="investment-net-promoter" value="1" <?php print ($course->getNetPromoter() == 1 ? 'checked="checked"' : ''); ?>/><i></i><br/><span>1</span></label>
                <label class="radio"><input type="radio" name="investment-net-promoter" value="2" <?php print ($course->getNetPromoter() == 2 ? 'checked="checked"' : ''); ?>/><i></i><br/><span>2</span></label>
                <label class="radio"><input type="radio" name="investment-net-promoter" value="3" <?php print ($course->getNetPromoter() == 3 ? 'checked="checked"' : ''); ?>/><i></i><br/><span>3</span></label>
                <label class="radio"><input type="radio" name="investment-net-promoter" value="4" <?php print ($course->getNetPromoter() == 4 ? 'checked="checked"' : ''); ?>/><i></i><br/><span>4</span></label>
                <label class="radio"><input type="radio" name="investment-net-promoter" value="5" <?php print ($course->getNetPromoter() == 5 ? 'checked="checked"' : ''); ?>/><i></i><br/><span>5</span></label>
                <label class="radio"><input type="radio" name="investment-net-promoter" value="6" <?php print ($course->getNetPromoter() == 6 ? 'checked="checked"' : ''); ?>/><i></i><br/><span>6</span></label>
                <label class="radio"><input type="radio" name="investment-net-promoter" value="7" <?php print ($course->getNetPromoter() == 7 ? 'checked="checked"' : ''); ?>/><i></i><br/><span>7</span></label>
                <label class="radio"><input type="radio" name="investment-net-promoter" value="8" <?php print ($course->getNetPromoter() == 8 ? 'checked="checked"' : ''); ?>/><i></i><br/><span>8</span></label>
                <label class="radio"><input type="radio" name="investment-net-promoter" value="9" <?php print ($course->getNetPromoter() == 9 ? 'checked="checked"' : ''); ?>/><i></i><br/><span>9</span></label>
                <label class="radio"><input type="radio" name="investment-net-promoter" value="10" <?php print ($course->getNetPromoter() == 10 ? 'checked="checked"' : ''); ?>/><i></i><br/><span>10</span></label>
            </div>
            <header>
                <label>Not likely at all</label>
                <label>Neutral</label>
                <label>Extremely likely</label>
            </header>
        </div>
        <div class="grid_6">
            <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/situation_compressed.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                <img width="200" height="200" src="<?php echo $view->escape($url) ?>" alt="Situation"/>
            <?php endforeach; ?>
        </div>
        <div class="highlighted-link">
            <a href="<?php print $view['router']->generate('premium'); ?>" class="more">Upgrade to premium</a>
            <a href="#bill-parents" class="more parents" data-toggle="modal">Bill my parents</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php $view['slots']->stop(); ?>

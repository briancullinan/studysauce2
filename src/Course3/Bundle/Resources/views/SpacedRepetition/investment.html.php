<?php

use Course3\Bundle\Entity\Course3;

$view->extend('Course3Bundle:Shared:layout.html.php');

/** @var Course3 $course */

 $view['slots']->start('body'); ?>
<div class="panel-pane course3 step4" id="course3_spaced_repetition-step4">
    <div class="pane-content">
        <h2>Congratulations, you have finished the course!!!</h2>
        <div class="grid_6">
            <h3>Assignment:</h3>
            <p>We hope you enjoyed the course. You now know how to study effectively. Remember to use your study tools and feel free to revisit some of the videos from time to time.</p>
            <p>Please let us know what you thought of the course. We are particularly interested in your feedback and your suggestions for new videos and study tools that you would like to see us make.</p>
            <label class="input"><textarea name="investment-feedback"><?php print $course->getFeedback(); ?></textarea></label>
            <p>How likely are you to recommend Study Sauce to a friend?</p>
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
        </div>
        <div class="grid_6">
            <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/complication_compressed.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                <img width="200" height="200" src="<?php echo $view->escape($url) ?>" alt="Complication"/>
            <?php endforeach; ?>
        </div>
        <div class="highlighted-link <?php print ($course->getNetPromoter() !== null && !empty($course->getFeedback()) ? 'valid' : 'invalid'); ?>">
            <a href="<?php print $view['router']->generate('home'); ?>" class="more">Way to go...me</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php $view['slots']->stop(); ?>

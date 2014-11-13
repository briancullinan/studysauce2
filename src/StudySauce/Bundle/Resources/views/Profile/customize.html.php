<?php
use StudySauce\Bundle\Entity\Course;
use StudySauce\Bundle\Entity\Schedule;

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

/** @var Schedule $schedule */
$view['slots']->start('stylesheets');
foreach ($view['assetic']->stylesheets(
    [
        '@StudySauceBundle/Resources/public/css/profile.css'
    ],
    [],
    ['output' => 'bundles/studysauce/css/*.css']
) as $url):
    ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts(
    [
        '@StudySauceBundle/Resources/public/js/profile.js'
    ],
    [],
    ['output' => 'bundles/studysauce/js/*.js']
) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('body'); ?>

<div class="panel-pane" id="customization">
    <div class="pane-content">
        <?php if($app->getRequest()->get('_format') == 'funnel') {
            echo $view->render('StudySauceBundle:Buy:funnel.html.php');
        } else { ?>
            <h2>Please tell us more about your classes</h2>
        <?php } ?>
        <div class="study-types">
            <h3>What is the primary type of studying you expect to do in this class? <span>Q: 1/2</span></h3>
            <header>
                <label>Memorization</label>
                <label>Reading/<br/>writing</label>
                <label>Conceptual<br/>application</label>
            </header>
            <?php
            foreach($courses as $i => $c)
            {
                /** @var Course $c */
                ?>
                <h4><span class="class<?php print $i; ?>"></span><?php print $c->getName(); ?></h4>
                <label class="radio"><span>Memorization</span><input name="profile-type-<?php print $c->getId(); ?>" type="radio" value="memorization" <?php print ($c->getStudyType() == 'memorization' ? 'checked="checked"' : ''); ?>><i></i></label>
                <label class="radio"><span>Reading/<br/>writing</span><input name="profile-type-<?php print $c->getId(); ?>" type="radio" value="reading" <?php print ($c->getStudyType() == 'reading' ? 'checked="checked"' : ''); ?>><i></i></label>
                <label class="radio"><span>Conceptual<br/>application</span><input name="profile-type-<?php print $c->getId(); ?>" type="radio" value="conceptual" <?php print ($c->getStudyType() == 'conceptual' ? 'checked="checked"' : ''); ?>><i></i></label>
            <?php } ?>
        </div>
        <div class="study-difficulty">
            <h3>What is the primary type of studying you expect to do in this class? <span>Q: 2/2</span></h3>
            <header>
                <label>Easy</label>
                <label>Average</label>
                <label>Tough</label>
            </header>
            <?php
            foreach($courses as $i => $c)
            {
                /** @var Course $c */
                ?>
                <h4><span class="class<?php print $i; ?>"></span><?php print $c->getName(); ?></h4>
                <label class="radio"><span>Easy</span><input name="profile-difficulty-<?php print $c->getId(); ?>" type="radio" value="easy" <?php print ($c->getStudyDifficulty() == 'easy' ? 'checked="checked"' : ''); ?>><i></i></label>
                <label class="radio"><span>Average</span><input name="profile-difficulty-<?php print $c->getId(); ?>" type="radio" value="medium" <?php print ($c->getStudyDifficulty() == 'average' ? 'checked="checked"' : ''); ?>><i></i></label>
                <label class="radio"><span>Tough</span><input name="profile-difficulty-<?php print $c->getId(); ?>" type="radio" value="tough" <?php print ($c->getStudyDifficulty() == 'tough' ? 'checked="checked"' : ''); ?>><i></i></label>
            <?php } ?>
        </div>
        <div class="form-actions highlighted-link">
            <?php if($app->getRequest()->get('_format') == 'funnel') { ?>
                <a href="#save-profile" class="more">Next</a>
            <?php } else { ?>
                <a href="#save-profile" class="more">Save</a>
            <?php } ?>
        </div>
    </div>
</div>

<?php $view['slots']->stop(); ?>

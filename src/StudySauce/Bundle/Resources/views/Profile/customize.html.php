<?php
use StudySauce\Bundle\Entity\Course;
use StudySauce\Bundle\Entity\Schedule;

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

/** @var Schedule $schedule */
$view['slots']->start('stylesheets');
foreach ($view['assetic']->stylesheets(['@StudySauceBundle/Resources/public/css/profile.css'],[],['output' => 'bundles/studysauce/css/*.css']) as $url): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts(['@StudySauceBundle/Resources/public/js/profile.js'],[],['output' => 'bundles/studysauce/js/*.js']) as $url): ?>
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
        <form action="<?php print $view['router']->generate('profile_update'); ?>" method="post">
        <div class="study-types">
            <h3>What is the primary type of studying you expect to do in this class? <span>Q: 1/2</span></h3>
            <header>
                <label title="Class will primarily test your ability to remember definitions or terms">Memorization</label>
                <label title="Class work focuses on heavy reading and writing">Reading / <br/>writing</label>
                <label title="Class will focus on deeper understanding of materials">Conceptual <br/>application</label>
            </header>
            <?php
            foreach($courses as $i => $c)
            {
                /** @var Course $c */
                ?>
                <h4><span class="class<?php print $i; ?>"></span><?php print $c->getName(); ?></h4>
                <label title="Class will primarily test your ability to remember definitions or terms"
                    class="radio"><span>Memorization</span><input name="profile-type-<?php print $c->getId(); ?>" type="radio" value="memorization" <?php print ($c->getStudyType() == 'memorization' ? 'checked="checked"' : ''); ?>><i></i></label>
                <label title="Class work focuses on heavy reading and writing"
                    class="radio"><span>Reading / <br/>writing</span><input name="profile-type-<?php print $c->getId(); ?>" type="radio" value="reading" <?php print ($c->getStudyType() == 'reading' ? 'checked="checked"' : ''); ?>><i></i></label>
                <label title="Class will focus on deeper understanding of materials"
                    class="radio"><span>Conceptual <br/>application</span><input name="profile-type-<?php print $c->getId(); ?>" type="radio" value="conceptual" <?php print ($c->getStudyType() == 'conceptual' ? 'checked="checked"' : ''); ?>><i></i></label>
            <?php } ?>
        </div>
        <div class="study-difficulty">
            <h3>What is the primary type of studying you expect to do in this class? <span>Q: 2/2</span></h3>
            <header>
                <label title="Class should require less homework and/or be less difficult than most classes.  Will require less than 2 hours of studying for every hour of class">Easy</label>
                <label title="Class will be a pretty standard amount of homework/difficulty.  Will require ~2 hours studying for every hour of class">Average</label>
                <label title="Class will require much more homework and focus than others.  ~3 hours studying for every hour of class">Tough</label>
            </header>
            <?php
            foreach($courses as $i => $c)
            {
                /** @var Course $c */
                ?>
                <h4><span class="class<?php print $i; ?>"></span><?php print $c->getName(); ?></h4>
                <label title="Class should require less homework and/or be less difficult than most classes.  Will require less than 2 hours of studying for every hour of class"
                    class="radio"><span>Easy</span><input name="profile-difficulty-<?php print $c->getId(); ?>" type="radio" value="easy" <?php print ($c->getStudyDifficulty() == 'easy' ? 'checked="checked"' : ''); ?>><i></i></label>
                <label title="Class will be a pretty standard amount of homework/difficulty.  Will require ~2 hours studying for every hour of class"
                    class="radio"><span>Average</span><input name="profile-difficulty-<?php print $c->getId(); ?>" type="radio" value="average" <?php print ($c->getStudyDifficulty() == 'average' ? 'checked="checked"' : ''); ?>><i></i></label>
                <label title="Class will require much more homework and focus than others.  ~3 hours studying for every hour of class"
                    class="radio"><span>Tough</span><input name="profile-difficulty-<?php print $c->getId(); ?>" type="radio" value="tough" <?php print ($c->getStudyDifficulty() == 'tough' ? 'checked="checked"' : ''); ?>><i></i></label>
            <?php } ?>
        </div>
        <div class="form-actions highlighted-link">
            <?php if($app->getRequest()->get('_format') == 'funnel') { ?>
                <button type="submit" value="#save-profile" class="more">Next</button>
            <?php } else { ?>
                <button type="submit" value="#save-profile" class="more">Save</button>
            <?php } ?>
        </div>
        </form>
    </div>
</div>

<?php $view['slots']->stop(); ?>

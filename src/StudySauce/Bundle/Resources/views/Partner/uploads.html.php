<?php

use StudySauce\Bundle\Entity\ActiveStrategy;
use StudySauce\Bundle\Entity\OtherStrategy;
use StudySauce\Bundle\Entity\SpacedStrategy;
use StudySauce\Bundle\Entity\TeachStrategy;

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('body'); ?>
<div class="panel-pane" id="deadlines">
    <div class="pane-content">
        <?php echo $view->render('StudySauceBundle:Partner:partner-nav.html.php', ['user' => $user]); ?>
        <h2>Upcoming deadlines</h2>
        <?php
if(empty($uploads)) {
    ?>
    <h3>Your student has not completed this section yet.</h3>
<?php
}
        else
        {
    // sort strategies by date and display strategies read-only
    foreach($uploads as $i => $s)
    {
        list($strategy, $entity) = $s;
        if($strategy == 'active')
        {
            /** @var ActiveStrategy $entity */
            ?>
            <h3>Active reading - Follow the guide below to better retain what you are reading.</h3>
            <h4>Before reading:</h4>
            <label>Take no more than 2 minutes to skim the reading. What is the topic?</label>
            <textarea readonly="readonly" name="strategy-skim"><?php print htmlspecialchars($entity->getSkim(), ENT_QUOTES); ?></textarea>
            <label>Why am I being asked to read this at this point in the class?</label>
            <textarea readonly="readonly" name="strategy-why"><?php print htmlspecialchars($entity->getWhy(), ENT_QUOTES); ?></textarea>
            <h4>During reading:</h4>
            <label>What questions do I have as I am reading?</label>
            <textarea readonly="readonly" name="strategy-questions"><?php print htmlspecialchars($entity->getQuestions(), ENT_QUOTES); ?></textarea>
            <h4>After reading:</h4>
            <label>Please summarize the reading in a few paragraphs (less than 1 page).  What are the 1 or 2 most important ideas from the reading?</label>
            <textarea readonly="readonly" name="strategy-summarize"><?php print htmlspecialchars($entity->getSummarize(), ENT_QUOTES); ?></textarea>
            <label>What possible exam questions will result from this reading?</label>
            <textarea readonly="readonly" name="strategy-exam"><?php print htmlspecialchars($entity->getExam(), ENT_QUOTES); ?></textarea>
        <?php
        }
        elseif($strategy == 'teach')
        {
            /** @var TeachStrategy $entity */
            ?>
            <h3>Teach - Upload a 1 min video explaining your assignment</h3>
            <div class="plupload">
                <a href="#teach-select" class="plup-select" id="teach-{eid}-select">Click here to select an image</a>
                <div class="plup-filelist" id="teach-{eid}-filelist">
                    <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/upload.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                        <img width="200" height="200" src="<?php echo $view->escape($url) ?>" alt="LOGO" />
                    <?php endforeach; ?>
                </div>
                <input type="hidden" name="teach-{eid}-plupload">
            </div>

            <div class="strategy-notes">
                <label>Title:</label>
                <input type="text" class="form-text" name="strategy-title" value="<?php print htmlspecialchars($entity->getTitle(), ENT_QUOTES); ?>" />
                <label>Notes:</label>
                <textarea type="text" name="strategy-notes"><?php print htmlspecialchars($entity->getNotes(), ENT_QUOTES); ?></textarea>
            </div>
        <?php
        }
        elseif($strategy == 'other')
        {
            /** @var OtherStrategy $entity */
            ?>
            <h3>Notes:</h3>
            <textarea name="strategy-notes"><?php print htmlspecialchars($entity->getNotes(), ENT_QUOTES); ?></textarea>
        <?php
        }
        elseif($strategy == 'spaced')
        {
            /** @var SpacedStrategy $entity */
            ?>
            <h3>Spaced repetition - Commit information to your long term memory by revisiting past work.</h3>
            <h4>Instructions - We highly recommend flashcards.  Online flashcard maker Quizlet is our favorite.  Read more about spaced repetition here.</h4>
            <div class="strategy-review">
                <label>Review material from:</label>
            </div>
            <div class="strategy-notes">
                <label>Write down any notes below:</label>
                <textarea type="text" name="strategy-notes"><?php print htmlspecialchars($entity->getNotes(), ENT_QUOTES); ?></textarea>
            </div>
        <?php
        }
    }
}
        ?>
    </div>
</div>
<?php $view['slots']->stop(); ?>

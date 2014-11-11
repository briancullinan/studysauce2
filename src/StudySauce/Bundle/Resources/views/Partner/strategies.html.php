<div class="strategy-active read-only invalid">
    <h3>Active reading - Follow the guide below to better retain what you are reading.</h3>
    <h4>Before reading:</h4>
    <label class="input"><span>Take no more than 2 minutes to skim the reading. What is the topic?</span>
        <textarea readonly="readonly" name="strategy-skim"></textarea>
    </label>
    <label class="input"><span>Why am I being asked to read this at this point in the class?</span>
        <textarea readonly="readonly" name="strategy-why"></textarea>
    </label>
    <h4>During reading:</h4>
    <label class="input"><span>What questions do I have as I am reading?</span>
        <textarea readonly="readonly" name="strategy-questions"></textarea>
    </label>
    <h4>After reading:</h4>
    <label class="input"><span>Please summarize the reading in a few paragraphs (less than 1 page).  What are the 1 or 2 most important ideas from the reading?</span>
        <textarea readonly="readonly" name="strategy-summarize"></textarea>
    </label>
    <label class="input"><span>What possible exam questions will result from this reading?</span>
        <textarea readonly="readonly" name="strategy-exam"></textarea>
    </label>
</div>
<div class="strategy-other read-only invalid">
    <h3>Notes:</h3>
    <textarea readonly="readonly" name="strategy-notes" placeholder="Write any notes here."></textarea>
</div>
<div class="strategy-prework read-only invalid">
    <h3>Prework - Get prepared for your class tomorrow.</h3>
    <div>
        <label class="checkbox"><input type="checkbox" value="topics"><i></i><span>Look at your syllabus to see what topics will be covered.</span></label>
        <label class="checkbox"><input type="checkbox" value="reading"><i></i><span>Ensure you have completed the assigned reading.</span></label>
        <label class="checkbox"><input type="checkbox" value="confusion"><i></i><span>Identify areas of confusion.  This will help you focus during the class on areas of need.</span></label>
        <label class="checkbox"><input type="checkbox" value="questions"><i></i><span>Prepare questions that you would like answered during class.</span></label>
    </div>
    <h3>Notes:</h3>
    <label class="input"><textarea readonly="readonly" name="strategy-notes" cols="60" rows="2"></textarea></label>
</div>
<div class="strategy-spaced read-only invalid">
    <h3>Spaced repetition - Commit information to your long term memory by revisiting past work.</h3>
    <h4>Instructions - We highly recommend flashcards.  Online flashcard maker Quizlet is our favorite.  Read more about spaced repetition here.</h4>
    <div class="strategy-review">
        <label>Review material from:</label>
    </div>
    <div class="strategy-notes">
        <label class="input"><span>Write down any notes below:</span>
            <textarea readonly="readonly" name="strategy-notes"></textarea>
        </label>
    </div>
</div>
<div class="strategy-teach read-only invalid">
    <h3>Teach - Upload a 1 min video explaining your assignment</h3>
    <div class="plupload">
        <a href="#teach-select" class="plup-select" id="teach-{eventId}-select">Click here to select a video</a>
        <div class="plup-filelist" id="teach-{eventId}-filelist">
            <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/upload.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                <img width="200" height="200" src="<?php echo $view->escape($url) ?>" alt="LOGO" />
            <?php endforeach; ?>
        </div>
        <input type="hidden" name="teach-{eventId}-plupload">
    </div>
    <div class="strategy-notes">
        <label class="input">
            <span>Title:</span>
            <input type="text" name="strategy-title">
        </label>
        <label class="input">
            <span>Notes:</span>
            <textarea readonly="readonly" name="strategy-notes"></textarea>
        </label>
    </div>
</div>


<div class="strategy-teach invalid">
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
            <textarea type="text" name="strategy-notes"></textarea>
        </label>
    </div>
    <div class="highlighted-link"><a href="#expand">Expand</a><a class="more" href="#save-strategy">Save</a></div>
</div>

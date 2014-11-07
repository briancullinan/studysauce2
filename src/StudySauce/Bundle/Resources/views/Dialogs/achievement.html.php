<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

 $view['slots']->start('modal-header') ?>
Let your sponsor know about your study achievement.
<?php $view['slots']->stop();

 $view['slots']->start('modal-body') ?>
<div class="plupload">
    <a href="#goal-select" class="plup-select" id="goals-plupload-select">Click here to select an image</a>
    <div class="plup-filelist" id="goal-plupload-filelist">
        <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/upload.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
            <img width="200" height="200" src="<?php echo $view->escape($url) ?>" alt="LOGO" />
        <?php endforeach; ?>
    </div>
    <input type="hidden" name="goals-plupload">
</div>
<label class="input">
    <span>Message</span>
    <textarea placeholder="" cols="60" rows="2"></textarea>
</label>
<?php $view['slots']->stop();

 $view['slots']->start('modal-footer') ?>
<a href="#submit-claim" class="btn btn-primary" data-dismiss="modal">Save</a>
<?php $view['slots']->stop() ?>


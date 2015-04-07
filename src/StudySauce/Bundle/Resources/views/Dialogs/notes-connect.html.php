<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Put all of your notes in one place.
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<div class="connect-sketch">
    <span class="site-name"><?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/Study_Sauce_Logo_Sketch.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
            <img width="100" height="100" src="<?php echo $view->escape($url) ?>" alt="LOGO" />
        <?php endforeach; ?><strong>Study</strong> Sauce</span>
    <div class="connect-or"><span>+</span></div>
    <span class="evernote-name"><?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/logotentips-7.jpg'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
            <img width="240" height="165" src="<?php echo $view->escape($url) ?>" alt="LOGO" />
        <?php endforeach; ?></span>
</div>
<div class="form-actions">
    <?php foreach($services as $o => $url) { ?>
        <a href="<?php print $url; ?>?_target=<?php print $view['router']->generate('notes'); ?>" class="more">Connect Evernote</a></label>
    <?php } ?>
</div>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer') ?>

<?php $view['slots']->stop() ?>


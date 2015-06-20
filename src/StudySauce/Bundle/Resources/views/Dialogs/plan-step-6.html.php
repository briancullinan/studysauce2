<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Step 6 - Download your new study plan
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<p>Syncronize your study plan with Google Calendar and all your devices.<br/><br/></p>

<div class="site-name"><?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/Study_Sauce_Logo_Sketch.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
        <img width="100" height="100" src="<?php echo $view->escape($url) ?>" alt="LOGO" />
    <?php endforeach; ?><strong>Study</strong> Sauce</div>
<div class="connect-or"><span>+</span></div>
<div class="evernote-name"><?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/calendar-logo.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
        <img width="120" src="<?php echo $view->escape($url) ?>" alt="LOGO" />
    <?php endforeach; ?>
    <span style="font-family: Arial, sans-serif; color:#15C;">Google</span>
    <?php foreach($services as $o => $url) { ?>
        <a href="<?php print $url; ?>?_target=<?php print $view['router']->generate('plan'); ?>" class="btn">Calendar</a>
    <?php } ?>
</div>
<br/><br/><br/>
<div class="highlighted-link">
    <ul class="dialog-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    <a href="#plan-step-6-2" data-toggle="modal" class="more">Download iCal</a>
</div>
<?php $view['slots']->stop();

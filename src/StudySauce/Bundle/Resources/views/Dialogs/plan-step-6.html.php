<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Step 6 - Download your new study plan
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<p>Now you can download your study plan into your calendar.  Just click on the icon below.<br/><br/></p>

<div class="site-name"><?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/Study_Sauce_Logo_Sketch.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
        <img width="100" height="100" src="<?php echo $view->escape($url) ?>" alt="LOGO" />
    <?php endforeach; ?><strong>Study</strong> Sauce<br />
<a href="<?php print $view['router']->generate('plan_download'); ?>"><?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/plan-download-white.gif'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
        <img width="50" height="50" src="<?php echo $view->escape($url) ?>" alt="LOGO" />
    <?php endforeach; ?></a></div>
<div class="connect-or"><span>+</span></div>
<div class="evernote-name"><?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/calendar-logo.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
        <img width="150" src="<?php echo $view->escape($url) ?>" alt="LOGO" />
    <?php endforeach; ?>
    <?php foreach($services as $o => $url) { ?>
        <a href="<?php print $url; ?>?_target=<?php print $view['router']->generate('plan'); ?>" class="btn">Connect</a>
    <?php } ?>
</div>
<br/><br/><br/>
<div class="highlighted-link invalid">
    <ul class="dialog-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    <div class="invalid-only">Please download your plan before you proceed.</div>
    <a href="#done" class="more">Go to study plan</a>
</div>
<?php $view['slots']->stop();

<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Step 6 - Download your new study plan
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<p>Download your study plan every time it changes by click the icon below.<br/><br/></p>
    <a href="<?php print $view['router']->generate('plan_download'); ?>"><?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/plan-download-white.gif'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
            <img width="50" height="50" src="<?php echo $view->escape($url) ?>" alt="LOGO" />
        <?php endforeach; ?></a>
    <br/><br/><br/>
<div class="highlighted-link invalid">
    <ul class="dialog-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    <div class="invalid-only">Please download your plan before you proceed.</div>
    <a href="#done" class="more">Go to study plan</a>
</div>
<?php $view['slots']->stop();

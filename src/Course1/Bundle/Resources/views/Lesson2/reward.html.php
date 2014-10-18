
<?php $view->extend('Course1Bundle:Shared:layout.html.php');

 $view['slots']->start('body'); ?>
<div class="panel-pane course1 step3" id="lesson2-step3">
    <div class="pane-content">
        <?php foreach ($view['assetic']->image(['@Course1Bundle/Resources/public/images/reward2.gif'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
            <img width="100%" src="<?php echo $view->escape($url) ?>" alt="LOGO" />
            <img width="100%" src="<?php echo $view->escape($url) ?>" alt="LOGO" />
            <img width="100%" src="<?php echo $view->escape($url) ?>" alt="LOGO" />
        <?php endforeach; ?>
        <span class="badge setup-hours">&nbsp;</span>
        <div class="description">&nbsp;</div>
        <div class="highlighted-link">
            <a href="<?php print $view['router']->generate('lesson2', ['_step' => 4]); ?>" class="more">Next</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php $view['slots']->stop(); ?>

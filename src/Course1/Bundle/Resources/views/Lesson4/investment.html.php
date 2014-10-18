
<?php $view->extend('Course1Bundle:Shared:layout.html.php');

 $view['slots']->start('body'); ?>
<div class="panel-pane course1 step4" id="lesson4-step4">

    <div class="pane-content">

        <h2>Well done, now complete a short task before we move on.</h2>
        <div class="grid_6">
            <h3>Assignment:</h3>
            <p>Study Sauce tools will be personalized to you and your class schedule.  Please enter the specifics of your class schedule in order to use the tools.</p>
        </div>
        <div class="grid_6">
            <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/resolution_compressed.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                <img width="200" height="200" src="<?php echo $view->escape($url) ?>" alt="Resolution"/>
            <?php endforeach; ?>
        </div>
        <div class="highlighted-link">
            <a href="<?php print $view['router']->generate('schedule'); ?>" class="more">Enter class schedule</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php $view['slots']->stop(); ?>

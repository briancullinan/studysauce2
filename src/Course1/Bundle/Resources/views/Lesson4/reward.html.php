
<?php $view->extend('Course1Bundle:Lesson1:layout.html.php');

 $view['slots']->start('body'); ?>
<div class="panel-pane course1 step3" id="lesson4-step3">
    <div class="pane-content" id="new-award">
        <div>
            <span class="badge setup-hours">&nbsp;</span>
            <div class="description">
                <h3>You have been awarded the <strong>Distractions</strong> badge.</h3>
                <p>An effective method for discouraging other from distracting you...</p>
            </div>
            <div class="highlighted-link">
                <a href="<?php print $view['router']->generate('lesson4', ['_step' => 4]); ?>" class="more">Next</a>
            </div>
            <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
        </div>
    </div>
</div>

<?php $view['slots']->stop(); ?>

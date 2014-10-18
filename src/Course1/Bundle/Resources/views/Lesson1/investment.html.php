<?php use Symfony\Component\HttpKernel\Controller\ControllerReference;

 $view->extend('Course1Bundle:Shared:layout.html.php');

 $view['slots']->start('body'); ?>
<div class="panel-pane course1 step4" id="lesson1-step4">

    <div class="pane-content">

        <h2>Great job!</h2>
        <div class="grid_6">
            <h3>Finally, before we get started, we have one last question for you.</h3>
            <br />
            <label class="input">
                <span>Why do you want to become better at studying?</span>
                <textarea placeholder="" cols="60" rows="2"></textarea>
            </label>
        </div>
        <div class="grid_6">
            <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/situation_compressed.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                <img width="200" height="200" src="<?php echo $view->escape($url) ?>" alt="Situation"/>
            <?php endforeach; ?>
        </div>
        <div class="highlighted-link invalid">
            <a href="<?php print $view['router']->generate('lesson2', ['_step' => 0]); ?>" class="more">Next step</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php $view['slots']->stop(); ?>

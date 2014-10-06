<?php use Symfony\Component\HttpKernel\Controller\ControllerReference;

/** @var $view \Symfony\Bundle\FrameworkBundle\Templating\PhpEngine */
 $view->extend('Course1Bundle:Lesson1:layout.html.php');

 $view['slots']->start('body'); ?>
<div class="panel-pane course1 step3" id="lesson1-step3">
    <div class="pane-content" id="new-award">
        <div>
            <span class="badge">&nbsp;</span>
            <div class="description">
                <h3>You have been awarded the <strong>Pulse Detected</strong> badge.</h3>
                <p>Yep, you are alive.</p>
            </div>
            <div class="highlighted-link">
                <a href="<?php print $view['router']->generate('lesson1', ['_step' => 4]); ?>" class="more">Next</a>
            </div>
            <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
        </div>
    </div>
</div>

<?php $view['slots']->stop(); ?>

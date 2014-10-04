<?php use Symfony\Component\HttpKernel\Controller\ControllerReference; ?>

<?php $view->extend('Course1Bundle:Lesson1:layout.html.php') ?>

<?php $view['slots']->start('body'); ?>
<div class="panel-pane course1" id="lesson1-step4">

    <div class="pane-content">

        <h2>Great job!</h2>
        <h3>Finally, before we get started, we have one last easy question for you.</h3>
        <h3>Why do you want to become better at studying?</h3>
        <div class="highlighted-link">
            <a href="<?php print $view['router']->generate('schedule'); ?>" class="more">Go</a>
        </div>
        <ul class="tab-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
    </div>
</div>

<?php echo $view['actions']->render(new ControllerReference('Course1Bundle:Lesson1:wizard', ['_step' => 3, '_format' => 'tab']), ['strategy' => 'sinclude']); ?>

<?php echo $view['actions']->render(new ControllerReference('Course1Bundle:Lesson1:wizard', ['_step' => 2, '_format' => 'tab']), ['strategy' => 'sinclude']); ?>

<?php echo $view['actions']->render(new ControllerReference('Course1Bundle:Lesson1:wizard', ['_step' => 1, '_format' => 'tab']), ['strategy' => 'sinclude']); ?>

<?php echo $view['actions']->render(new ControllerReference('Course1Bundle:Lesson1:wizard', ['_step' => 0, '_format' => 'tab']), ['strategy' => 'sinclude']); ?>

<?php $view['slots']->stop(); ?>

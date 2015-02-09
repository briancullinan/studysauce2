<?php
use Symfony\Component\HttpKernel\Controller\ControllerReference;

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('userBody'); ?>
    <div class="panel-pane" id="uid-<?php print $user->getId(); ?>">
        <?php
        echo $view['actions']->render(new ControllerReference('StudySauceBundle:Metrics:partner', ['_user' => $user->getId(), '_format' => 'tab']));
        echo $view['actions']->render(new ControllerReference('StudySauceBundle:Goals:partner', ['_user' => $user->getId(), '_format' => 'tab']));
        echo $view['actions']->render(new ControllerReference('StudySauceBundle:Deadlines:partner', ['_user' => $user->getId(), '_format' => 'tab']));
        echo $view['actions']->render(new ControllerReference('StudySauceBundle:File:partner', ['_user' => $user->getId(), '_format' => 'tab']));
        echo $view['actions']->render(new ControllerReference('StudySauceBundle:Plan:partner', ['_user' => $user->getId(), '_format' => 'tab']));
        ?>
    </div>
<?php $view['slots']->stop();

$view['slots']->start('body');

$view['slots']->stop();

$view['slots']->start('stylesheets');

$view['slots']->stop();

$view['slots']->start('javascripts'); ?>
<script type="text/javascript">
    $(document).ready(function () {
        var body = $('body');

        // activate all panels because they show up within the tab
        body.on('show', '.panel-pane[id^="uid-"]', function (evt) {
            if($(evt.target).is('[id^="uid-"]'))
                $(this).find('.panel-pane').not(this).trigger('show');
        });
    });
</script>
<?php $view['slots']->stop();

$view['slots']->start('sincludes');
$view['slots']->output('userBody');
$view['slots']->stop();
<?php
use Symfony\Component\HttpKernel\Controller\ControllerReference;

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('userBody'); ?>
    <div class="panel-pane" id="uid-<?php print $user->getId(); ?>">
        <?php
        echo $view['actions']->render(new ControllerReference('StudySauceBundle:Metrics:partner', ['_user' => $user->getId(), '_format' => 'tab']),['strategy' => $tab == 'metrics' ? 'inline' : 'sinclude']);
        echo $view['actions']->render(new ControllerReference('StudySauceBundle:Goals:partner', ['_user' => $user->getId(), '_format' => 'tab']),['strategy' => $tab == 'goals' ? 'inline' : 'sinclude']);
        echo $view['actions']->render(new ControllerReference('StudySauceBundle:Deadlines:partner', ['_user' => $user->getId(), '_format' => 'tab']),['strategy' => $tab == 'deadlines' ? 'inline' : 'sinclude']);
        echo $view['actions']->render(new ControllerReference('StudySauceBundle:File:partner', ['_user' => $user->getId(), '_format' => 'tab']),['strategy' => $tab == 'uploads' ? 'inline' : 'sinclude']);
        echo $view['actions']->render(new ControllerReference('StudySauceBundle:Plan:partner', ['_user' => $user->getId(), '_format' => 'tab']),['strategy' => $tab == 'plan' ? 'inline' : 'sinclude']);
        ?>
    </div>
<?php $view['slots']->stop();

$view['slots']->start('body');

$view['slots']->stop();

$view['slots']->start('stylesheets');

$view['slots']->stop();

$view['slots']->start('javascripts');

$view['slots']->stop();

$view['slots']->start('sincludes');
$view['slots']->output('userBody');
$view['slots']->stop();
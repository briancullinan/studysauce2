<?php
use Codeception\TestCase\Cest;
use StudySauce\Bundle\Entity\User;

/** @var User $user */
$user = $app->getUser();

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');
foreach ($view['assetic']->stylesheets(['@AdminBundle/Resources/public/css/validation.css'],[],['output' => 'bundles/admin/css/*.css']) as $url): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts(['@AdminBundle/Resources/public/js/validation.js'],[],['output' => 'bundles/admin/js/*.js']) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('body'); ?>
    <div class="panel-pane" id="validation">
        <div class="pane-content">
            <?php foreach ($suites as $i => $suite) { ?>
                <h2><?php print $suite; ?></h2>
                <hr/>
                <table>
                    <?php foreach ($tests[$suite] as $s => $t) {
                        /** @var Cest $t */
                        ?>
                        <tr class=" suite-<?php print $suite; ?> ">
                        <td><?php print $t->getName(); ?></td>
                        <td></td>
                        <td><a href="#run-test">Run</a></td>
                        </tr>
                    <?php } ?>
                </table>
            <?php } ?>
        </div>
    </div>
<?php $view['slots']->stop();

$view['slots']->start('sincludes');

$view['slots']->stop();

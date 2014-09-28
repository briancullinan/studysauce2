<?php use Symfony\Component\HttpKernel\Controller\ControllerReference; ?>

<?php $view->extend('StudySauceBundle:Shared:dashboard.html.php') ?>

<?php $view['slots']->start('stylesheets'); ?>
<?php foreach ($view['assetic']->stylesheets([
        '@StudySauceBundle/Resources/public/css/goals.css'
    ], [], ['output' => 'bundles/studysauce/css/*.css']) as $url):
    ?><link rel="stylesheet" href="<?php echo $view->escape($url) ?>" />
<?php endforeach; ?>
<?php $view['slots']->stop() ?>

<?php $view['slots']->start('javascripts'); ?>
<?php foreach ($view['assetic']->javascripts([
        '@StudySauceBundle/Resources/public/js/goals.js'
    ], [], ['output' => 'bundles/studysauce/js/*.js']) as $url):
    ?><script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach; ?>
<?php $view['slots']->stop() ?>

<?php $view['slots']->start('body'); ?>

<?php echo $view->render('StudySauceBundle:Goals:tab.html.php'); ?>

<?php echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:achievement', ['id' => 'claim']), ['strategy' => 'sinclude']); ?>

<?php $view['slots']->stop(); ?>

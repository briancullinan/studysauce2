<?php use Symfony\Component\HttpKernel\Controller\ControllerReference;

/** @var $view \Symfony\Bundle\FrameworkBundle\Templating\PhpEngine */
?>

<?php $view->extend('StudySauceBundle:Shared:dashboard.html.php') ?>

<?php $view['slots']->start('stylesheets'); ?>
<?php foreach ($view['assetic']->stylesheets([
        '@StudySauceBundle/Resources/public/css/schedule.css'
    ], [], ['output' => 'bundles/studysauce/css/*.css']) as $url):
    ?><link rel="stylesheet" href="<?php echo $view->escape($url) ?>" />
<?php endforeach; ?>
<?php $view['slots']->stop() ?>

<?php $view['slots']->start('javascripts'); ?>
<?php foreach ($view['assetic']->javascripts([
        '@StudySauceBundle/Resources/public/js/schedule.js'
    ], [], ['output' => 'bundles/studysauce/js/*.js']) as $url):
    ?><script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach; ?>
<?php $view['slots']->stop() ?>

<?php $view['slots']->start('body'); ?>

<?php echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:building', ['id' => 'building']), ['strategy' => 'sinclude']); ?>

<?php echo $view->render('StudySauceBundle:Schedule:tab.html.php', [
        'schedule' => $schedule,
        'demo' => $demo,
        'csrf_token' => $csrf_token,
        'demoCourses' => $demoCourses,
        'courses' => $courses,
        'others' => $others,
        'demoOthers' => $demoOthers
    ]); ?>

<?php $view['slots']->stop(); ?>

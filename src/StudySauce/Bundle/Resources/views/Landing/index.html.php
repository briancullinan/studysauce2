<?php use Symfony\Component\HttpKernel\Controller\ControllerReference; ?>

<?php $view['slots']->start('classes') ?>landing-home<?php $view['slots']->stop() ?>

<?php $view['slots']->start('stylesheets') ?>
<?php foreach ($view['assetic']->stylesheets([
    '@StudySauceBundle/Resources/public/css/video.css',
    '@StudySauceBundle/Resources/public/css/scr.css',
    '@StudySauceBundle/Resources/public/css/banner.css',
    '@StudySauceBundle/Resources/public/css/features.css',
    '@StudySauceBundle/Resources/public/css/testimony.css',
    '@StudySauceBundle/Resources/public/css/footer.css'
], [], ['output' => 'bundles/studysauce/css/*.css']) as $url):
    ?><link rel="stylesheet" href="<?php echo $view->escape($url) ?>" />
<?php endforeach; ?>
<?php $view['slots']->stop() ?>


<?php $view['slots']->start('javascripts') ?>
<?php foreach ($view['assetic']->javascripts([
    '@StudySauceBundle/Resources/public/js/sauce.js',
    '@StudySauceBundle/Resources/public/js/contact.js'
], [], ['output' => 'bundles/studysauce/js/*.js']) as $url):
    ?><script src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach; ?>
<?php $view['slots']->stop() ?>

<?php $view->extend('StudySauceBundle:Shared:layout.html.php') ?>

<?php $view['slots']->start('body'); ?>

<?php echo $view['actions']->render(new ControllerReference('StudySauceBundle:Landing:video')); ?>

<?php echo $view['actions']->render(new ControllerReference('StudySauceBundle:Landing:scr'), ['strategy' => 'sinclude']); ?>

<?php echo $view['actions']->render(new ControllerReference('StudySauceBundle:Landing:banner'), ['strategy' => 'sinclude']); ?>

<?php echo $view['actions']->render(new ControllerReference('StudySauceBundle:Landing:features'), ['strategy' => 'sinclude']); ?>

<?php echo $view['actions']->render(new ControllerReference('StudySauceBundle:Landing:testimony'), ['strategy' => 'sinclude']); ?>

<?php echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:contact', ['id' => 'contact-support']), ['strategy' => 'sinclude']); ?>

<?php echo $view->render('StudySauceBundle:Shared:footer.html.php'); ?>

<?php $view['slots']->stop(); ?>

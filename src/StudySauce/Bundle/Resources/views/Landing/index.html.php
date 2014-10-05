<?php use Symfony\Component\HttpKernel\Controller\ControllerReference;

 $view['slots']->start('classes') ?>landing-home<?php $view['slots']->stop();

 $view['slots']->start('stylesheets');

 foreach ($view['assetic']->stylesheets([
    '@StudySauceBundle/Resources/public/css/video.css',
    '@StudySauceBundle/Resources/public/css/scr.css',
    '@StudySauceBundle/Resources/public/css/banner.css',
    '@StudySauceBundle/Resources/public/css/features.css',
    '@StudySauceBundle/Resources/public/css/testimony.css',
    '@StudySauceBundle/Resources/public/css/footer.css'
], [], ['output' => 'bundles/studysauce/css/*.css']) as $url):
    ?><link rel="stylesheet" href="<?php echo $view->escape($url) ?>" />
<?php endforeach;

 $view['slots']->stop();

 $view['slots']->start('javascripts');

 foreach ($view['assetic']->javascripts([
    '@StudySauceBundle/Resources/public/js/sauce.js',
    '@StudySauceBundle/Resources/public/js/contact.js'
], [], ['output' => 'bundles/studysauce/js/*.js']) as $url):
    ?><script src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;

 $view['slots']->stop();

 $view->extend('StudySauceBundle:Shared:layout.html.php');

 $view['slots']->start('body');

 echo $view['actions']->render(new ControllerReference('StudySauceBundle:Landing:video'));

 echo $view['actions']->render(new ControllerReference('StudySauceBundle:Landing:scr'), ['strategy' => 'sinclude']);

 echo $view['actions']->render(new ControllerReference('StudySauceBundle:Landing:banner'), ['strategy' => 'sinclude']);

 echo $view['actions']->render(new ControllerReference('StudySauceBundle:Landing:features'), ['strategy' => 'sinclude']);

 echo $view['actions']->render(new ControllerReference('StudySauceBundle:Landing:testimony'), ['strategy' => 'sinclude']);

 echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:contact'), ['strategy' => 'sinclude']);

 echo $view->render('StudySauceBundle:Shared:footer.html.php');

 $view['slots']->stop(); ?>

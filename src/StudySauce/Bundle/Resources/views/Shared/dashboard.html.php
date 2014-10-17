
<?php
/** @var $view \Symfony\Bundle\FrameworkBundle\Templating\PhpEngine */
/** @var $app \Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables */

if($app->getRequest()->get('_format') == 'index' || $app->getRequest()->get('_format') == 'funnel') {
    $view->extend('StudySauceBundle:Shared:layout.html.php');

    if($app->getRequest()->get('_format') == 'index') {
        $view['slots']->start('classes'); ?>dashboard-home<?php $view['slots']->stop();
    }
    else {
        $view['slots']->start('classes'); ?>dashboard-home funnel<?php $view['slots']->stop();
    }

    $view['slots']->start('tmp-stylesheets');
    $view['slots']->output('stylesheets');
    $view['slots']->stop();
    $view['slots']->start('stylesheets');
    foreach ($view['assetic']->stylesheets(
        [
            '@StudySauceBundle/Resources/public/css/pietimer.css',
            '@StudySauceBundle/Resources/public/css/header.css',
            '@StudySauceBundle/Resources/public/css/menu.css',
            '@StudySauceBundle/Resources/public/css/dashboard.css',
            '@StudySauceBundle/Resources/public/css/footer.css'
        ],
        [],
        ['output' => 'bundles/studysauce/css/*.css']
    ) as $url):
        ?>
        <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>" />
    <?php endforeach;
    $view['slots']->output('tmp-stylesheets');
    $view['slots']->stop();


    if($app->getRequest()->get('_format') != 'funnel') {
        $view['slots']->start('tmp-body');
        $view['slots']->output('body');
        $view['slots']->stop();
        $view['slots']->start('body');
        echo $view->render('StudySauceBundle:Shared:header.html.php');
        echo $view->render('StudySauceBundle:Shared:menu.html.php');
        $view['slots']->output('tmp-body');
        $view['slots']->stop();
    }
    else {
        $view['slots']->start('tmp-body');
        $view['slots']->output('body');
        $view['slots']->stop();
        $view['slots']->start('body');
        echo $view->render('StudySauceBundle:Shared:guest-header.html.php');
        $view['slots']->output('tmp-body');
        $view['slots']->stop();
    }


    if($app->getRequest()->get('_format') != 'funnel') {
        $view['slots']->start('tmp-javascripts');
        $view['slots']->output('javascripts');
        $view['slots']->stop();
        $view['slots']->start('javascripts');
        foreach ($view['assetic']->javascripts(
            [
                '@StudySauceBundle/Resources/public/js/selectize.min.js',
                '@StudySauceBundle/Resources/public/js/jquery.scrollintoview.js',
                '@StudySauceBundle/Resources/public/js/jquery.pietimer.js',
                '@StudySauceBundle/Resources/public/js/jquery.plugin.js',
                '@StudySauceBundle/Resources/public/js/jquery.timeentry.js',
                '@StudySauceBundle/Resources/public/js/jquery.jplayer.min.js',
                '@StudySauceBundle/Resources/public/js/sauce.js',
                '@StudySauceBundle/Resources/public/js/dashboard.js',
                '@StudySauceBundle/Resources/public/js/contact.js'
            ],
            [],
            ['output' => 'bundles/studysauce/js/*.js']
        ) as $url):
            ?>
            <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
        <?php endforeach;
        $view['slots']->output('tmp-javascripts');
        $view['slots']->stop();
    }
    else
    {
        $view['slots']->start('tmp-javascripts');
        $view['slots']->output('javascripts');
        $view['slots']->stop();
        $view['slots']->start('javascripts');
        foreach ($view['assetic']->javascripts(
            [
                '@StudySauceBundle/Resources/public/js/jquery.scrollintoview.js',
                '@StudySauceBundle/Resources/public/js/sauce.js',
                '@StudySauceBundle/Resources/public/js/contact.js'
            ],
            [],
            ['output' => 'bundles/studysauce/js/*.js']
        ) as $url):
            ?>
            <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
        <?php endforeach;
        $view['slots']->output('tmp-javascripts');
        $view['slots']->stop();
    }
}

if($app->getRequest()->get('_format') == 'tab') {
    $view['slots']->output('stylesheets');
    $view['slots']->output('body');
    $view['slots']->output('javascripts');
    $view['slots']->output('sincludes');
}



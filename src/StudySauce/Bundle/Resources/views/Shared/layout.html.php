<!DOCTYPE html>
<html>
<head xmlns:hx="http://purl.org/NET/hinclude">
    <meta name="include_mode" content="async" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php $view['slots']->output('title', 'Study Sauce | Discover the secret sauce to studying') ?></title>

    <?php foreach ($view['assetic']->javascripts(array(
        '@StudySauceBundle/Resources/public/js/jquery-2.1.1.min.js',
        '@StudySauceBundle/Resources/public/js/bootstrap.min.js',
        '@StudySauceBundle/Resources/public/js/hinclude.js'
    ), array(), array('output' => 'bundles/studysauce/js/*.js')) as $url):
        ?><script src="<?php echo $view->escape($url) ?>"></script>
    <?php endforeach; ?>

    <?php $view['slots']->output('javascripts') ?>

    <?php foreach ($view['assetic']->stylesheets(array(
        '@StudySauceBundle/Resources/public/css/bootstrap.min.css',
        '@StudySauceBundle/Resources/public/css/sauce.css',
        '@StudySauceBundle/Resources/public/css/dialog.css',
    ), array(), array('output' => 'bundles/studysauce/css/*.css')) as $url):
        ?><link rel="stylesheet" href="<?php echo $view->escape($url) ?>" />
    <?php endforeach; ?>

    <?php $view['slots']->output('stylesheets') ?>
</head>
<body class="<?php $view['slots']->output('classes') ?>">
<?php $view['slots']->output('body') ?>
</body>
</html>
<?php

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('body'); ?>
<div class="panel-pane" id="error403">
    <div class="pane-content">
        You are already logged in. <a href="<?php print $view['router']->generate('logout'); ?>">Click here to Log Out.</a>
    </div>
</div>
<?php $view['slots']->stop();

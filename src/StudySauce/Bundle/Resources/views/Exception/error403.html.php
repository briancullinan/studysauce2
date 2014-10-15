<?php

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('body'); ?>

<div class="panel-pane" id="goals">

    <div class="pane-content">

        You are already logged in. <a href="<?php print $view['router']->generate('account_logout'); ?>">Click here to Log Out.</a>

    </div>

</div>

<?php $view['slots']->stop();

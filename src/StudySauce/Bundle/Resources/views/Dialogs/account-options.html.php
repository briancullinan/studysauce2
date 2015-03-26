<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
    Log in faster in the future
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
    <div class="social-login">
        <?php $first = true;
        foreach($services as $o => $url) {
            if(!$first) { ?>
                <div class="signup-or"><span>Or</span></div>
            <?php }
            $first = false; ?>
            <a href="<?php print $url; ?>?_target=<?php print $app->getRequest()->getUri(); ?>" class="more">Connect</a>
        <?php } ?>
    </div>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer') ?>
<a href="#close" data-dismiss="modal">No thanks</a>
<?php $view['slots']->stop() ?>


<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

 $view['slots']->start('modal-header') ?>
Tip #2 - Prework study sessions
<?php $view['slots']->stop();

 $view['slots']->start('modal-body') ?>
<p>Prework sessions are extremely important in college as professors expect you to come to class prepared and having completed the assigned readings, etc.</p>
<p>Your study plan allocates time for these prework sessions, so that you can use the class time with your professors more effectively.  During these prework sessions, identify areas of confusion and come to class looking to better understand these areas.</p>
<?php $view['slots']->stop();

 $view['slots']->start('modal-footer') ?>
<ul class="dialog-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
<a href="#plan-intro-3" class="btn btn-primary" data-toggle="modal">Next</a>
<?php $view['slots']->stop() ?>


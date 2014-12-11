<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

 $view['slots']->start('modal-header') ?>
Tip #3 - After class study sessions
<?php $view['slots']->stop();

 $view['slots']->start('modal-body') ?>
<p>It is extremely important to review the material covered in classes within a few hours.  Studies show that you will forget almost everything that you have learned in class if you do not (and you will have to work much harder to study for exams).</p>
<p>Your study plan allocates these sessions after your classes.  During these sessions, review your notes and work through the concepts that you have just learned.  This is also a great time to create flash cards or other tools as preparation for your exams later.</p>
<?php $view['slots']->stop();

 $view['slots']->start('modal-footer') ?>
<ul class="dialog-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
<a href="#plan-intro-4" class="btn btn-primary" data-toggle="modal">Next</a>
<?php $view['slots']->stop() ?>


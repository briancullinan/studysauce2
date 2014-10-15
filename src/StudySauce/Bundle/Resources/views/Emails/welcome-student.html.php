<?php
$view->extend('StudySauceBundle:Emails:layout.html.php');

$view['slots']->start('body'); ?>
Congratulations on taking the first step to improving your study effectiveness!  To get the most out of Study Sauce, we recommend a few key things to do.<br />
<br />
1. Enter your important deadlines and we will send you reminders to stay on track.<br />
<br />
2. Check in when you study and we will guide you through the best study techniques.<br />
<br />
3. Set up study goals and rewards.<br />
<br />
As always, we are happy to help with any questions that you might have.  Just email us at admin@studysauce.com.<br />
<br />
<?php $view['slots']->stop(); ?>

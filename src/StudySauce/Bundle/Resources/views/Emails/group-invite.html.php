<?php
use StudySauce\Bundle\Entity\Group;
/** @var Group $group */
$view->extend('StudySauceBundle:Emails:layout.html.php');

$view['slots']->start('message'); ?>
    You have been invited to Study Sauce by <?php print $group->getDescription(); ?>.  Your account has been prepaid.  Please click the link below to activate your account.<br />
    <br />
    Once you create your account, you will answer a short survey about your study preferences which we will use to build your personalized study tools.<br />
    <br />
    If you have any questions at all during the process, please feel free to contact Stephen Houghton.  I can be reached by email at stephen@studysauce.com or by phone at 480-331-8570.<br />
    <br />
<?php $view['slots']->stop();

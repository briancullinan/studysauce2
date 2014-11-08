<?php
$view->extend('StudySauceBundle:Emails:layout.html.php');

$view['slots']->start('body'); ?>
<p><?php
foreach($properties as $i => $prop)
{
    print $i . ' = ' . $prop . '<br />';
}
?></p>
<?php $view['slots']->stop(); ?>

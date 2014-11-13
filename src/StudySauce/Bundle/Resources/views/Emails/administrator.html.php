<?php
$view->extend('StudySauceBundle:Emails:layout.html.php');

$view['slots']->start('message'); ?>
<p><?php
foreach($properties as $i => $prop)
{
    if(is_string($prop) || is_object($prop))
        print $i . ' = ' . $prop . '<br />';
}
?></p>
<?php $view['slots']->stop(); ?>

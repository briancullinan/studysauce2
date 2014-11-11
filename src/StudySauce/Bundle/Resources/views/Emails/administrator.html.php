<?php
$view->extend('StudySauceBundle:Emails:layout.html.php');

$view['slots']->start('message'); ?>
<p><?php
foreach($properties as $i => $prop)
{
    try {
        print $i . ' = ' . $prop . '<br />';
    }
    catch(\Exception $x)
    {

    }
}
?></p>
<?php $view['slots']->stop(); ?>

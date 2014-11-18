<?php
$view->extend('StudySauceBundle:Emails:layout.html.php');

$view['slots']->start('message'); ?>
<p><?php
foreach($properties as $i => $prop)
{
    try {
        print $i . ' = ' . var_export($prop, true) . '<br />';
    } catch (\Exception $ex) {

    }
}
?></p>
<?php $view['slots']->stop(); ?>

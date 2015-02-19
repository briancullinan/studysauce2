<?php use StudySauce\Bundle\Controller\CalcController;

$view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Grade Scale at your school
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<ul class="nav nav-tabs">
    <?php
    $first = true;
    foreach(array_keys(CalcController::$presets) as $k) { ?>
        <li class="<?php print ($first ? 'active' : ''); ?>"><a href="#scale-preset"><?php print $k; ?></a></li>
    <?php $first = false; } ?>
</ul>
<table>
    <thead>
    <tr>
        <th></th>
        <th>High</th>
        <th>Low</th>
        <th>Grade point</th>
    </tr>
    </thead>
    <tbody>
    <?php
    if(empty($scale) || !is_array($scale) || count($scale[0]) < 4)
        $scale = CalcController::$presets['A +/-'];
    for($i = 0; $i < 15; $i++) {
        if (empty($scale[$i]) || empty($scale[$i][3])) { ?>
            <tr>
                <td><label class="input"><input type="text" value=""/></label></td>
                <td><label class="input"><input type="text" value=""/></label></td>
                <td><label class="input"><input type="text" value=""/></label></td>
                <td><label class="input"><input type="text" value=""/></label></td>
            </tr>
        <?php } else { ?>
            <tr>
                <td><label class="input"><input type="text" value="<?php print $scale[$i][0]; ?>"/></label></td>
                <td><label class="input"><input type="text" value="<?php print $scale[$i][1]; ?>"/></label></td>
                <td><label class="input"><input type="text" value="<?php print $scale[$i][2]; ?>"/></label></td>
                <td><label class="input"><input type="text"
                                                value="<?php print number_format($scale[$i][3], 2); ?>"/></label></td>
            </tr>
        <?php }
    } ?>
    </tbody>
</table>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer') ?>
<a href="#save-scale" data-dismiss="modal" class="btn btn-primary">Save</a>
<?php $view['slots']->stop() ?>


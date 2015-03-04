<?php use StudySauce\Bundle\Entity\Course;
use StudySauce\Bundle\Entity\Schedule;

$view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header'); ?>
Manage past and current terms.
<?php $view['slots']->stop();

$view['slots']->start('modal-body'); ?>
<div class="highlighted-link">
    <a href="#add-term" class="big-add">Add <span>+</span> a term</a>
</div>
<?php
$count = 0;
foreach ($schedules as $s) {
    /** @var Schedule $s */
    ?>
    <div class="term-row schedule-id-<?php print $s->getId(); ?> <?php print ($count == 0 ? 'selected' : ''); ?>">
        <div class="term-count"><?php print $count + 1; ?>.</div>
        <div class="term-name"><label class="input"><select>
            <?php for ($y = intval(date('Y')); $y > intval(date('Y')) - 20; $y--) {
                foreach ([11 => 'Winter', 8 => 'Fall', 1 => 'Spring', 6 => 'Summer'] as $m => $t) {
                    ?>
                    <option value="<?php print $m; ?>/<?php print $y; ?>" <?php
                    print (!empty($s->getTerm()) && $s->getTerm()->format('n/Y') == $m . '/' . $y
                        ? 'selected="selected"'
                        : ''); ?>><?php
                    print $t; ?> <?php print $y; ?></option><?php
                }
            }
            ?></select></label></div>
        <a href="#remove-term"></a>
    </div>
    <?php
    $count++;
}
$view['slots']->stop();

$view['slots']->start('modal-footer') ?>
<a href="#close" class="btn" data-dismiss="modal">Cancel</a>
<a href="#save-schedule" class="btn btn-primary" data-dismiss="modal">Done</a>
<?php $view['slots']->stop() ?>


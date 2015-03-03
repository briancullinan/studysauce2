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
    $name = '';
    if(empty($s->getCreated())) {
        $start = new \DateTime();
    }
    elseif (empty($s->getClasses()->count())) {
        $start = $s->getCreated();
    } else {
        $start = date_timestamp_set(
            new \DateTime(),
            array_sum(
                $s->getClasses()->map(
                    function (Course $c) {
                        return empty($c->getStartTime()) ? 0 : $c->getStartTime()->getTimestamp();
                    }
                )->toArray()
            ) / $s->getClasses()->count()
        );
    }
    if ($start->format('n') >= 11) {
        $name = 'Winter ' . $start->format('Y');
    } elseif ($start->format('n') >= 8) {
        $name = 'Fall ' . $start->format('Y');
    } elseif ($start->format('n') <= 5) {
        $name = 'Spring ' . $start->format('Y');
    } else {
        $name = 'Summer ' . $start->format('Y');
    }
    ?>
    <div class="term-row schedule-id-<?php print $s->getId(); ?> <?php print ($count == 0 ? 'selected' : ''); ?>">
        <div class="term-count"><?php print $count + 1; ?>.</div>
        <div class="term-name"><label class="input"><select>
            <?php for ($y = intval(date('Y')); $y > intval(date('Y')) - 20; $y--) {
                foreach ([11 => 'Winter', 8 => 'Fall', 1 => 'Spring', 6 => 'Summer'] as $m => $t) {
                    ?>
                    <option value="<?php print $m; ?>/<?php print $y; ?>" <?php
                    print ($name == $t . ' ' . $y ? 'selected="selected"' : ''); ?>><?php
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
<a href="#save-schedule" class="btn btn-primary">Done</a>
<?php $view['slots']->stop() ?>


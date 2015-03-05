<?php use StudySauce\Bundle\Entity\Course;
use StudySauce\Bundle\Entity\Schedule;

$view->extend('StudySauceBundle::Dialogs/dialog.html.php');
/** @var $schedule Schedule */

$view['slots']->start('modal-header'); ?>
Save your old schedule
<?php $view['slots']->stop();

$view['slots']->start('modal-body'); ?>
Save the previous term as:
<label class="input"><select>
        <?php for ($y = intval(date('Y')); $y > intval(date('Y')) - 20; $y--) {
            foreach ([11 => 'Winter', 8 => 'Fall', 1 => 'Spring', 6 => 'Summer'] as $m => $t) {
                ?>
                <option value="<?php print $m; ?>/<?php print $y; ?>" <?php
                print (!empty($schedule->getTerm()) && $schedule->getTerm()->format('n/Y') == $m . '/' . $y
                    ? 'selected="selected"'
                    : ''); ?>><?php
                print $t; ?> <?php print $y; ?></option><?php
            }
        }
        ?></select></label>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer') ?>
<a href="#close" class="btn" data-dismiss="modal">Cancel</a>
<a href="#create-schedule" class="btn btn-primary" data-dismiss="modal">Done</a>
<?php $view['slots']->stop() ?>


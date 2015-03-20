<?php
use StudySauce\Bundle\Entity\User;

/** @var User $user */
$user = $app->getUser();

$view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
    Did you study outside Study Sauce?
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
    No problem. Add your hours below.
    <form action="<?php print $view['router']->generate('contact_parents'); ?>" method="post">
        <div class="class-name">
            <label class="input"><span>Class</span><select>
                    <option></option>
            </select></label>
        </div>
        <div class="date">
            <label class="input"><span>Date</span><input type="text" value=""></label>
        </div>
        <div class="time">
            <label class="input"><span>Time (min)</span><input type="email" value=""></label>
        </div>
        <div class="highlighted-link invalid">
            <div style="float:left;">* Research shows you shouldn't studying longer than 60 minutes without a break.</div>
            <button type="submit" value="#submit-contact" class="more">Save</button>
        </div>
    </form>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer');
$view['slots']->stop();

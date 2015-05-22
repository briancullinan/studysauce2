<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Event settings
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<div class="start-time">
    <label class="input">
        <span>Time</span>
        <input type="text" placeholder="Start" title="What time does your class begin?"
               autocomplete="off"
               value="">
    </label>
    <label class="input mobile-only">
        <span>Time</span>
        <input type="time" title="What time does your class begin?"
               autocomplete="off"
               value="">
    </label>
</div>
<div class="end-time">
    <label class="input">
        <span>&nbsp;</span>
        <input type="text" placeholder="End" title="What time does your class end?" autocomplete="off"
               value="">
    </label>
    <label class="input mobile-only">
        <span>&nbsp;</span>
        <input type="time" title="What time does your class end?" autocomplete="off"
               value="">
    </label>
</div>
<div class="day-of-the-week">
    <label class="checkbox"><span>M</span>
        <input type="checkbox" value="M"><i></i></label>
    <label class="checkbox"><span>Tu</span>
        <input type="checkbox" value="Tu"><i></i></label>
    <label class="checkbox"><span>W</span>
        <input type="checkbox" value="W"><i></i></label>
    <label class="checkbox"><span>Th</span>
        <input type="checkbox" value="Th"><i></i></label>
    <label class="checkbox"><span>F</span>
        <input type="checkbox" value="F"><i></i></label>
    <label class="checkbox"><span>Sa</span>
        <input type="checkbox" value="Sa"><i></i></label>
    <label class="checkbox"><span>Su</span>
        <input type="checkbox" value="Su"><i></i></label>
</div>
<div class="start-date">
    <label class="input">
        <span>Date</span>
        <input type="text" placeholder="First class" title="What day does your academic term begin?"
               autocomplete="off"
               value="">
    </label>
    <label class="input mobile-only">
        <span>Date</span>
        <input type="date" placeholder="First class" title="What day does your academic term begin?"
               autocomplete="off"
               value="">
    </label>
</div>
<div class="end-date">
    <label class="input">
        <span>&nbsp;</span>
        <input type="text" placeholder="Last class" title="What day does your academic term end?"
               autocomplete="off"
               value="">
    </label>
    <label class="input mobile-only">
        <span>&nbsp;</span>
        <input type="date" placeholder="Last class" title="What day does your academic term end?"
               autocomplete="off"
               value="">
    </label>
</div>
<div class="location">
    <label class="input">
        <span>Location</span>
        <input type="text" placeholder="Location" title="Where will you be studying?"
               autocomplete="off"
               value="">
    </label>
</div>
<div class="title">
    <label class="input">
        <span>Title</span>
        <input type="text" placeholder="Title" title="What do you want to call this event?"
               autocomplete="off"
               value="">
    </label>
</div>
<div class="reminder">
    <label class="input">
        <span>Alert</span>
        <select name="spaced-alert">
            <option value="0">No alert</option>
            <option value="15">15 min</option>
            <option value="30">30 min</option>
            <option value="60">1 hour</option>
        </select>
    </label>
</div>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer') ?>

<?php $view['slots']->stop() ?>


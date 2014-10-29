
<div class="field-select-strategy">
    <div>
        <label>Recommended strategy:</label>
        <select name="strategy-select">
            <option value="_none" selected="selected">- Change strategy -</option>
            <option value="teach">Teach</option>
            <option value="spaced">Spaced repetition</option>
            <option value="active">Active reading</option>
            <option value="prework">Prework</option>
        </select>
    </div>
</div>

<?php
echo $view->render('StudySauceBundle:Plan:strategy-active.html.php');

echo $view->render('StudySauceBundle:Plan:strategy-other.html.php');

echo $view->render('StudySauceBundle:Plan:strategy-prework.html.php');

echo $view->render('StudySauceBundle:Plan:strategy-spaced.html.php');

echo $view->render('StudySauceBundle:Plan:strategy-teach.html.php');

echo $view->render('StudySauceBundle:Checkin:mini-checkin.html.php');

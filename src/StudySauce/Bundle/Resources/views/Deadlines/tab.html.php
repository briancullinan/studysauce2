<?php use StudySauce\Bundle\Entity\Deadline;

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

 $view['slots']->start('stylesheets');

 foreach ($view['assetic']->stylesheets([
        '@StudySauceBundle/Resources/public/css/deadlines.css'
    ], [], ['output' => 'bundles/studysauce/css/*.css']) as $url):
    ?><link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>" />
<?php endforeach;

 $view['slots']->stop();

 $view['slots']->start('javascripts');

 foreach ($view['assetic']->javascripts([
        '@StudySauceBundle/Resources/public/js/deadlines.js'
    ], [], ['output' => 'bundles/studysauce/js/*.js']) as $url):
    ?><script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;

 $view['slots']->stop();

 $view['slots']->start('body'); ?>

<div class="panel-pane" id="deadlines">

    <div class="pane-content">

        <h2>Enter important dates and we will send you email reminders</h2>

        <div class="highlighted-link form-actions">
            <a href="#add-deadline">Add <span>+</span> class</a>
            <a href="#save-deadline" class="more">Save</a>
        </div>

        <div class="sort-by">
            <label>Sort by: </label>
            <label class="radio"><input type="radio" name="deadlines-sort" value="date" checked="checked" /><i></i>Date</label>
            <label class="radio"><input type="radio" name="deadlines-sort" value="class"><i></i>Class</label>
            <label class="checkbox" title="Click here to see deadlines that have already passed."><input type="checkbox"><i></i>Past deadlines</label>
        </div>

        <header>
            <label>&nbsp;</label>
            <label>Assignment</label>
            <label>Reminders</label>
            <label>Due date</label>
            <label>% of grade</label>
        </header>

        <?php
        $isDemo = false;
        if(empty($deadlines))
        {
            $isDemo = true;
            $deadlines = $demoDeadlines;
        }
        $headStr = '';
        foreach($deadlines as $i => $d) {
            /** @var $d Deadline */
            $time = $d->getDueDate();
            $newHeadStr = $time->format('j F') . ' <span>' . $time->format('Y') . '</span>';
            if ($headStr != $newHeadStr) {
                $headStr = $newHeadStr;
                $classes = [];
                if($time < date_add(new \Datetime(), new \DateInterval('P1D')))
                    $classes[] = 'hide';
                print '<div class="head ' . implode(' ', $classes) . '">' . $headStr . '</div>';
            }
            if ($d->getReminder() != null) {
                $reminders = array_map(function ($x) {return intval($x);}, explode(',', $d->getReminder()));
            } else {
                $reminders = [];
            }
            $classI = array_search($d->getName(), array_values($courses));
            ?><div class="deadline-row first valid read-only">
                <div class="class-name">
                    <label class="select">
                        <span>Class name</span>
                        <select>
                            <option value="_none">- Select a class -</option>
                            <option value="CHEM 101">CHEM 101</option>
                            <option value="HIST 101">HIST 101</option>
                            <option value="STAT 101" selected="">STAT 101</option>
                            <option value="PHYS 101">PHYS 101</option>
                            <option value="SPAN 101">SPAN 101</option>
                            <option value="ECON 101">ECON 101</option>
                            <option value="Nonacademic">Nonacademic</option>
                        </select>
                    </label>
                </div>
                <div class="assignment">
                    <label class="select">
                        <span>Assignment</span>
                        <input placeholder="Paper, exam, project, etc." type="text" value="Group project" size="60" maxlength="255">
                    </label>
                </div>
                <div class="reminder">
                    <label>Reminders</label>
                    <label class="checkbox"><input type="checkbox" value="1209600" checked="checked"><i></i><br/>2 wk</label>
                    <label class="checkbox"><input type="checkbox" value="604800"><i></i><br/>1 wk</label>
                    <label class="checkbox"><input type="checkbox" value="345600"><i></i><br/>4 days</label>
                    <label class="checkbox"><input type="checkbox" value="172800"><i></i><br/>2 days</label>
                    <label class="checkbox"><input type="checkbox" value="86400"><i></i><br/>1 day</label>
                </div>
                <div class="due-date">
                    <label class="input">
                        <span>Due date</span>
                        <input placeholder="Enter date" type="text" value="06/01/2014" size="5" maxlength="255">
                    </label>
                </div>
                <div class="percent">
                    <label class="input">
                        <span>% of grade</span>
                        <input type="text" value="15%" size="2" maxlength="255">
                    </label>
                </div>
                <div class="read-only">
                    <a href="#edit-deadline">&nbsp;</a><a href="#remove-deadline">&nbsp;</a>
                </div>
            </div>
        <?php } ?>

        <div class="highlighted-link form-actions">
            <a href="<?php print $view['router']->generate('schedule'); ?>">Edit schedule</a><a href="#save-deadline" class="more">Save</a>
        </div>

    </div>

</div>

<?php $view['slots']->stop(); ?>

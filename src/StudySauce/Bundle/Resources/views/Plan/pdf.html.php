<?php
use StudySauce\Bundle\Controller\PlanController;
use StudySauce\Bundle\Entity\Course;
use StudySauce\Bundle\Entity\Schedule;
use StudySauce\Bundle\Entity\Event;

/** @var Schedule $schedule */
?>
<html>
<head>
    <?php
    foreach ($view['assetic']->stylesheets(
        ['@StudySauceBundle/Resources/public/css/fonts.css',],
        [],
        ['output' => 'bundles/studysauce/css/*.css']
    ) as $url): ?>
        <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
    <?php endforeach; ?>
    <style>
        @media print {
            @page {
                size: landscape
            }
        }

        html, body {
            font-family: 'Sintony', sans-serif;
            line-height: 140%;
            outline: 0 none;
            position: relative;
            left: 0;
            color: #555;
            font-size: 12px;;
        }

        body {
            padding: 5px;
        }

        table {
            width: 100%;
            table-layout: fixed;
            font-weight: inherit;
            height: 6.5in;
            font-size: inherit;
            border-collapse: collapse;
        }

        table td {
            position: relative;
            border: .035in solid #555;
            padding: 0 5px;
            margin-top: 15px;
            vertical-align: top;
        }

        table td.prev h3,
        table td.next h3 {
            color: #ccc;
        }

        .month-view table td:first-child,
        .month-view table td:last-child,
        .week-view table td.gray,
        .week-view table td.gray {
            background-color: #EEE;
        }

        table tr {
            page-break-inside: avoid;
        }

        table thead tr {
            height: 40px;
            background-color: #fff;
            position: relative;
            z-index: 100;
        }

        .month-view table tbody.row-count-4 tr {
            height: 25%;
        }

        .month-view table tbody.row-count-5 tr {
            height: 20%;
        }

        .month-view table tbody.row-count-6 tr {
            height: 16.66%;
        }

        .month-view table tbody.row-count-7 tr {
            height: 14.28%;
        }

        .month-view table tbody td {
            padding-top: 25px;
        }

        .week-view table tbody tr:first-child td {
            padding-top: 25px;
            border-top: .035in solid #555;
        }

        .week-view table tbody tr:last-child td,
        .week-view table tbody tr td[rowspan] {
            border-bottom: .035in solid #555;
            height: 100%;
            padding: 0;
        }

        .week-view table tbody tr td {
            border-top: 0;
            border-bottom: 0;
        }

        .week-view table tbody tr {
            vertical-align: middle;
        }

        .month-view table tbody tr {
            min-height: 100px;
        }

        .week-view table tbody tr.deadlines {
            height: 40px;
            min-height: 0;
        }

        .week-view table tbody tr.deadlines td {
            border-bottom: .035in solid #555;
        }

        .deadlines .class-row {
            color: #FF1100;
        }

        h1 {
            font-family: 'Ubuntu', Arial, sans-serif;
            color: #F90;
            font-size: 32px;
            width: 50%;
            display: inline-block;
            margin: 0 -2px 10px -2px;
            text-align: right;
            vertical-align: middle;
            line-height: 40px;
        }

        h1 * {
            vertical-align: middle;
        }

        h2 {
            font-size: 32px;
            width: 50%;
            display: inline-block;
            margin: 0 -2px 10px -2px;
            line-height: 40px;
            vertical-align: middle;
            text-align: left;
        }

        h1 img {
            width: 38px;
            height: 40px;
            margin: 0 5px;
            line-height: 40px;
            vertical-align: middle;
            background-color: white;
        }

        h1 span {
            font-weight: normal;
        }

        h3 {
            position: absolute;
            top: -1px;
            right: 1px;
            margin: 0;
            font-size: 16px;
        }

        h4 {
            position: absolute;
            top: 50%;
            right: 0;
            margin-top: -10px;
            margin-bottom: 0;
        }

        .class-row > * {
            vertical-align: middle;
        }

        .class-row {
            position: relative;
            page-break-inside: avoid;
            padding: 5px 5px 5px 20px;
        }

        .class-row i {
            width: 10px;
            height: 10px;
            border: 2px solid #555;
            border-radius: 100%;
            display: inline-block;
            position: absolute;
            top: 5px;
            left: 0;
        }

        .page {
            page-break-inside: avoid;
            page-break-after: always;
            page-break-before: auto;
            overflow: visible !important;
        }

        .page:last-child {
            page-break-after: auto;
        }

        .class {
            background-color: #555555;
        }

        .class0 {
            background-color: #FF1100;
        }

        .class1 {
            background-color: #FF9900;
        }

        .class2 {
            background-color: #FFDD00;
        }

        .class3 {
            background-color: #BBEE00;
        }

        .class4 {
            background-color: #33DD00;
        }

        .class5 {
            background-color: #009999;
        }

        .class6 {
            background-color: #1133AA;
        }

        .class7 {
            background-color: #6611AA;
        }

        .class8 {
            background-color: #BB0088;
        }

        .event-type-o .class {
            background-color: #000;
        }

    </style>
</head>

<body>
<?php
// organize events by day
$days = [];
foreach ($schedule->getEvents()->toArray() as $e) {
    /** @var Event $e */
    if (empty($e->getDeadline())) {
        continue;
    }
    $days[$e->getStart()->format('Y/m/d')][] = $e;
}

$scheduleStart = date_timestamp_set(
    new \DateTime(),
    min(
        $schedule->getClasses()->map(
            function (Course $c) {
                return $c->getStartTime()->getTimestamp();
            }
        )->toArray()
    )
);
$scheduleEnd = date_timestamp_set(
    new \DateTime(),
    max(
        $schedule->getClasses()->map(
            function (Course $c) {
                return $c->getEndTime()->getTimestamp();
            }
        )->toArray()
    )
);
for ($m = $scheduleStart->format('m'); $m <= $scheduleEnd->format('m'); $m++) {
    $month = new \DateTime($scheduleStart->format('Y') . '/' . $m . '/1');
    $monthEnd = strtotime($month->format('Y/m/t'));
    if ($month->format('w') == 0) {
        $firstDay = clone $month;
    } else {
        $firstDay = date_timestamp_set(new \DateTime(), strtotime('last Sunday', $month->getTimestamp()));
    }
    $lastDay = date_timestamp_set(new \DateTime(), strtotime('next Sunday', $monthEnd));
    ?>
    <div class="page month-view">
        <h2><?php print $month->format('F Y'); ?></h2>

        <h1><img width="40" height="40" alt="" src="<?php print $view['router']->generate(
                    '_welcome',
                    [],
                    true
                ) . 'bundles/studysauce'; ?>/images/Study_Sauce_Logo_small.png"><strong>Study </strong><span>Sauce</span>
        </h1>
        <table>
            <thead>
            <tr>
                <th>Sunday</th>
                <th>Monday</th>
                <th>Tuesday</th>
                <th>Wednesday</th>
                <th>Thursday</th>
                <th>Friday</th>
                <th>Saturday</th>
            </tr>
            </thead>
            <?php
            $count = 1;
            $view['slots']->start('month');
            for ($d = $firstDay->getTimestamp();
            $d < $lastDay->getTimestamp();
            $d += 86400) {
            $current = date_timestamp_set(new \DateTime(), $d);
            ?>
            <td class="<?php
            print ($d < $month->getTimestamp() ? ' prev' : '');
            print ($d > $monthEnd ? ' next' : ''); ?>">
                <h3><?php print $current->format('j'); ?></h3>
                <?php
                if (isset($days[$current->format('Y/m/d')])) {
                    foreach ($days[$current->format('Y/m/d')] as $e) {
                        ?>
                        <div class="class-row event-type-<?php print $e->getType(); ?>">
                            <i class="class<?php print (!empty($e->getCourse()) ? $e->getCourse()->getIndex(
                            ) : ''); ?>"></i>
                            <span><?php print $e->getDeadline()->getAssignment(); ?></span>
                        </div>
                        <?php
                    }
                }
                ?>
            </td><?php
            if ($current->format('w') == 6 && $d < $lastDay->getTimestamp() - 86400) {
            $count++;
            ?> </tr>
            <tr> <?php
                }
                }
                $view['slots']->stop();
                ?>
                <tbody class="row-count-<?php print $count; ?>">
            <tr><?php $view['slots']->output('month'); ?></tr>
            </tbody>
        </table>
    </div>

<?php }

// show weekly
$weekStart = clone $scheduleStart;
if ($scheduleStart->format('w') != 0) {
    $weekStart = date_timestamp_set(new \DateTime(), strtotime('last Sunday', $scheduleStart->getTimestamp()));
}
$weekEnd = clone $scheduleEnd;
if ($weekEnd->format('w') != 0) {
    $weekEnd = date_timestamp_set(new \DateTime(), strtotime('next Sunday', $weekEnd->getTimestamp()));
}
$weekDays = [];
$events = $schedule->getEvents()->toArray();
usort($events, function (Event $a, Event $b) {
    return $a->getStart()->getTimestamp() - $b->getStart()->getTimestamp();
});
foreach ($events as $e) {
    if($e->getType() == 'd')
        continue;
    /** @var Event $e */
    $instances = PlanController::getInstances(
        date_timezone_set(clone $e->getStart(), new \DateTimeZone('Z')),
        $e->getRecurrence()
    );

    foreach ($instances as $start) {
        /** @var \DateTime $start */
        $id = $e->getId() . '_' . $start->format('Ymd') . 'T' . $start->format('Hise');

        // check database for event id
        /** @var Event $child */
        if (!empty($child = $schedule->getEvents()->filter(
            function (Event $e) use ($id) {
                return !empty($e->getRecurrence()) && strpos($e->getRecurrence()[0], $id) !== false;
            }
        )->first())
        ) {
            if (!$child->getDeleted()) {
                continue;
            }
            $weekDays[date_timezone_set(
                clone $child->getStart(),
                new \DateTimeZone(date_default_timezone_get())
            )->format('Y/m/d')][$start->format('G')][] = $child;
        } else {
            $weekDays[date_timezone_set(
                clone $start,
                new \DateTimeZone(date_default_timezone_get())
            )->format('Y/m/d')][$start->format('G')][] = $e;
        }
    }
}
for ($j = $weekStart->getTimestamp();
     $j < $weekEnd->getTimestamp();
     $j += 604800) {
    $current = date_timestamp_set(new \DateTime(), $j);
    ?>
    <div class="page week-view">
        <h2><?php print $current->format('j F'); ?> - <?php print date_timestamp_set(
                new \DateTime(),
                $current->getTimestamp() + 604800
            )->format('j F'); ?></h2>

        <h1><img width="38" height="40" alt="" src="<?php print $view['router']->generate(
                    '_welcome',
                    [],
                    true
                ) . 'bundles/studysauce'; ?>/images/Study_Sauce_Logo_small.png"><strong>Study </strong><span>Sauce</span>
        </h1>
        <table>
            <thead>
            <tr>
                <th>Sunday</th>
                <th>Monday</th>
                <th>Tuesday</th>
                <th>Wednesday</th>
                <th>Thursday</th>
                <th>Friday</th>
                <th>Saturday</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $hasDeadlines = false;
            $view['slots']->start('deadlines');
            for ($d = 0; $d < 7; $d++) {
                $weekDay = date_timestamp_set(new \DateTime(), $j + $d * 86400);
                ?>
                <td class="<?php print ($d == 0 || $d == 6 ? 'gray' : ''); ?>">
                    <h3><?php print $weekDay->format('j'); ?></h3>
                    <?php
                    if (!isset($days[$weekDay->format('Y/m/d')])) {
                        continue;
                    }
                    foreach ($days[$weekDay->format('Y/m/d')] as $e) {
                        /** @var Event $e */
                        $exists = true;
                        $hasDeadlines = true;
                        ?>
                        <div class="class-row event-type-<?php print $e->getType(); ?>">
                            <i class="class<?php print (!empty($e->getCourse()) ? $e->getCourse()->getIndex(
                            ) : ''); ?>"></i>
                            <strong><?php print $e->getName(); ?></strong>
                            <?php
                            if (!empty($e->getLocation())) { ?>Location: <?php print $e->getLocation();
                            } ?>
                        </div>
                        <?php
                    } ?>
                </td>
                <?php
            }
            $view['slots']->stop();
            if ($hasDeadlines) { ?>
                <tr class="deadlines">
                    <?php $view['slots']->output('deadlines'); ?>
                </tr>
            <?php }
            $most = max(
                array_map(
                    function ($d) use ($j, $weekDays) {
                        $weekDay = date_timestamp_set(new \DateTime(), $j + $d * 86400);
                        if (!isset($weekDays[$weekDay->format('Y/m/d')])) {
                            return 0;
                        }

                        return count($weekDays[$weekDay->format('Y/m/d')]);
                    },
                    range(0, 7)
                )
            );
            for ($k = 0; $k <= $most; $k++) { ?>
                <tr><?php
                for ($d = 0; $d < 7; $d++) {
                    $weekDay = date_timestamp_set(new \DateTime(), $j + $d * 86400);
                    if (!isset($weekDays[$weekDay->format('Y/m/d')])
                        || count($weekDays[$weekDay->format('Y/m/d')]) <= $k
                    ) {
                        if ((!isset($weekDays[$weekDay->format('Y/m/d')]) && $k == 0)
                            || (isset($weekDays[$weekDay->format('Y/m/d')])
                                && count($weekDays[$weekDay->format('Y/m/d')]) == $k)
                        ) {
                            $cnt = !isset($weekDays[$weekDay->format('Y/m/d')])
                                ? 0
                                : count($weekDays[$weekDay->format('Y/m/d')]);
                            ?>
                            <td class="<?php print ($d == 0 || $d == 6 ? 'gray' : ''); ?>"
                                rowspan="<?php print $most - $cnt + 1; ?>"></td><?php
                        }
                        continue;
                    }
                    ?>
                    <td class="<?php print ($d == 0 || $d == 6 ? 'gray' : ''); ?>">
                    <?php
                    $h = array_keys($weekDays[$weekDay->format('Y/m/d')])[$k];
                    foreach ($weekDays[$weekDay->format('Y/m/d')][$h] as $e) {
                        /** @var Event $e */
                        $exists = true;
                        if (!$hasDeadlines && $k == 0) { ?>
                            <h3><?php print $weekDay->format('j'); ?></h3>
                        <?php } ?>
                        <div class="class-row event-type-<?php print $e->getType(); ?>">
                            <i class="class<?php print (!empty($e->getCourse())
                                ? $e->getCourse()->getIndex()
                                : ''); ?>"></i>
                            <strong><?php print $e->getName() . ($e->getType() == 'c'
                                        ? ' - Class'
                                        : ($e->getType() == 'p'
                                            ? ' - Pre-work'
                                            : ($e->getType() == 'sr'
                                                ? ' - Study'
                                                : ''))); ?></strong><br/>
                            <?php
                            print $e->getStart()->format('H:i'); ?> - <?php
                            print $e->getEnd()->format('H:i');
                            if (!empty($e->getLocation())) {
                                ?>; Location: <?php print $e->getLocation();
                            } ?>
                        </div>
                    <?php } ?></td><?php } ?></tr><?php } ?>
            </tbody>
        </table>
    </div>
<?php } ?>


</body>
</html>
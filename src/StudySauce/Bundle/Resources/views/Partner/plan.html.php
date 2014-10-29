<?php
use Doctrine\Common\Collections\ArrayCollection;
use StudySauce\Bundle\Entity\Course;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use StudySauce\Bundle\Entity\Event;

/** @var ArrayCollection $courses */

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');

foreach ($view['assetic']->stylesheets(
    [
        '@StudySauceBundle/Resources/public/js/fullcalendar/fullcalendar.css'
    ],
    [],
    ['output' => 'bundles/studysauce/js/fullcalendar/*.css']
) as $url):
    ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;

foreach ($view['assetic']->stylesheets(
    [
        '@StudySauceBundle/Resources/public/css/plan.css'
    ],
    [],
    ['output' => 'bundles/studysauce/css/*.css']
) as $url):
    ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts(
    [
        '@StudySauceBundle/Resources/public/js/plan.js',
        '@StudySauceBundle/Resources/public/js/strategies.js',
        '@StudySauceBundle/Resources/public/js/fullcalendar/lib/moment.min.js',
        '@StudySauceBundle/Resources/public/js/fullcalendar/fullcalendar.js'
    ],
    [],
    ['output' => 'bundles/studysauce/js/*.js']
) as $url):
    ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach; ?>
    <script type="text/javascript">
        // convert events array to object to keep track of keys better
        window.planEvents = JSON.parse('<?php print json_encode(array_values($jsonEvents)); ?>');
        // insert strategies
        window.strategies = JSON.parse('<?php print json_encode($strategies); ?>');
    </script>
<?php $view['slots']->stop();

$view['slots']->start('body'); ?>
    <div class="panel-pane" id="plan">
        <div class="pane-content">
            <?php echo $view->render('StudySauceBundle:Partner:partner-nav.html.php', ['user' => $user]); ?>
            <h2>Study schedule</h2>

            <div id="calendar" class="full-only fc fc-ltr fc-unthemed"></div>
            <div class="sort-by clearfix">
                <label>Sort by: </label>
                <label class="radio"><input type="radio" name="plan-sort" value="date"
                                            checked="checked"/><i></i>Date</label>
                <label class="radio"><input type="radio" name="plan-sort" value="class"><i></i>Class</label>
                <a href="#expand">Expand</a>
                <label class="checkbox" title="Click here to see sessions that have already passed.">
                    <input type="checkbox"><i></i>Past session</label>
            </div>
            <?php
            $first = true;
            $headStr = '';
            $startWeek = new \DateTime('last Sunday');
            $endWeek = new \DateTime('next Sunday');
            $yesterday = new \DateTime('yesterday');
            foreach ($events as $i => $event) {
                /** @var Event $event */

                // TODO: should we allow notes for class events?
                if ($event->getType() == 'c' ||
                    $event->getType() == 'h' ||
                    $event->getType() == 'r' ||
                    $event->getType() == 'm' ||
                    $event->getType() == 'z') {
                    continue;
                }


                $newHead = $event->getStart()->format('j F');
                if ($headStr != $newHead) {
                    $headStr = $newHead;
                    ?><div class="head <?php print ($event->getStart() < $yesterday ? ' hide' : '');
                    print ($event->getStart() >= $startWeek && $event->getStart() <= $endWeek ? ' mobile' : ''); ?>">
                    <?php print $headStr; ?>
                    </div><?php
                }

                /** @var Course $course */
                $course = $event->getCourse();
                $classI = array_search($course, $courses->toArray());
                if ($classI === false) {
                    $classI = '';
                }

                $session = '';
                if ($event->getType() == 'd' || empty($course)) {
                    $session = 'other';
                } elseif ($event->getType() == 'p') {
                    $session = 'prework';
                }
                // if no strategy default to sr
                // convert memorization answer to spaced
                elseif (empty($course->getStudyType()) || $course->getStudyType() == 'memorization') {
                    $session = 'spaced';
                } // convert reading answer to active
                elseif ($course->getStudyType() == 'reading') {
                    $session = 'active';
                } // convert conceptual answer to teach
                elseif ($course->getStudyType() == 'conceptual') {
                    $session = 'teach';
                }

                if ($event->getType() == 'd' && !empty($course)) {
                    $title = 'Deadline' . preg_replace(
                            ['/' . preg_quote($course->getName()) . '\s*/'],
                            [],
                            $event->getName()
                        );
                } elseif ($event->getType() == 'd') {
                    $title = 'Deadline' . str_replace('Nonacademic', '', $event->getName());
                } elseif ($event->getType() == 'f') {
                    $cid = 'f';
                    $title = 'Any class needed';
                } elseif ($event->getType() == 'sr') {
                    $title = $session == 'active'
                        ? 'Active reading'
                        : ($session == 'teach'
                            ? 'Teach'
                            : 'Spaced repetition');
                } elseif ($event->getType() == 'p') {
                    $title = 'Pre-work';
                } else {
                    $title = $event->getName();
                }

                ?>
                <div class="session-row <?php
                print ($first && !($first = false) ? ' first' : '');
                print ' event-type-' . $event->getType();
                print ' checkin' . $classI;
                print ($event->getStart() < $yesterday || $event->getDeleted() ? ' hide' : '');
                print ($event->getStart() >= $startWeek && $event->getStart() <= $endWeek ? ' mobile' : '');
                print (!empty($course) ? (' cid' . $course->getId()) : '');
                print (' default-' . $session);
                print ($event->getCompleted() ? ' done' : ''); ?>" id="eid-<?php print $event->getId(); ?>">
                    <div class="class-name">
                        <span class="class<?php print $classI; ?>">&nbsp;</span>
                        <div class="read-only"><?php print $event->getName(); ?></div>
                    </div>
                    <div class="assignment">
                        <div class="read-only"><?php print $title; ?></div>
                    </div>
                    <div class="percent">
                        <div class="read-only"><?php print ($event->getType() == 'd' && $event->getDeadline()->getPercent() ?: '&nbsp;'); ?></div>
                    </div>
                    <div class="completed">
                        <label class="checkbox"><input type="checkbox" name="plan-sort" value="class" <?php
                            print ($event->getCompleted() ? 'checked="checked"' : ''); ?>><i></i></label>
                    </div>
                </div>
            <?php } ?>
            <a class="return-to-top" href="#return-to-top">Top</a>
            <?php echo $view->render('StudySauceBundle:Plan:strategies.html.php'); ?>
        </div>
    </div>
<?php $view['slots']->stop();

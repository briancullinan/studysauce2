<?php
use Doctrine\Common\Collections\ArrayCollection;
use StudySauce\Bundle\Entity\Course;
use StudySauce\Bundle\Entity\User;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use StudySauce\Bundle\Entity\Event;

$isDemo = false;
if(empty($courses) || !$user->hasRole('ROLE_PAID'))
{
    $isDemo = true;
}

/** @var array $courses */
/** @var User $user */

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');

foreach ($view['assetic']->stylesheets(
    [
        '@StudySauceBundle/Resources/public/js/fullcalendar/fullcalendar.min.css'
    ],
    [],
    ['output' => 'bundles/studysauce/js/fullcalendar/*.css']
) as $url):
    ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;

foreach ($view['assetic']->stylesheets(
    [
        '@StudySauceBundle/Resources/public/css/clock.css',
        '@StudySauceBundle/Resources/public/css/checkin.css',
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
foreach ($view['assetic']->javascripts(['@checkin_scripts',],[],['output' => 'bundles/studysauce/js/*.js']) as $url):?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
foreach ($view['assetic']->javascripts(['@plan_scripts',],[],['output' => 'bundles/studysauce/js/*.js']) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
?>
    <script type="text/javascript">
        // convert events array to object to keep track of keys better
        if(typeof(window.planEvents) == 'undefined')
            window.planEvents = [];
        if(typeof(window.planLoaded) == 'undefined')
            window.planLoaded = [];
        if(window.planLoaded.indexOf(<?php print date_timestamp_set(new \DateTime(), $week)->format('W'); ?>) == -1) {
            window.planEvents = $.merge(window.planEvents, JSON.parse('<?php print json_encode(array_values($jsonEvents)); ?>'));
            window.planLoaded = $.merge(window.planLoaded, [<?php print date_timestamp_set(new \DateTime(), $week)->format('W'); ?>]);
        }
        // insert strategies
        window.strategies = JSON.parse('<?php print json_encode($strategies); ?>');
    </script>
<?php $view['slots']->stop();

$view['slots']->start('body'); ?>
<div class="panel-pane <?php print ($isDemo ? ' demo' : ''); ?>" id="plan">
    <div class="pane-content">
        <h2>Personalized study plan for <?php print $user->getFirst(); ?></h2>
        <div id="calendar" class="full-only fc fc-ltr fc-unthemed">
            <?php echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:planEmpty')); ?>
        </div>
        <div class="sort-by clearfix">
            <label>Sort by: </label>
            <label class="radio"><input type="radio" name="plan-sort" value="date"
                                        checked="checked"/><i></i>Date</label>
            <label class="radio"><input type="radio" name="plan-sort" value="class"><i></i>Class</label>
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
                ?><div class="head <?php print ($event->getStart() < $yesterday ? ' historic' : '');
                print ($event->getStart() >= $startWeek && $event->getStart() <= $endWeek ? ' mobile' : ''); ?>">
                <?php print $headStr; ?>
                </div><?php
            }

            /** @var Course $course */
            $course = $event->getCourse();
            $classI = array_search($course, $courses);
            if ($classI === false) {
                $classI = '';
            }

            $session = '';
            if ($event->getType() == 'd' || $classI === '') {
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
                $title = 'Deadline';
            } elseif ($event->getType() == 'd') {
                $title = 'Deadline';
            } elseif ($event->getType() == 'f') {
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
            print ($event->getStart() < $yesterday || $event->getDeleted() ? ' historic' : '');
            print ($event->getStart() >= $startWeek && $event->getStart() <= $endWeek ? ' mobile' : '');
            print (!empty($course) ? (' course-id-' . $course->getId()) : '');
            print (' default-' . $session);
            print ($event->getCompleted() ? ' done' : '');
            print (' event-id-' . $event->getId()); ?>">
                <div class="class-name"><span class="class<?php print $classI; ?>">&nbsp;</span><?php print $event->getName(); ?></div>
                <div class="assignment"><?php print $title; ?></div>
                <div class="percent"><?php print (!empty($event->getDeadline())
                        ? ($event->getDeadline()->getPercent() . '%')
                        : '&nbsp;'); ?></div>
                <div class="completed"><label class="checkbox"><input type="checkbox" value="true" <?php
                        print ($event->getCompleted() ? 'checked="checked"' : ''); ?>><i></i></label></div>
            </div>
        <?php } ?>
        <a class="return-to-top" href="#return-to-top">Top</a>
        <?php echo $view->render('StudySauceBundle:Plan:strategies.html.php'); ?>
    </div>
</div><?php
$view['slots']->stop();

$view['slots']->start('sincludes');
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:planUpgrade'));
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:planIntro1'));
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:planIntro2'),['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:planIntro3'),['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:planIntro4'),['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:sdsMessages'), ['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:checklist'), ['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:timerExpire'), ['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:billParents1'), ['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:billParents2'), ['strategy' => 'sinclude']);
$view['slots']->stop();


<?php

namespace StudySauce\Bundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use StudySauce\Bundle\Entity\Course;
use StudySauce\Bundle\Entity\Deadline;
use StudySauce\Bundle\Entity\Event;
use StudySauce\Bundle\Entity\Schedule;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * Class DeadlinesController
 * @package StudySauce\Bundle\Controller
 */
class DeadlinesController extends Controller
{
    /**
     * @param User $user
     * @param array $template
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request, User $user = null, $template = ['Deadlines', 'tab'])
    {
        /** @var $user \StudySauce\Bundle\Entity\User */
        if(empty($user))
            $user = $this->getUser();

        // forward to adviser deadlines
        if($user->hasRole('ROLE_ADVISER') || $user->hasRole('ROLE_MASTER_ADVISER')) {
            $params = $request->attributes->all();
            if($params['_format'] == 'index')
                $params['_format'] = 'adviser';
            return $this->forward('AdminBundle:Deadlines:index', $params, $request->query->all());
        }

        $deadlines = $user->getDeadlines()->filter(function (Deadline $d) {return !$d->getDeleted();});

        /** @var Schedule $schedule */
        $schedule = $user->getSchedules()->first();
        if(!empty($schedule))
            $courses = $schedule->getClasses()->toArray();
        else
            $courses = [];

        // show new deadline and hide headings if all the deadlines are in the past
        $isEmpty = false;
        if(!$deadlines->exists(function ($_, Deadline $d) use ($schedule) {
            return $d->getDueDate() >= date_sub(new \Datetime('today'), new \DateInterval('P1D'));
            //&& !empty($d->getCourse()) && $d->getCourse()->getSchedule() == $schedule;
        })) {
            $isEmpty = true;
        }

        $isDemo = false;
        if (empty($courses)) {
            $demo = ScheduleController::getDemoSchedule($this->container);
            $courses = $demo->getClasses()->toArray();
            $deadlines = $this->getDemoDeadlines($this->container);
            $isDemo = true;
        }

        $csrfToken = $this->has('form.csrf_provider')
            ? $this->get('form.csrf_provider')->generateCsrfToken('update_deadlines')
            : null;

        return $this->render('StudySauceBundle:' . $template[0] . ':' . $template[1] . '.html.php', [
                'csrf_token' => $csrfToken,
                'deadlines' => $deadlines->toArray(),
                'courses' => array_values($courses),
                'schedule' => $schedule,
                'user' => $user,
                'isDemo' => $isDemo,
                'isEmpty' => $isEmpty
            ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function widgetAction()
    {
        /** @var $user \StudySauce\Bundle\Entity\User */
        $user = $this->getUser();
        $deadlines = $user->getDeadlines()->filter(function (Deadline $d) {return !$d->getDeleted() &&
            $d->getDueDate() >= date_sub(new \DateTime(), new \DateInterval('P1D')); })->toArray();
        $deadlines = array_slice($deadlines, 0, 15);
        $schedule = $user->getSchedules()->first();
        if(!empty($schedule))
            $courses = $schedule->getClasses()->toArray();
        else
            $courses = [];

        return $this->render('StudySauceBundle:Deadlines:widget.html.php', [
                'deadlines' => $deadlines,
                'courses' => array_values($courses)
            ]);
    }

    public static $examples = ['Exam', 'Paper', 'Essay', 'Homework', 'Quiz', 'Group Project', 'Project', 'Test', 'Oral Exam', 'Journal'];

    /**
     * @return string
     */
    public static function getRandomAssignment()
    {
        return self::$examples[array_rand(self::$examples, 1)];
    }

    /**
     * @param ContainerInterface $container
     * @return ArrayCollection
     */
    public static function getDemoDeadlines($container)
    {
        /** @var $orm EntityManager */
        $orm = $container->get('doctrine')->getManager();
        /** @var $userManager UserManager */
        $userManager = $container->get('fos_user.user_manager');
        /** @var SecurityContext $context */
        /** @var TokenInterface $token */
        /** @var User $user */
        /** @var User $guest */
        if(!empty($context = $container->get('security.context')) && !empty($token = $context->getToken()) &&
            !empty($user = $token->getUser()) && $user->hasRole('ROLE_DEMO')) {
            $guest = $user;

        }
        else {
            $guest = $userManager->findUserByUsername('guest');
        }
        $demo = ScheduleController::getDemoSchedule($container);
        $courses = $demo->getClasses()->toArray();

        $deadlines = $guest->getDeadlines()->filter(function (Deadline $d) {return !$d->getDeleted() &&
            $d->getDueDate() >= date_sub(new \DateTime(), new \DateInterval('P1D'));});

        if($guest->getDeadlines()->count() < 10) {
            foreach ($courses as $course) {
                $assignment = self::getRandomAssignment();
                // pick a number between 1 and 10
                $repeat = rand(1, 10);
                $reminders = array_rand(
                    ['86400' => '', '172800' => '', '345600' => '', '604800' => '', '1209600' => ''],
                    rand(2, 5)
                );
                for ($j = 0; $j < $repeat; $j++) {
                    $deadline = new Deadline();
                    $deadline->setUser($guest);
                    $deadline->setCourse($course);
                    $deadline->setAssignment($assignment . ' ' . ($j + 1));
                    // evenly space $repeat over the next 4 weeks
                    $space = floor(7.0 * 8.0 / $repeat);
                    $due = new \DateTime();
                    $due->setTime(0, 0, 0);
                    $due->add(new \DateInterval('P' . ($space * $j + rand(1, 7)) . 'D'));
                    $deadline->setDueDate($due);
                    $deadline->setPercent(rand(5, 30));
                    $deadline->setReminder($reminders);
                    $deadlines->add($deadline);
                    $guest->addDeadline($deadline);
                    $orm->persist($deadline);
                }
            }
            $orm->flush();
        }
        PlanController::createAllDay($demo, $guest->getDeadlines()->toArray(), $orm);

        return $deadlines;
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var $user User */
        $user = $this->getUser();

        /** @var Schedule $schedule */
        $schedule = $user->getSchedules()->first();
        if(empty($schedule)) {
            $schedule = new Schedule();
            $schedule->setUser($user);
            $user->addSchedule($schedule);
            $orm->persist($schedule);
        }

        // save class
        $dates = $request->get('dates');
        if(empty($dates))
            $dates = [];

        if($request->get('className') && $request->get('assignment') && $request->get('reminders') &&
            $request->get('due') && $request->get('percent')) {
            $dates[] = [
                'eid' => isset($_POST['eid']) ? $_POST['eid'] : null,
                'courseId' => $request->get('courseId'),
                'className' => $request->get('className'),
                'assignment' => $request->get('assignment'),
                'reminders' => $request->get('reminders'),
                'due' => $request->get('due'),
                'percent' => $request->get('percent')
            ];
        }

        foreach($dates as $j => $d)
        {
            // check if class entity already exists by name
            if(empty($d['eid']))
            {
                $deadline = new Deadline();
                $deadline->setUser($user);
                $deadline->setCompleted(false);
                $deadline->setReminderSent([]);
            }
            else
            {
                /** @var $deadline Deadline */
                $deadline = $user->getDeadlines()->filter(function (Deadline $x) use($d) {return $x->getId() == $d['eid'];})->first();
                // figure out what changed
                if ($deadline->getDueDate()->format('Y-m-d') != (new \DateTime($d['due']))->format('Y-m-d')) {
                    // reset the sent reminders if the date changes
                    $deadline->setReminderSent('');
                }
            }

            if(!empty($d['courseId']) && $d['courseId'] != 'Nonacademic')
                $course = $schedule->getClasses()->filter(function (Course $c)use($d) {return $c->getId() == $d['courseId'];})->first();
            $deadline->setCourse(empty($course) ? null : $course);
            $deadline->setAssignment($d['assignment']);
            $deadline->setReminder(explode(',', $d['reminders']));
            $deadline->setDueDate(new \DateTime($d['due']));
            $deadline->setPercent($d['percent']);

            if(empty($d['eid']))
            {
                $user->addDeadline($deadline);
                $orm->persist($deadline);
            }
            else
            {
                $orm->merge($deadline);
            }
        }

        PlanController::createAllDay($schedule, $user->getDeadlines()->toArray(), $orm);
        $orm->flush();

        $request->attributes->set('_format', 'tab');
        /** @var Response $deadlines */
        $deadlines = $this->indexAction($request);
        /** @var Response $widget */
        $widget = $this->widgetAction();
        return new Response($deadlines->getContent() . $widget->getContent());
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeAction(Request $request)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var $user User */
        $user = $this->getUser();

        /** @var Schedule $schedule */
        $schedule = $user->getSchedules()->first();
        /** @var Deadline $deadline */
        $deadline = $user->getDeadlines()->filter(function (Deadline $d) use ($request) {
            return $d->getId() == $request->get('remove');})->first();
        if(!empty($deadline)) {
            $deadline->setDeleted(1);
            $orm->merge($deadline);
            // remove event
            /** @var Event $event */
            $event = $schedule->getEvents()->filter(
                function (Event $e) use ($deadline) {
                    return !empty($e->getDeadline()) && $e->getDeadline()->getId() == $deadline->getId();
                }
            )->first();
            if (!empty($event)) {
                $event->setDeleted(true);
                $orm->merge($event);
            }
            $orm->flush();
        }

        return $this->forward('StudySauceBundle:Deadlines:index', ['_format' => 'tab']);
    }
}


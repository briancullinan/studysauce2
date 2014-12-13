<?php

namespace StudySauce\Bundle\Controller;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use StudySauce\Bundle\Entity\Course;
use StudySauce\Bundle\Entity\Deadline;
use StudySauce\Bundle\Entity\Schedule;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DeadlinesController
 * @package StudySauce\Bundle\Controller
 */
class DeadlinesController extends Controller
{
    /**
     * @param User $user
     * @param array $template
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(User $user = null, $template = ['Deadlines', 'tab'])
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        /** @var $userManager UserManager */
        $userManager = $this->get('fos_user.user_manager');
        $demo = ScheduleController::getDemoSchedule($userManager, $orm);
        $demoCourses = $demo->getCourses()->filter(function (Course $b) {return $b->getType() == 'c';})->toArray();
        $demoDeadlines = $this->getDemoDeadlines();

        /** @var $user \StudySauce\Bundle\Entity\User */
        if(empty($user))
            $user = $this->getUser();
        $deadlines = $user->getDeadlines()->toArray();

        /** @var Schedule $schedule */
        $schedule = $user->getSchedules()->first();
        if(!empty($schedule))
            $courses = $schedule->getCourses()->filter(function (Course $b) {return $b->getType() == 'c';})->toArray();
        else
            $courses = [];

        $csrfToken = $this->has('form.csrf_provider')
            ? $this->get('form.csrf_provider')->generateCsrfToken('update_deadlines')
            : null;

        return $this->render('StudySauceBundle:' . $template[0] . ':' . $template[1] . '.html.php', [
                'csrf_token' => $csrfToken,
                'deadlines' => $deadlines,
                'demoDeadlines' => $demoDeadlines,
                'demoCourses' => array_values($demoCourses),
                'courses' => array_values($courses),
                'user' => $user
            ]);
    }

    /**
     * @param $_user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function partnerAction($_user)
    {
        /** @var $userManager UserManager */
        $userManager = $this->get('fos_user.user_manager');

        /** @var $user User */
        $user = $userManager->findUserBy(['id' => intval($_user)]);

        return $this->indexAction($user, ['Partner', 'deadlines']);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function widgetAction()
    {
        /** @var $user \StudySauce\Bundle\Entity\User */
        $user = $this->getUser();
        $deadlines = $user->getDeadlines()->filter(function (Deadline $d) {return $d->getDueDate() >= date_sub(new \DateTime(), new \DateInterval('P1D')); })->toArray();
        $schedule = $user->getSchedules()->first();
        if(!empty($schedule))
            $courses = $schedule->getCourses()->filter(function (Course $b) {return $b->getType() == 'c';})->toArray();
        else
            $courses = [];

        return $this->render('StudySauceBundle:Deadlines:widget.html.php', [
                'deadlines' => $deadlines,
                'courses' => $courses
            ]);
    }

    /**
     * @return mixed|Deadline
     */
    private function getDemoDeadlines()
    {
        /** @var $userManager UserManager */
        $userManager = $this->get('fos_user.user_manager');

        /** @var $guest User */
        $guest = $userManager->findUserByUsername("guest");

        $deadline = $guest->getDeadlines()->first();
        if($deadline == null)
        {
            $deadline = new Deadline();
            $deadline->setUser($guest);
            $deadline->setAssignment('Paper, exam, project, etc.');
            $deadline->setDueDate(date_add(new \DateTime(), new \DateInterval('P7D')));
            $deadline->setPercent(0);
            $deadline->setReminder([86400,345600,1209600]);
        }

        return [$deadline];
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
                $course = $schedule->getCourses()->filter(function (Course $c)use($d) {
                        return $c->getId() == $d['courseId'];})->first();
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

            $orm->flush();
        }

        $request->attributes->set('_format', 'tab');
        /** @var Response $deadlines */
        $deadlines = $this->indexAction();
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

        $deadline = $user->getDeadlines()->filter(function (Deadline $d) use ($request) {return $d->getId() == $request->get('remove');})->first();
        if(!empty($deadline)) {
            $orm->remove($deadline);
            $orm->flush();
        }

        return $this->forward('StudySauceBundle:Deadlines:index', ['_format' => 'tab']);
    }
}


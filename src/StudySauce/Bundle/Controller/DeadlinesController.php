<?php

namespace StudySauce\Bundle\Controller;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use StudySauce\Bundle\Entity\Course;
use StudySauce\Bundle\Entity\Deadline;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DeadlinesController
 * @package StudySauce\Bundle\Controller
 */
class DeadlinesController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        /** @var $userManager UserManager */
        $userManager = $this->get('fos_user.user_manager');
        $demo = ScheduleController::getDemoSchedule($userManager, $orm);
        $demoCourses = ScheduleController::getDemoCourses($demo, $orm);
        $demoDeadlines = $this->getDemoDeadlines();

        /** @var $user \StudySauce\Bundle\Entity\User */
        $user = $this->getUser();
        $deadlines = $user->getDeadlines()->toArray();
        $schedule = $user->getSchedules()->first();
        if(!empty($schedule))
            $courses = $schedule->getCourses()->filter(function (Course $b) {return $b->getType() == 'c';})->toArray();
        else
            $courses = [];

        $csrfToken = $this->has('form.csrf_provider')
            ? $this->get('form.csrf_provider')->generateCsrfToken('update_deadlines')
            : null;

        return $this->render('StudySauceBundle:Deadlines:tab.html.php', [
                'csrf_token' => $csrfToken,
                'deadlines' => $deadlines,
                'demoDeadlines' => $demoDeadlines,
                'demoCourses' => $demoCourses,
                'courses' => $courses
            ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function widgetAction()
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        /** @var $userManager UserManager */
        $userManager = $this->get('fos_user.user_manager');
        $demo = ScheduleController::getDemoSchedule($userManager, $orm);
        $demoCourses = ScheduleController::getDemoCourses($demo, $orm);
        $demoDeadlines = $this->getDemoDeadlines();

        /** @var $user \StudySauce\Bundle\Entity\User */
        $user = $this->getUser();
        $deadlines = $user->getDeadlines()->filter(function (Deadline $d) {return $d->getDueDate() > new \DateTime(); })->toArray();
        $schedule = $user->getSchedules()->first();
        if(!empty($schedule))
            $courses = $schedule->getCourses()->filter(function (Course $b) {return $b->getType() == 'c';})->toArray();
        else
            $courses = [];

        return $this->render('StudySauceBundle:Deadlines:widget.html.php', [
                'deadlines' => $deadlines,
                'demoDeadlines' => $demoDeadlines,
                'demoCourses' => $demoCourses,
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
            $deadline->setName('');
            $deadline->setReminder('86400,345600,1209600');
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

        // save class
        $dates = $request->get('dates');
        if(empty($dates))
            $dates = [];

        if($request->get('className') && $request->get('assignment') && $request->get('reminders') &&
            $request->get('due') && $request->get('percent')) {
            $dates[] = [
                'eid' => isset($_POST['eid']) ? $_POST['eid'] : null,
                'className' => $_POST['className'],
                'assignment' => $_POST['assignment'],
                'reminders' => $_POST['reminders'],
                'due' => $_POST['due'],
                'percent' => $_POST['percent']
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

            $deadline->setName($d['className']);
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

        return $this->forward('StudySauceBundle:Deadlines:index', ['_format' => 'tab']);
    }
}


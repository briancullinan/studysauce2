<?php

namespace StudySauce\Bundle\Controller;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use StudySauce\Bundle\Entity\Deadline;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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

        $csrfToken = $this->has('form.csrf_provider')
            ? $this->get('form.csrf_provider')->generateCsrfToken('update_schedule')
            : null;

        return $this->render('StudySauceBundle:Deadlines:tab.html.php', [
                'csrf_token' => $csrfToken,
                'deadlines' => $deadlines,
                'demoDeadlines' => $demoDeadlines,
                'courses' => $demoCourses
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
            $deadline->setCreated(new \DateTime);
            $deadline->setAssignment('Paper, exam, project, etc.');
            $deadline->setDueDate(date_add(new \DateTime(), new \DateInterval('P7D')));
            $deadline->setPercent(0);
            $deadline->setName('');
            $deadline->setReminder('86400,345600,1209600');
        }

        return [$deadline];
    }
}


<?php

namespace StudySauce\Bundle\Controller;

use Doctrine\ORM\EntityManager;
use StudySauce\Bundle\Entity\Course;
use StudySauce\Bundle\Entity\Schedule;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ProfileController
 * @package StudySauce\Bundle\Controller
 */
class ProfileController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {

        /** @var $user \StudySauce\Bundle\Entity\User */
        $user = $this->getUser();

        /** @var $schedule \StudySauce\Bundle\Entity\Schedule */
        $schedule = $user->getSchedules()->first();

        return $this->render('StudySauceBundle:Profile:tab.html.php', [
                'schedule' => $schedule ?: new Schedule(),
                'user' => $user
            ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function customizeAction()
    {
        /** @var $user \StudySauce\Bundle\Entity\User */
        $user = $this->getUser();

        /** @var $schedule \StudySauce\Bundle\Entity\Schedule */
        $schedule = $user->getSchedules()->first() ?: new Schedule();
        $courses = $schedule->getClasses()->toArray();

        return $this->render('StudySauceBundle:Profile:customize.html.php', [
                'courses' => array_values($courses),
                'user' => $user
            ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateAction(Request $request)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var $user \StudySauce\Bundle\Entity\User */
        $user = $this->getUser();

        /** @var $schedule \StudySauce\Bundle\Entity\Schedule */
        $schedule = $user->getSchedules()->first();
        $isNew = false;
        if(empty($schedule)) {
            $isNew = true;
            $schedule = new Schedule();
            $schedule->setUser($user);
            //$schedule->setCreated(new \DateTime());
            $user->addSchedule($schedule);
        }

        if(!empty($request->get('grades')) && !empty($request->get('weekends'))) {
            $schedule->setGrades($request->get('grades'));
            $schedule->setWeekends($request->get('weekends'));
            $schedule->setSharp6am11am($request->get('6-am-11-am'));
            $schedule->setSharp11am4pm($request->get('11-am-4-pm'));
            $schedule->setSharp4pm9pm($request->get('4-pm-9-pm'));
            $schedule->setSharp9pm2am($request->get('9-pm-2-am'));
        }

        if($isNew) {
            $orm->persist($schedule);
       }
        else {
            $orm->merge($schedule);
        }

        $courses = $schedule->getClasses()->toArray();
        foreach($courses as $i => $c)
        {
            /** @var Course $c */
            if(!empty($request->get('profile-type-' . $c->getId()))) {
                $c->setStudyType($request->get('profile-type-' . $c->getId()));
                $orm->merge($c);
            }
            if(!empty($request->get('profile-difficulty-' . $c->getId()))) {
                $c->setStudyDifficulty($request->get('profile-difficulty-' . $c->getId()));
                $orm->merge($c);
            }
        }
        $orm->flush();

        // check if schedule is empty
        return new JsonResponse(true);
    }
}


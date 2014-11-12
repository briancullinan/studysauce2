<?php

namespace StudySauce\Bundle\Controller;

use Doctrine\ORM\EntityManager;
use StudySauce\Bundle\Entity\Course;
use StudySauce\Bundle\Entity\Schedule;
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
        $courses = $schedule->getCourses()->filter(
            function (Course $b) {
                return $b->getType() == 'c';
            }
        )->toArray();

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
        $schedule = $user->getSchedules()->first() ?: new Schedule();

        if(!empty($request->get('grades')) && !empty($request->get('weekends'))) {
            $schedule->setGrades($request->get('grades'));
            $schedule->setWeekends($request->get('weekends'));
            $schedule->setSharp6am11am($request->get('6-am-11-am'));
            $schedule->setSharp11am4pm($request->get('11-am-4-pm'));
            $schedule->setSharp4pm9pm($request->get('4-pm-9-pm'));
            $schedule->setSharp9pm2am($request->get('9-pm-2-am'));
            $orm->merge($schedule);
            $orm->flush();
        }

        $courses = $schedule->getCourses()->filter(
            function (Course $b) {
                return $b->getType() == 'c';
            }
        )->toArray();
        $hasEmpties = false;
        foreach($courses as $i => $c)
        {
            /** @var Course $c */
            if(!empty($request->get('profile-type-' . $c->getId()))) {
                $params = $request->get('profile-type-' . $c->getId());
                $c->setStudyType($params['type']);
                $c->setStudyDifficulty($params['difficulty']);
                $orm->merge($c);
            }
            if(empty($c->getStudyType()) || empty($c->getStudyDifficulty()))
                $hasEmpties = true;
        }
        $orm->flush();

        // check if schedule is empty
        if(strpos($request->headers->get('referer'), '/funnel') > -1) {
            if (empty($courses)) {
                return new RedirectResponse($this->generateUrl('schedule', ['_format' => 'funnel']));
            } // check if study options are empty
            elseif ($hasEmpties) {
                return new RedirectResponse($this->generateUrl('customization', ['_format' => 'funnel']));
            } // check if the referrer is funnel, we must redirect, so go to plan tab
            else {
                return new RedirectResponse($this->generateUrl('plan'));
            }
        }
        return new JsonResponse(true);
    }
}


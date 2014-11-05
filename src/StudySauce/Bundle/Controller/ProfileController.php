<?php

namespace StudySauce\Bundle\Controller;

use Doctrine\ORM\EntityManager;
use StudySauce\Bundle\Entity\Course;
use StudySauce\Bundle\Entity\Schedule;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
                'schedule' => $schedule ?: new Schedule()
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

        return $this->render('StudySauceBundle:Profile:customize.html.php', [
                'courses' => $schedule->getCourses()->filter(
                    function (Course $b) {
                        return $b->getType() == 'c';
                    }
                )->toArray()
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

        if(!empty($request->get('grades')) || !empty($request->get('weekends')) || !empty($request->get('6-am-11-am')) ||
            !empty($request->get('11-am-4-pm')) || !empty($request->get('4-pm-9-pm')) || !empty($request->get('9-pm-2-am'))) {
            if (!empty($request->get('grades'))) {
                $schedule->setGrades($request->get('grades'));
            }

            if (!empty($request->get('weekends'))) {
                $schedule->setWeekends($request->get('weekends'));
            }

            if (!empty($request->get('6-am-11-am'))) {
                $schedule->setSharp6am11am($request->get('6-am-11-am'));
            }

            if (!empty($request->get('11-am-4-pm'))) {
                $schedule->setSharp11am4pm($request->get('11-am-4-pm'));
            }

            if (!empty($request->get('4-pm-9-pm'))) {
                $schedule->setSharp4pm9pm($request->get('4-pm-9-pm'));
            }

            if (!empty($request->get('9-pm-2-am'))) {
                $schedule->setSharp9pm2am($request->get('9-pm-2-am'));
            }

            $orm->merge($schedule);
            $orm->flush();
        }

        $courses = $schedule->getCourses()->filter(
            function (Course $b) {
                return $b->getType() == 'c';
            }
        )->toArray();

        foreach($courses as $i => $c)
        {
            /** @var Course $c */
            if(!empty($request->get('profile-type-' . $c->getId()))) {
                $params = $request->get('profile-type-' . $c->getId());
                $c->setStudyType($params['type']);
                $c->setStudyDifficulty($params['difficulty']);
                $orm->merge($c);
            }
        }
        $orm->flush();

        return new RedirectResponse($this->generateUrl('schedule', ['_format' => 'funnel']));
    }
}


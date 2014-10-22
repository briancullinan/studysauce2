<?php

namespace StudySauce\Bundle\Controller;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use StudySauce\Bundle\Entity\Checkin;
use StudySauce\Bundle\Entity\Course;
use StudySauce\Bundle\Entity\Schedule;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CheckinController
 * @package StudySauce\Bundle\Controller
 */
class CheckinController extends Controller
{
    /**
     * @internal param string $_format
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        /** @var $userManager UserManager */
        $userManager = $this->get('fos_user.user_manager');
        $demo = ScheduleController::getDemoSchedule($userManager, $orm);
        $demoCourses = $demo->getCourses()->filter(function (Course $b) {return $b->getType() == 'c';})->toArray();

        /** @var $user \StudySauce\Bundle\Entity\User */
        $user = $this->getUser();

        /** @var $schedule Schedule */
        $schedule = $user->getSchedules()->first();
        if(!empty($schedule))
            $courses = $schedule->getCourses()->filter(function (Course $b) {return $b->getType() == 'c';})->toArray();
        else
            $courses = [];

        $csrfToken = $this->has('form.csrf_provider')
            ? $this->get('form.csrf_provider')->generateCsrfToken('checkin_update')
            : null;

        return $this->render('StudySauceBundle:Checkin:tab.html.php', [
                'csrf_token' => $csrfToken,
                'demoCourses' => $demoCourses,
                'courses' => $courses
            ]);
    }

    /**
     * @internal param string $_format
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function widgetAction()
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        /** @var $userManager UserManager */
        $userManager = $this->get('fos_user.user_manager');
        $demo = ScheduleController::getDemoSchedule($userManager, $orm);
        $demoCourses = $demo->getCourses()->filter(function (Course $b) {return $b->getType() == 'c';})->toArray();

        /** @var $user \StudySauce\Bundle\Entity\User */
        $user = $this->getUser();

        /** @var $schedule Schedule */
        $schedule = $user->getSchedules()->first();
        if(!empty($schedule))
            $courses = $schedule->getCourses()->filter(function (Course $b) {return $b->getType() == 'c';})->toArray();
        else
            $courses = [];

        return $this->render('StudySauceBundle:Checkin:widget.html.php', [
                'demoCourses' => $demoCourses,
                'courses' => $courses
            ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateAction(Request $request)
    {
        // record checkin time
        $cid = $request->get('cid');
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var $user \StudySauce\Bundle\Entity\User */
        $user = $this->getUser();
        $schedule = $user->getSchedules()->first();

        /** @var $course Course */
        $course = $schedule->getCourses()->filter(function (Course $b) use ($cid) {
                return $b->getType() == 'c' && $b->getId() == $cid;
            })->first();
        if($request->get('checkedIn'))
        {
            /** @var $c Checkin */
            $c = $course->getCheckins()->first();
            $c->setCheckout(new \DateTime($request->get('date')));
            $c->setUtcCheckout(new \DateTime());
            $orm->merge($c);
        }
        else
        {
            $c = new Checkin();
            $c->setCourse($course);
            $c->setCheckin(new \DateTime($request->get('date')));
            $c->setUtcCheckin(new \DateTime());
            $orm->persist($c);
        }
        $orm->flush();

        $csrfToken = $this->has('form.csrf_provider')
            ? $this->get('form.csrf_provider')->generateCsrfToken('checkin_update')
            : null;

        return new JsonResponse(['cid' => $cid, 'csrf_token' => $csrfToken]);
    }
}


<?php

namespace StudySauce\Bundle\Controller;

use Doctrine\ORM\EntityManager;
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
        $demo = ScheduleController::getDemoSchedule($this->container);
        $demoCourses = $demo->getClasses()->toArray();

        /** @var $user \StudySauce\Bundle\Entity\User */
        $user = $this->getUser();

        /** @var $schedule Schedule */
        $schedule = $user->getSchedules()->first();
        if(!empty($schedule))
            $courses = $schedule->getClasses()->toArray();
        else
            $courses = [];

        $csrfToken = $this->has('form.csrf_provider')
            ? $this->get('form.csrf_provider')->generateCsrfToken('checkin_update')
            : null;

        return $this->render('StudySauceBundle:Checkin:tab.html.php', [
                'csrf_token' => $csrfToken,
                'demoCourses' => array_values($demoCourses),
                'courses' => array_values($courses)
            ]);
    }

    /**
     * @internal param string $_format
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function widgetAction()
    {
        $demo = ScheduleController::getDemoSchedule($this->container);
        $demoCourses = $demo->getClasses()->toArray();

        /** @var $user \StudySauce\Bundle\Entity\User */
        $user = $this->getUser();

        /** @var $schedule Schedule */
        $schedule = $user->getSchedules()->first();
        if(!empty($schedule))
            $courses = $schedule->getClasses()->toArray();
        else
            $courses = [];

        return $this->render('StudySauceBundle:Checkin:widget.html.php', [
                'demoCourses' => array_values($demoCourses),
                'courses' => array_values($courses)
            ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateAction(Request $request)
    {
        // record checkin time
        $courseId = $request->get('courseId');
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var $user \StudySauce\Bundle\Entity\User */
        $user = $this->getUser();
        $schedule = $user->getSchedules()->first();

        /** @var $course Course */
        $course = $schedule->getClasses()->filter(function (Course $b) use ($courseId) {return $b->getId() == $courseId;})->first();
        if(!empty($course)) {
            if ($request->get('checkedIn')) {
                /** @var $c Checkin */
                $c = $course->getCheckins()->last();
                $c->setCheckout(date_timezone_set(new \DateTime($request->get('date')), new \DateTimeZone(date_default_timezone_get())));
                $c->setUtcCheckout(new \DateTime());
                $orm->merge($c);
            } else {
                $c = new Checkin();
                $c->setCourse($course);
                $c->setCheckin(date_timezone_set(new \DateTime($request->get('date')), new \DateTimeZone(date_default_timezone_get())));
                $utc = new \DateTime();
                $c->setUtcCheckin($utc);
                if(!empty($request->get('length'))) {
                    $c->setCheckout(date_timezone_set(date_add(new \DateTime($request->get('date')), new \DateInterval('PT' . intval($request->get('length')) * 60 . 'S')), new \DateTimeZone(date_default_timezone_get())));
                    $c->setUtcCheckout($utc);
                }
                $course->addCheckin($c);
                $orm->persist($c);
            }
            $orm->flush();
        }

        $csrfToken = $this->has('form.csrf_provider')
            ? $this->get('form.csrf_provider')->generateCsrfToken('checkin_update')
            : null;

        $count = array_sum(array_map(function (Course $c) {return $c->getCheckins()->count(); }, $schedule->getCourses()->toArray()));

        return new JsonResponse(['courseId' => $courseId, 'lastSDS' => $count, 'csrf_token' => $csrfToken]);
    }
}


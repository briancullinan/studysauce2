<?php

namespace StudySauce\Bundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use StudySauce\Bundle\Entity\Course;
use StudySauce\Bundle\Entity\Event;
use StudySauce\Bundle\Entity\Schedule;
use StudySauce\Bundle\Entity\User;
use StudySauce\Bundle\StudySauceBundle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class ScheduleController
 * @package StudySauce\Bundle\Controller
 */
class ScheduleController extends Controller
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

        $demo = self::getDemoSchedule($userManager, $orm);
        $demoCourses = self::getDemoCourses($demo, $orm);
        $demoOthers = self::getDemoOthers($demo, $orm);

        /** @var $user \StudySauce\Bundle\Entity\User */
        $user = $this->getUser();

        /** @var $schedule \StudySauce\Bundle\Entity\Schedule */
        $schedule = $user->getSchedules()->first();

        if(!empty($schedule)) {
            $courses = $schedule->getCourses()->filter(
                function (Course $b) {
                    return !$b->getDeleted() && $b->getType() == 'c';
                }
            )->toArray();
            $others = $schedule->getCourses()->filter(
                function (Course $b) {
                    return !$b->getDeleted() && $b->getType() == 'o';
                }
            )->toArray();
        }
        else
        {
            $courses = [];
            $others = [];
        }

        $csrfToken = $this->has('form.csrf_provider')
            ? $this->get('form.csrf_provider')->generateCsrfToken('update_schedule')
            : null;

        return $this->render('StudySauceBundle:Schedule:tab.html.php', [
                'schedule' => $schedule,
                'demo' => $demo,
                'csrf_token' => $csrfToken,
                'courses' => array_values($courses),
                'demoCourses' => $demoCourses,
                'others' => $others,
                'demoOthers' => $demoOthers
            ]);
    }

    /**
     * @param $userManager
     * @param $orm
     * @return mixed|\StudySauce\Bundle\Entity\Schedule
     */
    public static function getDemoSchedule(UserManager $userManager, EntityManager $orm)
    {
        /** @var $guest User */
        $guest = $userManager->findUserByUsername('guest');

        $schedule = $guest->getSchedules()->first();
        // TODO: generate valid schedules and courses
        if($schedule == null)
        {
            // create new empty schedule for all guest users
            $schedule = new Schedule();
            $schedule->setUser($guest);
            $schedule->setUniversity('Enter the full name');
            $schedule->setGrades('as-only');
            $schedule->setWeekends('hit-hard');
            $schedule->setSharp6am11am(5);
            $schedule->setSharp11am4pm(3);
            $schedule->setSharp4pm9pm(4);
            $schedule->setSharp9pm2am(4);

            $guest->addSchedule($schedule);
            $orm->persist($schedule);
            $orm->flush();

        }

        self::getDemoCourses($schedule, $orm);
        self::getDemoOthers($schedule, $orm);

        return $schedule;
    }

    /**
     * @param Schedule $schedule
     * @param EntityManager $orm
     * @return array
     */
    private static function getDemoCourses(Schedule $schedule, EntityManager $orm)
    {
        // TODO: remap demo functions to studysauce static functions used by testers?
        $courses = $schedule->getCourses()->filter(function (Course $b) {return !$b->getDeleted() && $b->getType() == 'c';})->toArray();
        if(empty($courses))
        {
            $courses = [];
            $class1 = new \DateTime('last Sunday');
            $class1->setTime(8, 0, 0);
            $class1->sub(new \DateInterval('P3W'));
            $course = new Course();
            $course->setSchedule($schedule);
            $course->setName(self::getRandomName());
            $course->setType('c');
            $course->setDotw(['M', 'W', 'F']);
            $course->setStartTime($class1);
            $course->setEndTime(date_add(clone $class1, new \DateInterval('P8WT50M')));
            $course->setStudyDifficulty('easy');
            $course->setStudyType('memorization');

            $orm->persist($course);
            $orm->flush();
            $courses[] = $course;
            $schedule->addCourse($course);

            $course = new Course();
            $class2 = new \DateTime('last Sunday');
            $class2->setTime(9, 0, 0);
            $class2->sub(new \DateInterval('P3W'));
            $course->setSchedule($schedule);
            $course->setName(self::getRandomName());
            $course->setType('c');
            $course->setDotw(['M', 'W', 'F']);
            $course->setStartTime($class2);
            $course->setEndTime(date_add(clone $class2, new \DateInterval('P8WT50M')));
            $course->setStudyDifficulty('easy');
            $course->setStudyType('memorization');

            $orm->persist($course);
            $orm->flush();
            $courses[] = $course;
            $schedule->addCourse($course);

            $course = new Course();
            $class3 = new \DateTime('last Sunday');
            $class3->setTime(10, 0, 0);
            $class3->sub(new \DateInterval('P3W'));
            $course->setSchedule($schedule);
            $course->setName(self::getRandomName());
            $course->setType('c');
            $course->setDotw(['M', 'W', 'F']);
            $course->setStartTime($class3);
            $course->setEndTime(date_add(clone $class3, new \DateInterval('P8WT50M')));
            $course->setStudyDifficulty('average');
            $course->setStudyType('conceptual');

            $orm->persist($course);
            $orm->flush();
            $courses[] = $course;
            $schedule->addCourse($course);

            $course = new Course();
            $class4 = new \DateTime('last Sunday');
            $class4->setTime(11, 0, 0);
            $class4->sub(new \DateInterval('P3W'));
            $course->setSchedule($schedule);
            $course->setName(self::getRandomName());
            $course->setType('c');
            $course->setDotw(['M', 'W', 'F']);
            $course->setStartTime($class4);
            $course->setEndTime(date_add(clone $class4, new \DateInterval('P8WT50M')));
            $course->setStudyDifficulty('average');
            $course->setStudyType('conceptual');

            $orm->persist($course);
            $orm->flush();
            $courses[] = $course;
            $schedule->addCourse($course);

            $course = new Course();
            $class5 = new \DateTime('last Sunday');
            $class5->setTime(12, 0, 0);
            $class5->sub(new \DateInterval('P3W'));
            $course->setSchedule($schedule);
            $course->setName(self::getRandomName());
            $course->setType('c');
            $course->setDotw(['M', 'W', 'F']);
            $course->setStartTime($class5);
            $course->setEndTime(date_add(clone $class5, new \DateInterval('P8WT50M')));
            $course->setStudyDifficulty('tough');
            $course->setStudyType('reading');

            $orm->persist($course);
            $orm->flush();
            $courses[] = $course;
            $schedule->addCourse($course);

            $course = new Course();
            $class6 = new \DateTime('last Sunday');
            $class6->setTime(9, 0, 0);
            $class6->sub(new \DateInterval('P3W'));
            $course->setSchedule($schedule);
            $course->setName(self::getRandomName());
            $course->setType('c');
            $course->setDotw(['Tu', 'Th']);
            $course->setStartTime($class6);
            $course->setEndTime(date_add(clone $class6, new \DateInterval('P8WT120M')));
            $course->setStudyDifficulty('easy');
            $course->setStudyType('reading');

            $orm->persist($course);
            $orm->flush();
            $courses[] = $course;
            $schedule->addCourse($course);

            $course = new Course();
            $class7 = new \DateTime('last Sunday');
            $class7->setTime(11, 15, 0);
            $class7->sub(new \DateInterval('P3W'));
            $course->setSchedule($schedule);
            $course->setName(self::getRandomName());
            $course->setType('c');
            $course->setDotw(['Tu', 'Th']);
            $course->setStartTime($class7);
            $course->setEndTime(date_add(clone $class7, new \DateInterval('P8WT90M')));
            $course->setStudyDifficulty('easy');
            $course->setStudyType('conceptual');

            $orm->persist($course);
            $orm->flush();
            $courses[] = $course;
            $schedule->addCourse($course);

            $course = new Course();
            $class8 = new \DateTime('last Sunday');
            $class8->setTime(14, 50, 0);
            $class8->sub(new \DateInterval('P3W'));
            $course->setSchedule($schedule);
            $course->setName(self::getRandomName());
            $course->setType('c');
            $course->setDotw(['Tu', 'Th']);
            $course->setStartTime($class8);
            $course->setEndTime(date_add(clone $class8, new \DateInterval('P8WT50M')));
            $course->setStudyDifficulty('easy');
            $course->setStudyType('memorization');

            $orm->persist($course);
            $orm->flush();
            $courses[] = $course;
            $schedule->addCourse($course);

        }

        return $courses;
    }

    /**
     * @param Schedule $schedule
     * @param EntityManager $orm
     * @return array
     */
    private static function getDemoOthers(Schedule $schedule, EntityManager $orm)
    {
        // TODO: remap demo functions to studysauce static functions used by testers?
        /*
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq("birthday", "1982-02-17"))
            ->orderBy(array("username" => Criteria::ASC))
            ->setFirstResult(0)
            ->setMaxResults(20)
        ;

        $birthdayUsers = $userCollection->matching($criteria);
        */
        $others = $schedule->getCourses()->filter(function (Course $b) {return !$b->getDeleted() && $b->getType() == 'o';})->toArray();
        if(empty($others))
        {
            $others = [];
            $course = new Course();
            $classO = new \DateTime('last Sunday');
            $classO->setTime(18, 0, 0);
            $classO->sub(new \DateInterval('P3W'));
            $course->setSchedule($schedule);
            $course->setName(self::getRandomOther());
            $course->setType('o');
            $course->setDotw(['M', 'Tu', 'W', 'Th', 'F', 'Weekly']);
            $course->setStartTime($classO);
            $course->setEndTime(date_add(clone $classO, new \DateInterval('P8WT60M')));

            $orm->persist($course);
            $orm->flush();
            $others[] = $course;
            $schedule->addCourse($course);
        }

        return array_values($others);
    }

    public static $examples = ['HIST 10', 'CALC 12', 'MAT 20', 'PHY 11', 'BUS 30', 'ANT 35', 'GEO 40', 'BIO 25', 'CHM 18', 'PHIL 10', 'ENG 10'];
    public static $counter = 0;

    /**
     * @return string
     */
    public static function getRandomName()
    {
        return self::$examples[array_rand(self::$examples, 1)] . ++self::$counter;
    }

    public static $otherExamples = ['Work', 'Practice', 'Gym', 'Meeting'];
    public static function getRandomOther()
    {
        return self::$otherExamples[array_rand(self::$otherExamples, 1)];
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request)
    {
        if (FALSE === $this->get('form.csrf_provider')->isCsrfTokenValid('update_schedule', $request->get('csrf_token')))
        {
            throw new AccessDeniedHttpException('Invalid CSRF token.');
        }
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var $user User */
        $user = $this->getUser();

        /** @var $schedule Schedule */
        $schedule = $user->getSchedules()->first();
        // create new schedule if one does not exist
        if($schedule == null)
        {
            $schedule = new Schedule();
            // set all default on new schedule
            $schedule->setUser($user);
            if(!empty($request->get('university')) && $request->get('university') != $schedule->getUniversity())
                $schedule->setUniversity($request->get('university'));
            $user->addSchedule($schedule);
            $orm->persist($schedule);
            $orm->flush();
        }
        // set school name
        elseif(!empty($request->get('university')) && $request->get('university') != $schedule->getUniversity())
        {
            $schedule->setUniversity($request->get('university'));
            $orm->merge($schedule);
            $orm->flush();
        }

        $classes = $request->get('classes');
        if (empty($classes))
            $classes = [];

        // move single values in to an array so we can reuse the code from the plan page and the schedule page
        if ($request->get('className') && $request->get('type') && $request->get('dotw') &&
            $request->get('start') && $request->get('end')
        ) {
            $classes[] = [
                'className' => $request->get('className'),
                'type' => $request->get('type'),
                'dotw' => $request->get('dotw'),
                'start' => $request->get('start'),
                'end' => $request->get('end')
            ];
        }

        $added = [];
        $renamed = [];
        foreach ($classes as $j => $c) {
            // check if class entity already exists
            if (empty($c['courseId'])) {
                $course = new Course();
                $course->setSchedule($schedule);
                $course->setName($c['className']);
            } else {
                /** @var $course Course */
                $course = $schedule->getCourses()->filter(function (Course $x) use($c) {return !$x->getDeleted() && $x->getId() == $c['courseId'];})->first();
                // figure out what changed
                if ($course->getName() != $c['className']) {
                    // remove old reoccurring events
                    $renamed[$c['courseId']] = $course->getName();
                    $course->setName($c['className']);
                }
            }
            $dotw = explode(',', $c['dotw']);
            if(empty($dotw[0]))
                $dotw = [];

            $dotw = array_unique($dotw);
            sort($dotw);

            $course->setDotw($dotw);
            $course->setType($c['type']);
            $course->setStartTime(new \DateTime($c['start']));
            $course->setEndTime(new \DateTime($c['end']));

            if (empty($c['courseId'])) {
                // save course
                $schedule->addCourse($course);

                // we don't need manage removal of new entries
                $added['new' . count($added)] = $course;

                $orm->persist($course);
            }
            else
                $orm->merge($course);

            $orm->flush();
        }

        // redirect to customization page in buy funnel
        if(strpos($request->headers->get('referer'), '/funnel') > -1) {
            if (($step = ProfileController::getFunnelState($user))) {
                return new RedirectResponse($this->generateUrl($step, ['_format' => 'funnel']));
            }
            else
                return new RedirectResponse($this->generateUrl('plan'));
        }

        return $this->forward('StudySauceBundle:Schedule:index', ['_format' => 'tab']);
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

        /** @var Course $course */
        $course = $schedule->getCourses()
            ->filter(function (Course $c) use($request) {return !$c->getDeleted() && $c->getId() == $request->get('remove');})
            ->first();

        if(!empty($course))
        {
            if($course->getEvents()->exists(function ($k, Event $save) {
                    return !empty($save->getActive()) || !empty($save->getCompleted()) || !empty($save->getOther()) ||
                        !empty($save->getPrework()) || !empty($save->getTeach()) || !empty($save->getSpaced());
                })) {
                $course->setDeleted(true);
                $orm->merge($course);
            }
            else {
                $events = $course->getEvents()->toArray();
                foreach($events as $event) {
                    $course->removeEvent($event);
                    $schedule->removeEvent($event);
                    $orm->remove($event);
                }
                $schedule->removeCourse($course);
                $orm->remove($course);
            }
            $orm->flush();
        }

        return $this->forward('StudySauceBundle:Schedule:index', ['_format' => 'tab']);
    }

    private static $institutions;

    /**
     * @param $search
     * @return ArrayCollection
     */
    public static function getInstitutions($search)
    {
        if(empty($search))
            return new ArrayCollection();
        if(empty(self::$institutions))
        {
            self::$institutions = json_decode(file_get_contents(StudySauceBundle::$institutions_path));
        }
        $results = [];
        $search = strtolower($search);

        foreach(self::$institutions as $i => $u) {
            if (count($results) > 200) {
                break;
            }
            if (strpos(strtolower($u->institution), $search) > -1 || strpos(strtolower($u->state), $search) > -1 ||
                strpos(strtolower($u->link), $search) > -1) {
                $results[] = ['institution' => html_entity_decode($u->institution), 'state' => $u->state, 'link' => $u->link];
            }
        }

        return new ArrayCollection($results);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function institutionsAction(Request $request)
    {
        return new JsonResponse(self::getInstitutions($request->query->get('q'))->toArray());
    }

}
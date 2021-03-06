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
use StudySauce\Bundle\StudySauceBundle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContext;

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

        $demo = self::getDemoSchedule($this->container);
        self::getDemoCourses($demo, $orm);
        self::getDemoOthers($demo, $orm);

        /** @var $user \StudySauce\Bundle\Entity\User */
        $user = $this->getUser();

        $csrfToken = $this->has('form.csrf_provider')
            ? $this->get('form.csrf_provider')->generateCsrfToken('update_schedule')
            : null;

        /** @var Schedule $schedule */
        /** @var \DateTime $prev */
        $needsNew = !empty(
            // check that there are classes set up
            $schedule = $user->getSchedules()->first()) && !empty($schedule->getClasses()->count())
            // check that all the courses have ended
            && !$schedule->getClasses()->exists(function ($_, Course $c) {
                    return $c->getEndTime() > new \DateTime();});
            // check that message hasn't been displayed in at least 6 months
            //&& (empty($user->getProperty('needs_new')) || true === ($prev = $user->getProperty('needs_new')) ||
            //    $prev->getTimestamp() < date_sub(new \DateTime(), new \DateInterval('P3M'))->getTimestamp());
        if($needsNew) {
            /** @var $userManager UserManager */
            $userManager = $this->get('fos_user.user_manager');
            $user->setProperty('needs_new', (new \DateTime())->getTimestamp());
            $userManager->updateUser($user);
        }

        $step = PlanController::getPlanStep($user);

        return $this->render('StudySauceBundle:Schedule:tab.html.php', [
                'needsNew' => $needsNew,
                'schedules' => $user->getSchedules()->isEmpty()
                    ? [new Schedule()]
                    : $user->getSchedules()->toArray(),
                'demoSchedules' => [$demo],
                'isDemo' => $user->getSchedules()->isEmpty() || $user->getSchedules()->first()->getCourses()->isEmpty(),
                'csrf_token' => $csrfToken,
                'step' => $step
            ]);
    }

    /**
     * @param ContainerInterface $container
     * @return mixed|\StudySauce\Bundle\Entity\Schedule
     */
    public static function getDemoSchedule(ContainerInterface $container)
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

        $schedule = $guest->getSchedules()->first();
        // generate valid schedules and courses
        if($schedule == null)
        {
            // create new empty schedule for all guest users
            $schedule = new Schedule();
            $schedule->setUser($guest);
            $schedule->setUniversity('Arizona State University');
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
        $old = $schedule->getCourses()->filter(function (Course $b) {return !$b->getDeleted() &&
            $b->getEndTime() > new \DateTime(); })->toArray();
        if(count($old) < 4) {
            $old = $schedule->getCourses()->toArray();
            foreach ($old as $i => $c) {
                self::removeCourse($c, $orm);
            }
            $orm->flush();
        }

        $courses = $schedule->getClasses()->toArray();
        if(empty($courses))
        {
            $courses = [];
            $class1 = new \DateTime('last Sunday');
            $class1->setTime(8, 0, 0);
            $class1->sub(new \DateInterval('P3W'));
            $course = new Course();
            $course->setSchedule($schedule);
            $course->setName('ENG 101');
            $course->setType('c');
            $course->setDotw(['M', 'W', 'F']);
            $course->setStartTime($class1);
            $course->setEndTime(date_add(clone $class1, new \DateInterval('P8WT50M')));
            $course->setStudyDifficulty('easy');
            $course->setStudyType('memorization');

            $orm->persist($course);
            $courses[] = $course;
            $schedule->addCourse($course);

            $course = new Course();
            $class2 = new \DateTime('last Sunday');
            $class2->setTime(9, 0, 0);
            $class2->sub(new \DateInterval('P3W'));
            $course->setSchedule($schedule);
            $course->setName('ENG 102');
            $course->setType('c');
            $course->setDotw(['M', 'W', 'F']);
            $course->setStartTime($class2);
            $course->setEndTime(date_add(clone $class2, new \DateInterval('P8WT50M')));
            $course->setStudyDifficulty('easy');
            $course->setStudyType('memorization');

            $orm->persist($course);
            $courses[] = $course;
            $schedule->addCourse($course);

            $course = new Course();
            $class3 = new \DateTime('last Sunday');
            $class3->setTime(10, 0, 0);
            $class3->sub(new \DateInterval('P3W'));
            $course->setSchedule($schedule);
            $course->setName('HIST 103');
            $course->setType('c');
            $course->setDotw(['M', 'W', 'F']);
            $course->setStartTime($class3);
            $course->setEndTime(date_add(clone $class3, new \DateInterval('P8WT50M')));
            $course->setStudyDifficulty('average');
            $course->setStudyType('conceptual');

            $orm->persist($course);
            $courses[] = $course;
            $schedule->addCourse($course);

            $course = new Course();
            $class4 = new \DateTime('last Sunday');
            $class4->setTime(11, 0, 0);
            $class4->sub(new \DateInterval('P3W'));
            $course->setSchedule($schedule);
            $course->setName('BIO 254');
            $course->setType('c');
            $course->setDotw(['M', 'W', 'F']);
            $course->setStartTime($class4);
            $course->setEndTime(date_add(clone $class4, new \DateInterval('P8WT50M')));
            $course->setStudyDifficulty('average');
            $course->setStudyType('conceptual');

            $orm->persist($course);
            $courses[] = $course;
            $schedule->addCourse($course);
        }

        $orm->flush();

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
        $others = $schedule->getOthers()->toArray();
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
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var $user User */
        $user = $this->getUser();

        $terms = $request->get('terms');
        foreach($terms as $t) {
            /** @var $schedule Schedule */
            if(empty($t['scheduleId']) && $t['remove'] == 'true') {
                continue;
            }
            else if(!empty($t['scheduleId'])) {
                $schedule = $user->getSchedules()->filter(
                    function (Schedule $s) use ($t) {
                        return $s->getId() == $t['scheduleId'];
                    }
                )->first();
            }
            // create new schedule if one does not exist
            else {
                $first = $user->getSchedules()->first();
                $schedule = new Schedule();
                $schedule->setUser($user);
                $user->addSchedule($schedule);

                // default profile settings to previous schedule
                /** @var Schedule $first */
                if(!empty($first)) {
                    $schedule->setUniversity($first->getUniversity());
                    $schedule->setGrades($first->getGrades());
                    $schedule->setWeekends($first->getWeekends());
                    $schedule->setSharp6am11am($first->getSharp6am11am());
                    $schedule->setSharp11am4pm($first->getSharp11am4pm());
                    $schedule->setSharp4pm9pm($first->getSharp4pm9pm());
                    $schedule->setSharp9pm2am($first->getSharp9pm2am());
                }
            }

            // remove empty schedules
            if($t['remove'] == 'true') {
                self::removeSchedule($schedule, $user, $orm);
                $orm->flush();
                continue;
            }

            if(!empty($t['university']) && $t['university'] != $schedule->getUniversity())
                $schedule->setUniversity($t['university']);
            $schedule->setTerm(empty($t['term']) ? null : date_create_from_format('!n/Y', $t['term']));

            // save the schedule first
            if(empty($t['scheduleId']))
                $orm->persist($schedule);
            else
                $orm->merge($schedule);
            $orm->flush();

            if(empty($t['classes']))
                continue;

            // remove courses first
            foreach ($t['classes'] as $j => $c) {
                if (empty($c['courseId']) || $c['remove'] != 'true')
                    continue;

                /** @var $course Course */
                $course = $schedule->getCourses()->filter(function (Course $x) use($c) {
                    return !$x->getDeleted() && $x->getId() == $c['courseId'];})->first();

                self::removeCourse($course, $orm);
            }

            // if schedule is empty, remove the rest of the events
            if(empty($schedule->getClasses()->count())) {
                foreach($schedule->getEvents()->toArray() as $e) {
                    $orm->remove($e);
                }
            }

            // create new courses and ignore removes
            foreach ($t['classes'] as $j => $c) {
                if ($c['remove'] == 'true')
                    continue;

                // check if class entity already exists
                if (empty($c['courseId'])) {
                    $course = new Course();
                    $course->setSchedule($schedule);
                    $course->setName($c['className']);
                } else {
                    /** @var $course Course */
                    $course = $schedule->getCourses()->filter(function (Course $x) use($c) {
                        return !$x->getDeleted() && $x->getId() == $c['courseId'];})->first();
                }

                // save course settings
                $dotw = explode(',', $c['dotw']);
                if(empty($dotw[0]))
                    $dotw = [];

                $dotw = array_unique($dotw);
                sort($dotw);

                $course->setName($c['className']);
                $course->setDotw($dotw);
                $course->setType($c['type']);
                $course->setStartTime(new \DateTime($c['start']));
                $course->setEndTime(new \DateTime($c['end']));

                if (empty($c['courseId'])) {
                    // save course
                    $schedule->addCourse($course);
                    $orm->persist($course);

                    $user->setProperty('plan_complete', false);
                    /** @var $userManager UserManager */
                    $userManager = $this->get('fos_user.user_manager');
                    $userManager->updateUser($user);
                }
                else
                    $orm->merge($course);

                PlanController::createCourseEvents($course, $orm);
                PlanController::expandStudyEvents($course, $schedule, $orm);
                PlanController::createAllDay($schedule, $user->getDeadlines()->toArray(), $orm);
                $orm->flush();
            }
        }

        return $this->forward('StudySauceBundle:Schedule:index', ['_format' => 'tab']);
    }

    /**
     * @param Schedule $s
     * @param User $u
     * @param EntityManager $orm
     */
    private static function removeSchedule(Schedule $s, User $u, EntityManager $orm)
    {
        /** @var Schedule $s */
        foreach($s->getEvents()->toArray() as $j => $e) {
            /** @var Event $e */
            $s->removeEvent($e);
            $orm->remove($e);
        }
        foreach($s->getCourses()->toArray() as $j => $co) {
            /** @var Course $co */
            foreach($co->getCheckins()->toArray() as $k => $ch) {
                $co->removeCheckin($ch);
                $orm->remove($ch);
            }
            foreach($co->getGrades()->toArray() as $gr) {
                $co->removeGrade($gr);
                $orm->remove($gr);
            }
            foreach($co->getDeadlines() as $d) {
                /** @var Deadline $d */
                $co->removeDeadline($d);
                $orm->remove($d);
            }
            $s->removeCourse($co);
            $orm->remove($co);
        }
        $u->removeSchedule($s);
        $orm->remove($s);
    }

    /**
     * @param Course $course
     * @param EntityManager $orm
     */
    private static function removeCourse(Course $course, EntityManager $orm)
    {
       $course->setDeleted(true);
        foreach($course->getDeadlines()->toArray() as $d) {
            /** @var Deadline $d */
            $d->setDeleted(true);
            $orm->merge($d);
        }
        foreach($course->getEvents()->toArray() as $e) {
            /** @var Event $e */
            $orm->remove($e);
            $course->removeEvent($e);
            $course->getSchedule()->removeEvent($e);
        }
        $orm->merge($course);
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
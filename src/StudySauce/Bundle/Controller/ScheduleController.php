<?php

namespace StudySauce\Bundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use StudySauce\Bundle\Entity\Course;
use StudySauce\Bundle\Entity\Schedule;
use StudySauce\Bundle\Entity\User;
use StudySauce\Bundle\StudySauceBundle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
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
                    return $b->getType() == 'c';
                }
            )->toArray();
            $others = $schedule->getCourses()->filter(
                function (Course $b) {
                    return $b->getType() == 'o';
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
                'classes' => $schedule != null ? $schedule->getCourses()->toArray() : [],
                'csrf_token' => $csrfToken,
                'courses' => $courses,
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
        $guest = $userManager->findUserByUsername("guest");

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
            $schedule->setSharp6am11am(2);
            $schedule->setSharp11am4pm(3);
            $schedule->setSharp4pm9pm(5);
            $schedule->setSharp9pm2am(4);

            $guest->addSchedule($schedule);
            $orm->persist($schedule);
            $orm->flush();
        }

        return $schedule;
    }

    /**
     * @param Schedule $schedule
     * @param EntityManager $orm
     * @return array
     */
    public static function getDemoCourses(Schedule $schedule, EntityManager $orm)
    {
        // TODO: remap demo functions to studysauce static functions used by testers?
        $courses = $schedule->getCourses()->filter(function (Course $b) {return $b->getType() == 'c';})->toArray();
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
            $course->setEndTime(date_add(clone $class1, new \DateInterval('P3WT50M')));

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
            $course->setEndTime(date_add(clone $class2, new \DateInterval('P3WT50M')));

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
            $course->setEndTime(date_add(clone $class3, new \DateInterval('P3WT50M')));

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
            $course->setEndTime(date_add(clone $class4, new \DateInterval('P3WT50M')));

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
            $course->setEndTime(date_add(clone $class5, new \DateInterval('P3WT50M')));

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
            $course->setEndTime(date_add(clone $class6, new \DateInterval('P3WT120M')));

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
            $course->setEndTime(date_add(clone $class7, new \DateInterval('P3WT90M')));

            $orm->persist($course);
            $orm->flush();
            $courses[] = $course;
            $schedule->addCourse($course);

            $course = new Course();
            $class8 = new \DateTime('last Sunday');
            $class8->setTime(2, 50, 0);
            $class8->sub(new \DateInterval('P3W'));
            $course->setSchedule($schedule);
            $course->setName(self::getRandomName());
            $course->setType('c');
            $course->setDotw(['Tu', 'Th']);
            $course->setStartTime($class8);
            $course->setEndTime(date_add(clone $class8, new \DateInterval('P3WT50M')));

            $orm->persist($course);
            $orm->flush();
            $courses[] = $course;
            $schedule->addCourse($course);

        }

        return $courses;
    }

    /**
     * @param Schedule $schedule
     * @return array
     */
    public static function getDemoOthers(Schedule $schedule, EntityManager $orm)
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
        $others = $schedule->getCourses()->filter(function (Course $b) {return $b->getType() == 'o';})->toArray();
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
            $course->setEndTime(date_add(clone $classO, new \DateInterval('P3WT60M')));

            $orm->persist($course);
            $orm->flush();
            $others[] = $course;
            $schedule->addCourse($course);
        }

        return array_values($others);
    }

    public static $examples = ['HIST 101', 'CALC 120', 'MAT 200', 'PHY 110', 'BUS 300', 'ANT 350', 'GEO 400', 'BIO 250', 'CHM 180', 'PHIL 102', 'ENG 100'];

    public static function getRandomName()
    {
        return self::$examples[array_rand(self::$examples, 1)];
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
            if (empty($c['cid'])) {
                $course = new Course();
                $course->setSchedule($schedule);
                $course->setName($c['className']);
            } else {
                /** @var $course Course */
                $course = $schedule->getCourses()->filter(function (Course $x) use($c) {return $x->getId() == $c['cid'];})->first();
                // figure out what changed
                if ($course->getName() != $c['className']) {
                    // remove old reoccurring events
                    $renamed[$c['cid']] = $course->getName();
                    $course->setName($c['className']);
                }
            }
            $dotw = explode(',', $c['dotw']);
            if(empty($dotw[0]))
                $dotw = [];

            // select the first day between the two dates
            if($c['type'] == 'o' && !in_array('Weekly', $dotw))
            {
                $classStart = new \DateTime($c['start']);
                $classEnd = new \DateTime($c['end']);

                // add repeating other events
                $startTerm = date_timestamp_set(new \DateTime(), strtotime('this week', $classStart->getTimestamp()));
                $startTerm->setTime(0, 0, 0);
                $endTerm = date_timestamp_set(new \DateTime(), strtotime('this week', $classEnd->getTimestamp()) + 604800);
                $endTerm->setTime(0, 0, 0);
                for ($w = $startTerm->getTimestamp(); $w < $endTerm->getTimestamp(); $w += 604800)
                {
                    for($d = 0; $d < 7; $d++)
                    {
                        $t = strtotime('this week', $w) + $d * 86400;
                        if ($t <= $classEnd->getTimestamp() && $t + 86400 >= $classStart->getTimestamp())
                        {
                            switch ($d)
                            {
                                case 0:
                                    $dotw[] = 'M';
                                    break;
                                case 1:
                                    $dotw[] = 'Tu';
                                    break;
                                case 2:
                                    $dotw[] = 'W';
                                    break;
                                case 3:
                                    $dotw[] = 'Th';
                                    break;
                                case 4:
                                    $dotw[] = 'F';
                                    break;
                                case 5:
                                    $dotw[] = 'Sa';
                                    break;
                                case 6:
                                    $dotw[] = 'Su';
                                    break;
                                default:
                                    continue;
                            }

                        }
                    }
                }
            }

            $dotw = array_unique($dotw);
            sort($dotw);

            $course->setDotw($dotw);
            $course->setType($c['type']);
            $course->setStartTime(new \DateTime($c['start']));
            $course->setEndTime(new \DateTime($c['end']));

            if (empty($c['cid'])) {
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

            //if ($isPaid && !$skipBuild)
            //    TODO: studysauce_rebuild_schedule($node, $entities, $added, $renamed);

        return $this->forward('StudySauceBundle:Schedule:index', ['_format' => 'tab']);
    }

    /**
     * @param Request $request
     */
    public function removeAction(Request $request)
    {

    }

    private static $institutions;

    /**
     * @param $search
     * @return ArrayCollection
     */
    public static function getInstitutions($search)
    {
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
<?php

namespace StudySauce\Bundle\Controller;

use Course1\Bundle\Entity\Course1;
use Course1\Bundle\Entity\Quiz1;
use Course1\Bundle\Entity\Quiz2;
use Course1\Bundle\Entity\Quiz3;
use Course1\Bundle\Entity\Quiz4;
use Course1\Bundle\Entity\Quiz5;
use Course1\Bundle\Entity\Quiz6;
use Doctrine\ORM\EntityManager;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * Class CourseController
 * @package StudySauce\Bundle\Controller
 */
class CourseController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('StudySauceBundle:Course:tab.html.php');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function menuAction()
    {
        /** @var User $user */
        $user = $this->getUser();
        $course1 = $user->getCourse1s()->first();
        $course2 = $user->getCourse2s()->first();
        $course3 = $user->getCourse3s()->first();
        return $this->render('Course1Bundle:Shared:menu.html.php', [
                'course1' => $course1,
                'course2' => $course2,
                'course3' => $course3
            ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function widgetAction()
    {
        /** @var User $user */
        $user = $this->getUser();
        $course1 = $user->getCourse1s()->first();
        $course2 = $user->getCourse2s()->first();
        $course3 = $user->getCourse3s()->first();
        return $this->render('StudySauceBundle:Course:widget.html.php', [
                'course1' => $course1,
                'course2' => $course2,
                'course3' => $course3
            ]);
    }

    /**
     * @param ContainerInterface $container
     */
    public static function getDemoCourses($container)
    {
        /** @var $orm EntityManager */
        $orm = $container->get('doctrine')->getManager();
        /** @var SecurityContext $context */
        /** @var TokenInterface $token */
        /** @var User $user */
        /** @var User $guest */
        if(!empty($context = $container->get('security.context')) && !empty($token = $context->getToken()) &&
            !empty($user = $token->getUser()) && $user->hasRole('ROLE_DEMO')) {
            $guest = $user;

        }

        /** @var Course1 $course */
        $course = $user->getCourse1s()->first();

        if(empty($course)) {
            $course = new Course1();
            $course->setUser($guest);
            $orm->persist($course);
            $user->addCourse1($course);
            $orm->flush();
        }

        $course->setWhyStudy('To get better grades!');
        $course->setLesson1(4);

        // store quiz results
        $quiz1 = new Quiz1();
        $quiz1->setCourse($course);
        $course->addQuiz1($quiz1);
        $quiz1->setEducation('college-freshman');
        $quiz1->setMindset('practice');
        $quiz1->setTimeManagement('advance');
        $quiz1->setDevices('off');
        $quiz1->setStudyMuch('two');
        $orm->persist($quiz1);

        // store quiz results
        $course->setLesson2(4);
        $quiz2 = new Quiz2();
        $quiz2->setCourse($course);
        $course->addQuiz2($quiz2);
        $quiz2->setGoalPerformance('90');
        $quiz2->setSpecific('specific');
        $quiz2->setMeasurable('measurable');
        $quiz2->setAchievable('achievable');
        $quiz2->setRelevant('relevant');
        $quiz2->setTimeBound('time-bound');
        $quiz2->setIntrinsic('intrinsic');
        $quiz2->setExtrinsic('extrinsic');
        $orm->persist($quiz2);

        $course->setLesson3(4);
        $quiz4 = new Quiz4();
        $quiz4->setCourse($course);
        $course->addQuiz4($quiz4);
        $quiz4->setMultitask('false');
        $quiz4->setDownside('shorter');
        $quiz4->setLowerScore('20');
        $quiz4->setDistraction('40');
        $orm->persist($quiz4);

        $course->setLesson4(4);
        $quiz3 = new Quiz3();
        $quiz3->setCourse($course);
        $course->addQuiz3($quiz3);
        $quiz3->setActiveMemory('active memory');
        $quiz3->setReferenceMemory('reference memory');
        $quiz3->setStudyGoal('retain the information');
        $quiz3->setProcrastinating('space out studying');
        $quiz3->setDeadlines('set deadlines');
        $quiz3->setPlan('study plan');
        $orm->persist($quiz3);

        $course->setLesson5(4);
        $quiz5 = new Quiz5();
        $quiz5->setCourse($course);
        $course->addQuiz5($quiz5);
        $quiz5->setBed(false);
        $quiz5->setMozart(false);
        $quiz5->setNature(true);
        $quiz5->setBreaks(false);
        $orm->persist($quiz5);

        $course->setLesson6(4);
        $quiz6 = new Quiz6();
        $quiz6->setCourse($course);
        $course->addQuiz6($quiz6);
        $quiz6->setHelp(['motivate', 'focus']);
        $quiz6->setAttribute('knows');
        $quiz6->setOften('weekly');
        $quiz6->setUsage(['gyms', 'dieting', 'churches']);
        $orm->persist($quiz6);

        $orm->merge($course);
        $orm->flush();

    }
}


<?php

namespace Admin\Bundle\Controller;

use Course1\Bundle\Entity\Course1;
use Course2\Bundle\Entity\Course2;
use Course3\Bundle\Entity\Course3;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManager;
use StudySauce\Bundle\Entity\Course;
use StudySauce\Bundle\Entity\Event;
use StudySauce\Bundle\Entity\Goal;
use StudySauce\Bundle\Entity\Schedule;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class PartnerController
 * @package StudySauce\Bundle\Controller
 */
class AdminController extends Controller
{
    private static $paidStr = '';

    /**
     * @param EntityManager $orm
     * @param Request $request
     * @param $joins
     * @return QueryBuilder
     */
    static function searchBuilder(EntityManager $orm, Request $request, &$joins = [])
    {
        $joins = [];
        /** @var QueryBuilder $qb */
        $qb = $orm->getRepository('StudySauceBundle:User')->createQueryBuilder('u');

        if(empty(self::$paidStr)) {
            $paidGroups = $orm->getRepository('StudySauceBundle:Group')->createQueryBuilder('g')
                ->select('g.id')
                ->andWhere('g.roles LIKE \'%s:9:"ROLE_PAID"%\'')
                ->getQuery()
                ->getArrayResult();
            self::$paidStr = implode(', ', array_map(function ($x) { return $x['id']; }, $paidGroups));
        }

        if(!empty($search = $request->get('search'))) {
            if(strpos($search, '%') === false) {
                $search = '%' . $search . '%';
            }
            $qb = $qb->andWhere('u.first LIKE :search OR u.last LIKE :search OR u.email LIKE :search')
                ->setParameter('search', $search);
        }

        if(!empty($role = $request->get('role'))) {
            if($role == 'ROLE_STUDENT') {
                $qb = $qb->andWhere('u.roles NOT LIKE \'%s:12:"ROLE_ADVISER"%\' AND u.roles NOT LIKE \'%s:19:"ROLE_MASTER_ADVISER"%\' AND u.roles NOT LIKE \'%s:12:"ROLE_PARTNER"%\' AND u.roles NOT LIKE \'%s:11:"ROLE_PARENT"%\'');
            }
            else if($role == 'ROLE_PAID') {
                if(!in_array('g', $joins)) {
                    $qb = $qb->leftJoin('u.groups', 'g');
                    $joins[] = 'g';
                }
                $qb = $qb->andWhere('u.roles LIKE \'%s:9:"ROLE_PAID"%\' OR g.id IN (' . self::$paidStr . ')');
            }
            else {
                $qb = $qb->andWhere('u.roles LIKE \'%s:' . strlen($role) . ':"' . $role . '"%\'');
            }
        }

        if(!empty($group = $request->get('group'))) {
            if(!in_array('g', $joins)) {
                $qb = $qb->leftJoin('u.groups', 'g');
                $joins[] = 'g';
            }
            $qb = $qb->andWhere('g.id=:gid')->setParameter('gid', intval($group));
        }

        if(!empty($last = $request->get('last'))) {
            $qb = $qb->andWhere('u.last LIKE \'' . $last . '\'');
        }

        if(!empty($completed = $request->get('completed'))) {
            if(!in_array('c1', $joins)) {
                $qb = $qb
                    ->leftJoin('u.course1s', 'c1')
                    ->leftJoin('u.course2s', 'c2')
                    ->leftJoin('u.course3s', 'c3');
                $joins[] = 'c1';
            }
            if(strpos($completed, '1') !== false)
                $qb = $qb->andWhere('c1.lesson1=4 AND c1.lesson2=4 AND c1.lesson3=4 AND c1.lesson4=4 AND c1.lesson5=4 AND c1.lesson6=4');
            if(strpos($completed, '2') !== false)
                $qb = $qb->andWhere('c2.lesson1=4 AND c2.lesson2=4 AND c2.lesson3=4 AND c2.lesson4=4 AND c2.lesson5=4');
            if(strpos($completed, '3') !== false)
                $qb = $qb->andWhere('c3.lesson1=4 AND c3.lesson2=4 AND c3.lesson3=4 AND c3.lesson4=4 AND c3.lesson5=4');
        }

        if(!empty($paid = $request->get('paid'))) {
            if(!in_array('g', $joins)) {
                $qb = $qb->leftJoin('u.groups', 'g');
                $joins[] = 'g';
            }
            if($paid == 'yes') {
                $qb = $qb->andWhere('u.roles LIKE \'%s:9:"ROLE_PAID"%\' OR g.id IN (' . self::$paidStr . ')');
            }
            else {
                $qb = $qb->andWhere('u.roles NOT LIKE \'%s:9:"ROLE_PAID"%\' AND g.id NOT IN (' . self::$paidStr . ')');
            }
        }

        if(!empty($goals = $request->get('goals'))) {
            if(!in_array('goals', $joins)) {
                $qb = $qb->leftJoin('u.goals', 'goals');
                $joins[] = 'goals';
            }
            if($goals == 'yes') {
                $qb = $qb->andWhere('goals.id IS NOT NULL');
            }
            else {
                $qb = $qb->andWhere('goals.id IS NULL');
            }
        }

        if(!empty($deadlines = $request->get('deadlines'))) {
            if(!in_array('deadlines', $joins)) {
                $qb = $qb->leftJoin('u.deadlines', 'deadlines');
                $joins[] = 'deadlines';
            }
            if($deadlines == 'yes') {
                $qb = $qb->andWhere('deadlines.id IS NOT NULL');
            }
            else {
                $qb = $qb->andWhere('deadlines.id IS NULL');
            }
        }

        if(!empty($plans = $request->get('plans'))) {
            if(!in_array('plans', $joins)) {
                $qb = $qb->leftJoin('u.schedules', 'plans');
                $joins[] = 'plans';
            }
            if($plans == 'yes') {
                $qb = $qb->andWhere('plans.id IS NOT NULL');
            }
            else {
                $qb = $qb->andWhere('plans.id IS NULL');
            }
        }

        if(!empty($partners = $request->get('partners'))) {
            if(!in_array('partners', $joins)) {
                $qb = $qb->leftJoin('u.partnerInvites', 'partners');
                $joins[] = 'partners';
            }
            if($partners == 'yes') {
                $qb = $qb->andWhere('partners.id IS NOT NULL');
            }
            else {
                $qb = $qb->andWhere('partners.id IS NULL');
            }
        }

        return $qb;
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        set_time_limit(0);
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var $user User */
        $user = $this->getUser();
        if(!$user->hasRole('ROLE_ADMIN')) {
            throw new AccessDeniedHttpException();
        }

        // count total so we know the max pages
        $total = self::searchBuilder($orm, $request)
            ->select('COUNT(DISTINCT u.id)')
            ->getQuery()
            ->getSingleScalarResult();

        // max pagination to search count
        if(!empty($page = $request->get('page'))) {
            if($page == 'last') {
                $page = $total / 25;
            }
            $resultOffset = (min(max(1, ceil($total / 25)), max(1, intval($page))) - 1) * 25;
        }
        else {
            $resultOffset = 0;
        }

        // get the actual list of users
        /** @var QueryBuilder $users */
        $users = self::searchBuilder($orm, $request, $joins)->distinct(true)->select('u');

        // figure out how to sort
        if(!empty($order = $request->get('order'))) {
            $field = explode(' ', $order)[0];
            $direction = explode(' ', $order)[1];
            if($direction != 'ASC' && $direction != 'DESC')
                $direction = 'DESC';
            // no extra join information needed
            if($field == 'created' || $field == 'lastLogin' || $field == 'last') {
                $users = $users->orderBy('u.' . $field, $direction);
            }
            if($field == 'completed') {
                $users = $users
                    ->leftJoin('u.course1s', 'c1')
                    ->leftJoin('u.course2s', 'c2')
                    ->leftJoin('u.course3s', 'c3')
                    ->addOrderBy('c1.lesson1 + c1.lesson2 + c1.lesson3 + c1.lesson4 + c1.lesson5 + c1.lesson6 + c1.lesson7 + c2.lesson1 + c2.lesson2 + c2.lesson3 + c2.lesson4 + c2.lesson5 + c3.lesson1 + c3.lesson2 + c3.lesson3 + c3.lesson4 + c3.lesson5', $direction)
                    ->addOrderBy('c1.lesson1 + c1.lesson2 + c1.lesson3 + c1.lesson4 + c1.lesson5 + c1.lesson6 + c1.lesson7', $direction)
                    ->addOrderBy('c2.lesson1 + c2.lesson2 + c2.lesson3 + c2.lesson4 + c2.lesson5', $direction)
                    ->addOrderBy('c3.lesson1 + c3.lesson2 + c3.lesson3 + c3.lesson4 + c3.lesson5', $direction);
                $joins[] = 'c1';
            }
        }
        else {
            $users = $users->orderBy('u.lastLogin', 'DESC');
        }

        $users = $users
            ->setFirstResult($resultOffset)
            ->setMaxResults(25)
            ->getQuery()
            ->getResult();



        // get all the interesting aggregate counts
        $yesterday = new \DateTime('yesterday');
        $signups = self::searchBuilder($orm, $request)
            ->select('COUNT(DISTINCT u.id)')
            ->andWhere('u.created > :yesterday')
            ->setParameter('yesterday', $yesterday)
            ->getQuery()
            ->getSingleScalarResult();

        $visitors = self::searchBuilder($orm, $request)
            ->select('COUNT(DISTINCT u.id)')
            ->andWhere('u.lastLogin > :yesterday')
            ->setParameter('yesterday', $yesterday)
            ->getQuery()
            ->getSingleScalarResult();

        $parents = self::searchBuilder($orm, $request)
            ->select('COUNT(DISTINCT u.id)')
            ->andWhere('u.roles LIKE \'%s:11:"ROLE_PARENT"%\'')
            ->getQuery()
            ->getSingleScalarResult();

        $partners = self::searchBuilder($orm, $request)
            ->select('COUNT(DISTINCT u.id)')
            ->andWhere('u.roles LIKE \'%s:12:"ROLE_PARTNER"%\'')
            ->getQuery()
            ->getSingleScalarResult();

        $advisers = self::searchBuilder($orm, $request)
            ->select('COUNT(DISTINCT u.id)')
            ->andWhere('u.roles LIKE \'%s:12:"ROLE_ADVISER"%\' OR u.roles LIKE \'%s:19:"ROLE_MASTER_ADVISER"%\'')
            ->getQuery()
            ->getSingleScalarResult();

        $students = self::searchBuilder($orm, $request)
            ->select('COUNT(DISTINCT u.id)')
            ->andWhere('u.roles NOT LIKE \'%s:12:"ROLE_ADVISER"%\'')
            ->andWhere('u.roles NOT LIKE \'%s:19:"ROLE_MASTER_ADVISER"%\'')
            ->andWhere('u.roles NOT LIKE \'%s:12:"ROLE_PARTNER"%\'')
            ->andWhere('u.roles NOT LIKE \'%s:11:"ROLE_PARENT"%\'')
            ->getQuery()
            ->getSingleScalarResult();

        /** @var QueryBuilder $paid */
        $paid = self::searchBuilder($orm, $request, $joins);
        if(!in_array('g', $joins)) {
            $paid = $paid->leftJoin('u.groups', 'g');
        }
        $paid = $paid->select('COUNT(DISTINCT u.id)')
            ->andWhere('u.roles LIKE \'%s:9:"ROLE_PAID"%\' OR g.id IN (' . self::$paidStr . ')')
            ->getQuery()
            ->getSingleScalarResult();
        /** @var int $paid */

        /** @var QueryBuilder $completed */
        $completed = self::searchBuilder($orm, $request, $joins);
        if(!in_array('c1', $joins)) {
            $completed = $completed
                ->leftJoin('u.course1s', 'c1')
                ->leftJoin('u.course2s', 'c2')
                ->leftJoin('u.course3s', 'c3');
        }
        $completed = $completed->select('COUNT(DISTINCT u.id)')
            ->andWhere('c1.lesson1=4 AND c1.lesson2=4 AND c1.lesson3=4 AND c1.lesson4=4 AND c1.lesson5=4 AND c1.lesson6=4')
            ->andWhere('c2.lesson1=4 AND c2.lesson2=4 AND c2.lesson3=4 AND c2.lesson4=4 AND c2.lesson5=4')
            ->andWhere('c3.lesson1=4 AND c3.lesson2=4 AND c3.lesson3=4 AND c3.lesson4=4 AND c3.lesson5=4')
            ->getQuery()
            ->getSingleScalarResult();

        /** @var QueryBuilder $goals */
        $goals = self::searchBuilder($orm, $request, $joins);
        if(!in_array('goals', $joins)) {
            $goals = $goals->leftJoin('u.goals', 'goals');
        }
        $goals = $goals->select('COUNT(DISTINCT u.id)')
            ->andWhere('goals.id IS NOT NULL')
            ->getQuery()
            ->getSingleScalarResult();


        /** @var QueryBuilder $deadlines */
        $deadlines = self::searchBuilder($orm, $request, $joins);
        if(!in_array('deadlines', $joins)) {
            $deadlines = $deadlines->leftJoin('u.deadlines', 'deadlines');
        }
        $deadlines = $deadlines->select('COUNT(DISTINCT u.id)')
            ->andWhere('deadlines.id IS NOT NULL')
            ->getQuery()
            ->getSingleScalarResult();

        /** @var QueryBuilder $plans */
        $plans = self::searchBuilder($orm, $request, $joins);
        if(!in_array('plans', $joins)) {
            $plans = $plans->leftJoin('u.schedules', 'plans');
        }
        $plans = $plans->select('COUNT(DISTINCT u.id)')
            ->andWhere('plans.id IS NOT NULL')
            ->getQuery()
            ->getSingleScalarResult();

        /** @var QueryBuilder $partnerTotal */
        $partnerTotal = self::searchBuilder($orm, $request, $joins);
        if(!in_array('partners', $joins)) {
            $partnerTotal = $partnerTotal->leftJoin('u.partnerInvites', 'partners');
        }
        $partnerTotal = $partnerTotal->select('COUNT(DISTINCT u.id)')
            ->andWhere('partners.id IS NOT NULL')
            ->getQuery()
            ->getSingleScalarResult();

        // get the groups for use in dropdown
        $groups = $orm->getRepository('StudySauceBundle:Group')->findAll();

        return $this->render('AdminBundle:Admin:index.html.php', [
                'groups' => $groups,
                'users' => $users,
                'visitors' => $visitors,
                'signups' => $signups,
                'parents' => $parents,
                'partners' => $partners,
                'advisers' => $advisers,
                'paid' => $paid,
                'students' => $students,
                'completed' => $completed,
                'goals' => $goals,
                'deadlines' => $deadlines,
                'plans' => $plans,
                'partnerTotal' => $partnerTotal,
                'total' => $total
            ]);
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeUserAction(Request $request) {

        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var $user User */
        $user = $this->getUser();
        if(!$user->hasRole('ROLE_ADMIN')) {
            throw new AccessDeniedHttpException();
        }

        /** @var User $u */
        $u = $orm->getRepository('StudySauceBundle:User')->findOneBy(['id' => $request->get('userId')]);
        if(!empty($u)) {
            // remove all entities attached
            foreach($u->getVisits()->toArray() as $i => $v) {
                $u->removeVisit($v);
                $orm->remove($v);
            }
            foreach($u->getCourse1s()->toArray() as $i => $c1) {
                /** @var Course1 $c1 */
                foreach($c1->getQuiz1s()->toArray() as $j => $q1) {
                    $c1->removeQuiz1($q1);
                    $orm->remove($q1);
                }
                foreach($c1->getQuiz2s()->toArray() as $j => $q2) {
                    $c1->removeQuiz2($q2);
                    $orm->remove($q2);
                }
                foreach($c1->getQuiz3s()->toArray() as $j => $q3) {
                    $c1->removeQuiz3($q3);
                    $orm->remove($q3);
                }
                foreach($c1->getQuiz4s()->toArray() as $j => $q4) {
                    $c1->removeQuiz4($q4);
                    $orm->remove($q4);
                }
                foreach($c1->getQuiz5s()->toArray() as $j => $q5) {
                    $c1->removeQuiz5($q5);
                    $orm->remove($q5);
                }
                foreach($c1->getQuiz6s()->toArray() as $j => $q6) {
                    $c1->removeQuiz6($q6);
                    $orm->remove($q6);
                }
                $u->removeCourse1($c1);
                $orm->remove($c1);
            }
            foreach($u->getCourse2s()->toArray() as $i => $c2) {
                /** @var Course2 $c2 */
                foreach($c2->getInterleaving()->toArray() as $j => $q1) {
                    $c2->removeInterleaving($q1);
                    $orm->remove($q1);
                }
                foreach($c2->getStudyMetrics()->toArray() as $j => $q2) {
                    $c2->removeStudyMetric($q2);
                    $orm->remove($q2);
                }
                foreach($c2->getStudyPlan()->toArray() as $j => $q3) {
                    $c2->removeStudyPlan($q3);
                    $orm->remove($q3);
                }
                foreach($c2->getStudyTests()->toArray() as $j => $q4) {
                    $c2->removeStudyTest($q4);
                    $orm->remove($q4);
                }
                foreach($c2->getTestTaking()->toArray() as $j => $q5) {
                    $c2->removeTestTaking($q5);
                    $orm->remove($q5);
                }
                $u->removeCourse2($c2);
                $orm->remove($c2);
            }
            foreach($u->getCourse3s()->toArray() as $i => $c3) {
                /** @var Course3 $c3 */
                foreach($c3->getActiveReading()->toArray() as $j => $q1) {
                    $c3->removeActiveReading($q1);
                    $orm->remove($q1);
                }
                foreach($c3->getGroupStudy()->toArray() as $j => $q2) {
                    $c3->removeGroupStudy($q2);
                    $orm->remove($q2);
                }
                foreach($c3->getSpacedRepetition()->toArray() as $j => $q3) {
                    $c3->removeSpacedRepetition($q3);
                    $orm->remove($q3);
                }
                foreach($c3->getStrategies()->toArray() as $j => $q4) {
                    $c3->removeStrategy($q4);
                    $orm->remove($q4);
                }
                foreach($c3->getTeaching()->toArray() as $j => $q5) {
                    $c3->removeTeaching($q5);
                    $orm->remove($q5);
                }
                $u->removeCourse3($c3);
                $orm->remove($c3);
            }
            foreach($u->getMessages()->toArray() as $i => $m) {
                $u->removeMessage($m);
                $orm->remove($m);
            }
            foreach($u->getFiles()->toArray() as $i => $f) {
                $u->removeFile($f);
                $orm->remove($f);
            }
            foreach($u->getGoals()->toArray() as $i => $g) {
                /** @var Goal $g */
                foreach($g->getClaims()->toArray() as $j => $c) {
                    $g->removeClaim($c);
                    $orm->remove($c);
                }
                $u->removeGoal($g);
                $orm->remove($g);
            }
            foreach($u->getGroups()->toArray() as $i => $gr) {
                $u->removeGroup($gr);
            }
            foreach($u->getGroupInvites()->toArray() as $i => $gri) {
                $u->removeGroupInvite($gri);
                $orm->remove($gri);
            }
            foreach($u->getParentInvites()->toArray() as $i => $p) {
                $u->removeParentInvite($p);
                $orm->remove($p);
            }
            foreach($u->getPartnerInvites()->toArray() as $i => $pa) {
                $u->removePartnerInvite($pa);
                $orm->remove($pa);
            }
            foreach($u->getPayments()->toArray() as $i => $pay) {
                $u->removePayment($pay);
                $orm->remove($pay);
            }
            foreach($u->getSchedules()->toArray() as $i => $s) {
                /** @var Schedule $s */
                foreach($s->getEvents()->toArray() as $j => $e) {
                    /** @var Event $e */
                    if(!empty($ac = $e->getActive()))
                        $orm->remove($ac);
                    if(!empty($pr = $e->getPrework()))
                        $orm->remove($pr);
                    if(!empty($ot = $e->getOther()))
                        $orm->remove($ot);
                    if(!empty($sp = $e->getSpaced()))
                        $orm->remove($sp);
                    if(!empty($te = $e->getTeach()))
                        $orm->remove($te);
                    $s->removeEvent($e);
                    $orm->remove($e);
                }
                foreach($s->getWeeks()->toArray() as $j => $w) {
                    $s->removeWeek($w);
                    $orm->remove($w);
                }
                foreach($s->getCourses()->toArray() as $j => $co) {
                    /** @var Course $co */
                    foreach($co->getCheckins()->toArray() as $k => $ch) {
                        $co->removeCheckin($ch);
                        $orm->remove($ch);
                    }
                    $s->removeCourse($co);
                    $orm->remove($co);
                }
                $u->removeSchedule($s);
                $orm->remove($s);
            }
            foreach($u->getDeadlines()->toArray() as $i => $d) {
                $u->removeDeadline($d);
                $orm->remove($d);
            }
            foreach($u->getStudentInvites()->toArray() as $i => $st) {
                $u->removeStudentInvite($st);
                $orm->remove($st);
            }
            foreach($u->getInvitedStudents()->toArray() as $i => $is) {
                $u->removeInvitedStudent($is);
                $orm->remove($is);
            }
            foreach($u->getInvitedPartners()->toArray() as $i => $ip) {
                $u->removeInvitedPartner($ip);
                $orm->remove($ip);
            }
            foreach($u->getInvitedParents()->toArray() as $i => $ipa) {
                $u->removeInvitedParent($ipa);
                $orm->remove($ipa);
            }
            foreach($u->getInvitedGroups()->toArray() as $i => $ig) {
                $u->removeInvitedGroup($ig);
                $orm->remove($ig);
            }

            $orm->remove($u);
            $orm->flush();
        }

        return $this->indexAction($request);
    }
}

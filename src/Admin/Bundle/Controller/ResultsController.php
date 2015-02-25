<?php

namespace Admin\Bundle\Controller;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManager;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class PartnerController
 * @package StudySauce\Bundle\Controller
 */
class ResultsController extends Controller
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
            if(!in_array('g', $joins)) {
                $qb = $qb->leftJoin('u.groups', 'g');
                $joins[] = 'g';
            }
            $qb = $qb->andWhere('u.first LIKE :search OR u.last LIKE :search OR u.email LIKE :search OR g.name LIKE :search OR g.description LIKE :search')
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
            if($group == 'nogroup') {
                $qb = $qb->andWhere('g.id IS NULL');
            }
            else {
                $qb = $qb->andWhere('g.id=:gid')->setParameter('gid', intval($group));
            }
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
            if(($pos = strpos($completed, '1')) !== false) {
                if(substr($completed, $pos - 1, 1) != '!')
                    $qb = $qb->andWhere('c1.lesson1=4 AND c1.lesson2=4 AND c1.lesson3=4 AND c1.lesson4=4 AND c1.lesson5=4 AND c1.lesson6=4');
                else
                    $qb = $qb->andWhere('(c1.lesson1<4 OR c1.lesson1 IS NULL) OR (c1.lesson2<4 OR c1.lesson2 IS NULL) OR (c1.lesson3<4 OR c1.lesson3 IS NULL) OR (c1.lesson4<4 OR c1.lesson4 IS NULL) OR (c1.lesson5<4 OR c1.lesson5 IS NULL) OR (c1.lesson6<4 OR c1.lesson6 IS NULL)');
            }
            if(($pos = strpos($completed, '2')) !== false) {
                if(substr($completed, $pos - 1, 1) != '!')
                    $qb = $qb->andWhere('c2.lesson1=4 AND c2.lesson2=4 AND c2.lesson3=4 AND c2.lesson4=4 AND c2.lesson5=4');
                else
                    $qb = $qb->andWhere('(c2.lesson1<4 OR c2.lesson1 IS NULL) OR (c2.lesson2<4 OR c2.lesson2 IS NULL) OR (c2.lesson3<4 OR c2.lesson3 IS NULL) OR (c2.lesson4<4 OR c2.lesson4 IS NULL) OR (c2.lesson5<4 OR c2.lesson5 IS NULL)');
            }
            if(($pos = strpos($completed, '3')) !== false) {
                if(substr($completed, $pos - 1, 1) != '!')
                    $qb = $qb->andWhere('c3.lesson1=4 AND c3.lesson2=4 AND c3.lesson3=4 AND c3.lesson4=4 AND c3.lesson5=4');
                else
                    $qb = $qb->andWhere('(c3.lesson1<4 OR c3.lesson1 IS NULL) OR (c3.lesson2<4 OR c3.lesson2 IS NULL) OR (c3.lesson3<4 OR c3.lesson3 IS NULL) OR (c3.lesson4<4 OR c3.lesson4 IS NULL) OR (c3.lesson5<4 OR c3.lesson5 IS NULL)');
            }
        }


        // check for individual lesson filters
        for($i = 1; $i <= 17; $i++) {
            if(!empty($lesson = $request->get('lesson' . $i))) {
                if (!in_array('c1', $joins)) {
                    $qb = $qb
                        ->leftJoin('u.course1s', 'c1')
                        ->leftJoin('u.course2s', 'c2')
                        ->leftJoin('u.course3s', 'c3');
                    $joins[] = 'c1';
                }
                if($i > 12) {
                    $l = $i - 12;
                    $c = 3;
                }
                elseif($i > 7) {
                    $l = $i - 7;
                    $c = 2;
                }
                else {
                    $l = $i;
                    $c = 1;
                }
                if($lesson == 'yes') {
                    $qb = $qb->andWhere('c' . $c . '.lesson' . $l . '=4');
                }
                else {
                    $qb = $qb->andWhere('c' . $c . '.lesson' . $l . '<4 OR ' . 'c' . $c . '.lesson' . $l . ' IS NULL');
                }
            }
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

        return $qb;
    }

    /**
     * @param Request $request
     * @return array
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
                if(!in_array('c1', $joins)) {
                    $users = $users
                        ->leftJoin('u.course1s', 'c1')
                        ->leftJoin('u.course2s', 'c2')
                        ->leftJoin('u.course3s', 'c3');
                }
                $users = $users
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

        /** @var QueryBuilder $torch */
        $torch = self::searchBuilder($orm, $request, $joins);
        if(!in_array('g', $joins)) {
            $torch = $torch->leftJoin('u.groups', 'g');
        }
        $torch = $torch->select('COUNT(DISTINCT u.id)')
            ->andWhere('g.name LIKE \'%torch%\'')
            ->getQuery()
            ->getSingleScalarResult();
        /** @var int $torch */

        /** @var QueryBuilder $csa */
        $csa = self::searchBuilder($orm, $request, $joins);
        if(!in_array('g', $joins)) {
            $csa = $csa->leftJoin('u.groups', 'g');
        }
        $csa = $csa->select('COUNT(DISTINCT u.id)')
            ->andWhere('g.name LIKE \'%csa%\'')
            ->getQuery()
            ->getSingleScalarResult();
        /** @var int $csa */

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


        // get the groups for use in dropdown
        $groups = $orm->getRepository('StudySauceBundle:Group')->findAll();

        return $this->render('AdminBundle:Results:tab.html.php', [
            'users' => $users,
            'groups' => $groups,
            'parents' => $parents,
            'partners' => $partners,
            'advisers' => $advisers,
            'paid' => $paid,
            'students' => $students,
            'torch' => $torch,
            'csa' => $csa,
            'completed' => $completed,
            'total' => $total,
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function userAction(Request $request)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('AdminBundle:Results:result.html.php', [
            'orm' => $orm,
            'course1' => $user->getCourse1s()->first(),
            'course2' => $user->getCourse2s()->first(),
            'course3' => $user->getCourse3s()->first()
        ]);
    }
}
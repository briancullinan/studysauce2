<?php

namespace Admin\Bundle\Controller;

use Course1\Bundle\Course1Bundle;
use Course1\Bundle\Entity\Course1;
use Course2\Bundle\Course2Bundle;
use Course2\Bundle\Entity\Course2;
use Course3\Bundle\Course3Bundle;
use Course3\Bundle\Entity\Course3;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\PartnerInvite;
use StudySauce\Bundle\Entity\Schedule;
use StudySauce\Bundle\Entity\User;
use StudySauce\Bundle\Entity\Visit;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class PartnerController
 * @package StudySauce\Bundle\Controller
 */
class AdminController extends Controller
{

    /**
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
        if($user->hasRole('ROLE_ADMIN')) {
            $groups = $orm->getRepository('StudySauceBundle:Group')->findAll();
            $users = $orm->getRepository('StudySauceBundle:User')->findAll();
        }
        elseif($user->hasRole('ROLE_ADVISER') || $user->hasRole('ROLE_MASTER_ADVISER')) {
            $groups = $user->getGroups()->toArray();
            $users = [];
            foreach ($groups as $i => $g) {
                /** @var Group $g */
                $users = array_merge($users, $g->getUsers()->toArray());
            }
        }
        else {
            $users = [];
            $groups = [];
        }

        /** @var PartnerInvite $partner */
        $partner = $orm->getRepository('StudySauceBundle:PartnerInvite')->findBy(['partner' => $user->getId()]);
        foreach($partner as $j => $p)
        {
            /** @var PartnerInvite $p */
            $users[] = $p->getUser();
        }

        $users = array_unique($users);

        // show sessions
        /*
        $sessions = [];
        $dql = 'SELECT v.time, v.id FROM StudySauceBundle:Session v ORDER BY v.time DESC';
        $query = $orm->createQuery($dql);
        $sids = $query->execute();
        $dql = 'SELECT v.created, v.path, IDENTITY(v.user) AS uid FROM StudySauceBundle:Visit v WHERE v.session IN (\'' . implode('\',\'', array_map(function ($s) {return $s['id'];}, $sids)) . '\') GROUP BY v.session ORDER BY v.created DESC';
        $query = $orm->createQuery($dql);
        $sessions = $query->execute();
        foreach($sids as $j => $s)
        {
            $visits = $orm->getRepository('StudySauceBundle:Visit');
            $criteria = Criteria::create()
                ->orWhere(Criteria::expr()->contains('path', '/metrics'))
                ->orWhere(Criteria::expr()->contains('path', '/schedule'))
                ->orWhere(Criteria::expr()->contains('path', '/account'))
                ->orWhere(Criteria::expr()->contains('path', '/profile'))
                ->orWhere(Criteria::expr()->contains('path', '/premium'))
                ->orWhere(Criteria::expr()->contains('path', '/partner'))
                ->orWhere(Criteria::expr()->contains('path', '/goals'))
                ->orWhere(Criteria::expr()->contains('path', '/plan'))
                ->orWhere(Criteria::expr()->contains('path', '/deadlines'));
            $v = $visits
                ->matching(Criteria::create()->where(Criteria::expr()->eq('session', $s['id'])))
                ->matching($criteria)->first();
            if(empty($v))
            {
                $v = $visits
                    ->matching(Criteria::create()
                            ->where(Criteria::expr()->eq('session', $s['id'])))->first();
                if(!empty($v))
                    $v->setPath('/');
            }
            if(!empty($v)) {
                if($v->getCreated() > $yesterday)
                    $visitors++;
                $sessions[$v->getCreated()->getTimestamp()] = $v;
            }
        }
        */

        $yesterday = new \DateTime('yesterday');
        $visitors = 0;
        $signups = 0;
        $listedUsers = [];
        $parents = 0;
        $partners = 0;
        $advisers = 0;
        $paid = 0;
        $students = 0;
        $completed = 0;
        $goals = 0;
        $deadlines = 0;
        $plans = 0;
        $partnerTotal = 0;
        foreach($users as $i => $u) {
            /** @var User $u */

            if ($u->getCreated() > $yesterday) {
                $signups++;
            }

            /** @var Visit $v */
            if(!empty($v = $u->getVisits()->slice(0, 1)) && ($v = $v[0])
                && $v->getCreated() > $yesterday) {
                $visitors++;
            }

            if($u->hasRole('ROLE_PARENT')) {
                $parents++;
            }

            if($u->hasRole('ROLE_PARTNER')) {
                $partners++;
            }

            if($u->hasRole('ROLE_ADVISER') || $u->hasRole('ROLE_MASTER_ADVISER')) {
                $advisers++;
            }

            if($u->hasRole('ROLE_PAID')) {
                $paid++;
            }

            if(!$u->hasRole('ROLE_PARENT') && !$u->hasRole('ROLE_PARTNER') && !$u->hasRole('ROLE_ADVISER') &&
                !$u->hasRole('ROLE_MASTER_ADVISER')) {
                $students++;
            }

            if($u->getCompleted() == 100) {
                $completed++;
            }

            if($u->getGoals()->count() > 0) {
                $goals++;
            }

            if($u->getDeadlines()->count() > 0) {
                $deadlines++;
            }

            if($u->getSchedules()->count() > 0) {
                $plans++;
            }

            if($u->getPartnerInvites()->count() > 0) {
                $partnerTotal++;
            }

            $listedUsers[$u->getId()] = $u;
        }

        $keys = array_map(function (User $u) {
                return empty($u->getLastLogin()) ? $u->getCreated()->getTimestamp() : $u->getLastLogin()->getTimestamp();}, $listedUsers);
        array_multisort($keys, SORT_DESC, SORT_NUMERIC, $listedUsers);

        $listedUsers = array_splice($listedUsers, $request->get('page') ?: 0, 25);

        return $this->render('AdminBundle:Admin:index.html.php', [
                'groups' => $groups,
                'users' => $listedUsers,
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
                'partnerTotal' => $partnerTotal
            ]);
    }



}

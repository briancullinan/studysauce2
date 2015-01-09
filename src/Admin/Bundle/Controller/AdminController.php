<?php

namespace Admin\Bundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\PartnerInvite;
use StudySauce\Bundle\Entity\User;
use StudySauce\Bundle\Entity\Visit;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
    public function indexAction()
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
        // show sessions
        $sessions = [];
        $visitors = 0;
        $signups = 0;
        $yesterday = new \DateTime('yesterday');
        foreach($users as $i => $u) {
            /** @var User $u */
            if($u->hasRole('ROLE_ADVISER') || $u->hasRole('ROLE_MASTER_ADVISER') || $u->hasRole('ROLE_ADMIN') ||
                $u->getId() == $user->getId())
                continue;


            if($u->getCreated() > $yesterday)
                $signups++;
            $dql = 'SELECT DISTINCT SUBSTRING(v.created,1,10) AS created, v.session FROM StudySauceBundle:Visit v WHERE v.user=' . $u->getId() . ' GROUP BY v.session ORDER BY v.created DESC';
            $query = $orm->createQuery($dql);
            $sids = $query->execute();
            $count = 0;
            foreach($sids as $j => $s)
            {
                if($count == 10)
                    break;
                /** @var ArrayCollection $visits */
                $visits = $u->getVisits();
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
                /** @var Visit $v */
                $v = $visits
                    ->matching(Criteria::create()->where(Criteria::expr()->eq('session', $s['session'])))
                    ->matching($criteria)->first();
                if(empty($v))
                {
                    $v = $visits
                        ->matching(Criteria::create()
                                ->where(Criteria::expr()->eq('session', $s['session'])))->first();
                    if(!empty($v))
                        $v->setPath('/');
                }
                if(!empty($v)) {
                    if($v->getCreated() > $yesterday)
                        $visitors++;
                    $sessions[$v->getCreated()->getTimestamp()] = $v;
                }
                $count++;
            }
        }
        krsort($sessions);

        $users = array_unique($users);
        $keys = array_map(function (User $u) {
                return empty($u->getLastLogin()) ? $u->getCreated()->getTimestamp() : $u->getLastLogin()->getTimestamp();}, $users);
        array_multisort($keys, SORT_DESC, SORT_NUMERIC, $users);
        return $this->render('AdminBundle:Admin:index.html.php', [
                'sessions' => $sessions,
                'groups' => $groups,
                'users' => $users,
                'visitors' => $visitors,
                'signups' => $signups
            ]);
    }

}

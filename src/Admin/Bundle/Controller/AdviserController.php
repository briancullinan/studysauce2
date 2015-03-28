<?php


namespace Admin\Bundle\Controller;


use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\PartnerInvite;
use StudySauce\Bundle\Entity\User;
use StudySauce\Bundle\Entity\Visit;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class AdviserController
 */
class AdviserController extends Controller
{

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function userlistAction()
    {
        set_time_limit(0);
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var $user User */
        $user = $this->getUser();
        if(!$user->hasRole('ROLE_ADVISER') && !$user->hasRole('ROLE_MASTER_ADVISER') && !$user->hasRole('ROLE_PARTNER')) {
            throw new AccessDeniedHttpException();
        }
        elseif($user->hasRole('ROLE_ADVISER') || $user->hasRole('ROLE_MASTER_ADVISER')) {
            $users = [];
            foreach ($user->getGroups()->toArray() as $i => $g) {
                /** @var Group $g */
                $users = array_merge($users, $g->getUsers()->toArray());
            }
        }
        else {
            $users = [];
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
        $sessions = $orm->getRepository('StudySauceBundle:Visit')->createQueryBuilder('v')
            ->leftJoin('v.user', 'u')
            ->select(['v', 'u'])
            ->andWhere('v.user IN (\'' . implode('\',\'', array_map(function (User $u) {return $u->getId();}, $users)) . '\')')
            ->andWhere('v.path LIKE \'/schedule%\' OR v.path LIKE \'/metrics%\' OR v.path LIKE \'/goals%\' OR v.path LIKE \'/plan%\' OR v.path LIKE \'/course%\' OR v.path LIKE \'/account%\' OR v.path LIKE \'/premium%\' OR v.path LIKE \'/deadlines%\' OR v.path LIKE \'/checkin%\' OR v.path LIKE \'/home%\' OR v.path LIKE \'/partner%\' OR v.path LIKE \'/profile%\' OR v.path LIKE \'/calculator%\'')
            ->andWhere('u.roles NOT LIKE \'%ADVISER%\' AND u.roles NOT LIKE \'%ADMIN%\'')
            ->groupBy('v.session')
            ->orderBy('v.created', 'DESC')
            ->getQuery()
            ->getResult();

        // group sessions by day
        $groups = [];
        foreach($sessions as $v) {
            /** @var Visit $v */
            $groups[$v->getCreated()->format('Y-m-d')][$v->getUser()->getId()] = $v;
        }
        if(count($groups))
            $sessions = call_user_func_array('array_merge', $groups);

        $showPartnerIntro = false;
        if(count($users) && empty($user->getProperty('seen_partner_intro'))) {
            $showPartnerIntro = true;
            /** @var $userManager UserManager */
            $userManager = $this->get('fos_user.user_manager');
            $user->setProperty('seen_partner_intro', true);
            $userManager->updateUser($user);
        }

        $uniqueUsers = array_unique(array_map(function (Visit $v) {return $v->getUser();}, $sessions));
        $diffUsers = array_diff($users, $uniqueUsers);
        foreach($diffUsers as $u) {
            /** @var User $u */
            $v = new Visit();
            $v->setUser($u);
            $v->setCreated($u->getCreated());
            $sessions[] = $v;
        }

        return $this->render('AdminBundle:Adviser:userlist.html.php', [
            'sessions' => $sessions,
            'users' => $users,
            'showPartnerIntro' => $showPartnerIntro
        ]);
    }

    /**
     * @param $_user
     * @param $_tab
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function partnerAction(User $_user, $_tab)
    {
        return $this->render('StudySauceBundle:Partner:partner.html.php', [
            'user' => $_user,
            'tab' => $_tab
        ]);
    }

    /**
     * @param $_user
     * @param $_tab
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function adviserAction(User $_user, $_tab)
    {
        $u = $this->getUser();

        if(!$u->hasRole('ROLE_PARTNER') && !$u->hasRole('ROLE_ADVISER') && !$u->hasRole('ROLE_MASTER_ADVISER') &&
            !$u->hasRole('ROLE_ADMIN'))
            throw new AccessDeniedHttpException();
        return $this->render('AdminBundle:Adviser:adviser.html.php', [
            'user' => $_user,
            'tab' => $_tab
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateStatusAction(Request $request)
    {
        /** @var $userManager UserManager */
        $userManager = $this->get('fos_user.user_manager');

        /** @var $user User */
        $user = $userManager->findUserBy(['id' => intval($request->get('userId'))]);

        // TODO: check if partner and user is connected
        $user->setProperty('adviser_status', $request->get('status'));
        $userManager->updateUser($user);
        return new JsonResponse(true);
    }
}
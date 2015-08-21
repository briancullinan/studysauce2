<?php


namespace Admin\Bundle\Controller;


use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\GroupInvite;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ImportController
 * @package Admin\Bundle\Controller
 */
class ImportController extends Controller
{

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        // get the groups this user has control over
        $groups = $user->getGroups()->toArray();
        if($user->hasRole('ROLE_ADMIN')) {
            $groups = $orm->getRepository('StudySauceBundle:Group')->createQueryBuilder('g')
                ->select('g')
                ->getQuery()
                ->getResult();
        }

        return $this->render('AdminBundle:Import:tab.html.php', ['groups' => $groups]);
    }

    /**
     * @param Request $request
     * @param Group $group
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request, Group $group = null)
    {
        /** @var $userManager UserManager */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        /** @var User $user */
        $user = $this->getUser();

        $users = $request->get('users');
        $existing = $user->getGroupInvites()->toArray();
        $emails = new \StudySauce\Bundle\Controller\EmailsController();
        $emails->setContainer($this->container);
        foreach($users as $i => $u)
        {
            if($group == null) {
                if(!empty($u['adviser'])) {
                    /** @var User $adviser */
                    $adviser = $userManager->findUserByEmail($u['adviser']);
                    if(!empty($adviser)) {
                        $group = $adviser->getGroups()->first();
                    }
                    else {
                        $group = $orm->getRepository('StudySauceBundle:Group')->createQueryBuilder('g')
                            ->select('g')
                            ->where('g.name LIKE :search')
                            ->orWhere('g.id=:group')
                            ->setParameter('search', '%' . $u['adviser'] . '%')
                            ->setParameter('group', intval($u['adviser']))
                            ->getQuery()
                            ->getOneOrNullResult();
                    }
                }
                if(empty($group))
                    $group = $user->getGroups()->first();
            }
            unset($invite);
            // check if invite has already been sent
            foreach($existing as $j => $gi)
            {
                /** @var GroupInvite $gi */
                if(strtolower($gi->getEmail()) == $u['email']) {
                    $invite = $gi;
                    break;
                }
            }

            // check if the user already exists
            /** @var User $invitee */
            $invitee = $userManager->findUserByEmail($u['email']);

            // save the invite
            if(!isset($invite) && !empty($group)) {
                $invite = new GroupInvite();
                $invite->setGroup($group);
                $invite->setUser($user);
                $invite->setFirst($u['first']);
                $invite->setLast($u['last']);
                $invite->setEmail($u['email']);
                $invite->setCode(md5(microtime()));
                $user->addGroupInvite($invite);
                $orm->persist($invite);
                $orm->flush();
                // don't send emails to existing users
                if(empty($invitee)) {
                    $emails->groupInviteAction($user, $invite);
                }
            }

            if(!empty($invitee)) {
                $invite->setStudent($invitee);
                $invite->setActivated(true);
                $invitee->addInvitedGroup($invite);
                if(!$invitee->hasGroup($group->getName()))
                    $invitee->addGroup($group);
                $userManager->updateUser($invitee);
                $orm->merge($invite);
                $orm->flush();
            }
        }

        return $this->forward('AdminBundle:Import:index', ['_format' => 'tab']);
    }

}
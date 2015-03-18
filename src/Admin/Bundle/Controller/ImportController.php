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
        return $this->render('StudySauceBundle:Partner:import.html.php');
    }

    /**
     * @param Request $request
     * @param Group $group
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function importAction(Request $request, Group $group = null)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        /** @var User $user */
        $user = $this->getUser();

        if($group == null)
            $group = $user->getGroups()->first();

        $users = $request->get('users');
        $existing = $user->getGroupInvites()->toArray();
        $emails = new EmailsController();
        $emails->setContainer($this->container);
        foreach($users as $i => $u)
        {
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
            /** @var $userManager UserManager */
            $userManager = $this->get('fos_user.user_manager');
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

        return $this->forward('StudySauceBundle:Partner:import', ['_format' => 'tab']);
    }

}
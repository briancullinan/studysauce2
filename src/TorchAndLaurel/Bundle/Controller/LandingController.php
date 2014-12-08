<?php

namespace TorchAndLaurel\Bundle\Controller;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use StudySauce\Bundle\Controller\HomeController;
use StudySauce\Bundle\Entity\GroupInvite;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class LandingController
 * @package StudySauce\Bundle\Controller
 */
class LandingController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        /** @var User $user */
        $user = $this->getUser();
        /** @var $userManager UserManager */
        $userManager = $this->get('fos_user.user_manager');

        // check if we have a user and redirect accordingly.
        list($route, $options) = HomeController::getUserRedirect($user);
        if ($route != '_welcome') {
            return $this->redirect($this->generateUrl($route, $options));
        }

        $request->getSession()->set('organization', 'Torch And Laurel');

        /** @var GroupInvite $group */
        $group = $orm->getRepository('StudySauceBundle:GroupInvite')->findOneBy(['code' => $request->get('_code')]);
        if(empty($group)) {
            return $this->render('TorchAndLaurelBundle:Landing:index.html.php');
        }
        else {
            $group->setActivated(true);
            /** @var User $studentUser */
            $studentUser = $userManager->findUserByEmail($group->getEmail());
            if($studentUser != null)
                $group->setStudent($studentUser);
            $orm->merge($group);
            $orm->flush();
            $session = $request->getSession();
            $session->set('group', $request->get('_code'));
            return $this->render('TorchAndLaurelBundle:Landing:index.html.php');
        }
    }
}


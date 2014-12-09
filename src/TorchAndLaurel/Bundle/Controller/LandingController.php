<?php

namespace TorchAndLaurel\Bundle\Controller;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use StudySauce\Bundle\Controller\HomeController;
use StudySauce\Bundle\Entity\GroupInvite;
use StudySauce\Bundle\Entity\ParentInvite;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

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

    /**
     * @param UserManager $userManager
     * @param string $template
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function logoutUser(UserManager $userManager, $template)
    {
        $loginManager = $this->get('fos_user.security.login_manager');
        $this->get('security.context')->setToken(null);
        $this->get('request')->getSession()->invalidate();
        /** @var EncoderFactory $encoder_service */
        $encoder_service = $this->get('security.encoder_factory');
        /** @var PasswordEncoderInterface $encoder */
        $user = $userManager->findUserByUsername('guest');
        $encoder = $encoder_service->getEncoder($user);
        $password = $encoder->encodePassword('guest', $user->getSalt());
        $this->get('security.context')->setToken(new UsernamePasswordToken($user, $password, 'main', $user->getRoles()));
        $response = $this->render($template);
        $loginManager->loginUser('main', $user, $response);
        return $response;
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function parentsAction(Request $request)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        /** @var $userManager UserManager */
        $userManager = $this->get('fos_user.user_manager');

        /** @var ParentInvite $parent */
        $parent = $orm->getRepository('StudySauceBundle:ParentInvite')->findOneBy(['code' => $request->get('_code')]);
        if(empty($parent)) {
            return $this->render('TorchAndLaurelBundle:Landing:parents.html.php');
        }
        else {
            $parent->setActivated(true);
            /** @var User $parentUser */
            $parentUser = $userManager->findUserByEmail($parent->getEmail());
            if($parentUser != null)
                $parent->setParent($parentUser);
            $orm->merge($parent);
            $orm->flush();
            $response = $this->logoutUser($userManager, 'TorchAndLaurelBundle:Landing:parents.html.php');
            $session = $request->getSession();
            $session->set('parent', $request->get('_code'));
            return $response;
        }
    }
}


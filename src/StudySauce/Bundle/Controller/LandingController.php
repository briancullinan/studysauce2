<?php

namespace StudySauce\Bundle\Controller;

use Course1\Bundle\Entity\Course1;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use StudySauce\Bundle\Entity\ParentInvite;
use StudySauce\Bundle\Entity\PartnerInvite;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        /** @var User $user */
        $user = $this->getUser();

        // check if we have a user and redirect accordingly.
        list($route, $options) = HomeController::getUserRedirect($user);
        if($route != '_welcome')
            return $this->redirect($this->generateUrl($route, $options));

        $session = $request->getSession();

        if($user->hasGroup('Torch And Laurel') || ($session->has('organization') && $session->get('organization') == 'Torch And Laurel'))
        {
            return $this->forward('TorchAndLaurelBundle:Landing:index');
        }

        return $this->render('StudySauceBundle:Landing:index.html.php');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function termsAction()
    {
        return $this->render('StudySauceBundle:Landing:terms.html.php');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function privacyAction()
    {
        return $this->render('StudySauceBundle:Landing:privacy.html.php');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function aboutAction()
    {
        return $this->render('StudySauceBundle:Landing:about.html.php');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function refundAction()
    {
        return $this->render('StudySauceBundle:Landing:refund.html.php');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function contactAction()
    {
        return $this->render('StudySauceBundle:Landing:contact.html.php');
    }

    /**
     * Do nothing
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function visitAction(Request $request)
    {
        // call visits for other bundles
        //$course = new

        // TODO: recording logic
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        $visits = $request->get('__visits');
        $visits[]['path'] = $request->getPathInfo();
        foreach ($visits as $i => $v) {
            if(substr(str_replace($request->getBaseUrl(), '', $v['path']), 0, 10) == '/course/1/')
                // TODO: check for quiz completeness
                if(preg_match('/lesson\/([0-9]+)\/step\/?([0-9]+)?/', $v['path'], $matches))
                {
                    // compare course progress

                    /** @var $user User */
                    $user = $this->getUser();

                    /** @var Course1 $course */
                    $course = $user->getCourse1s()->first();

                    if(!empty($course))
                    {
                        if($course->getLevel() * 10 + $course->getStep() < intval($matches[1]) * 10 + intval(isset($matches[2]) ? $matches[2] : 0)) {
                            $course->setLevel(intval($matches[1]));
                            $course->setStep(intval(isset($matches[2]) ? $matches[2] : 0));
                            $orm->merge($course);
                            $orm->flush();
                        }
                    }
                }
        }

        return new JsonResponse(true);
    }

    /**
     * @param Request $request
     * @param $_code
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function partnersAction(Request $request, $_code)
    {
        /** @var $userManager UserManager */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var PartnerInvite $partner */
        $partner = $orm->getRepository('StudySauceBundle:PartnerInvite')->findOneBy(['code' => $_code]);
        if(empty($partner)) {
            return $this->render('StudySauceBundle:Landing:partners.html.php');
        }
        else {
            $partner->setActivated(true);
            /** @var User $partnerUser */
            $partnerUser = $userManager->findUserByEmail($partner->getEmail());
            if($partnerUser != null)
                $partner->setPartner($partnerUser);
            $orm->merge($partner);
            $orm->flush();
            $response = $this->logoutUser($userManager, 'StudySauceBundle:Landing:partners.html.php');
            $session = $request->getSession();
            $session->set('partner', $_code);
            return $response;
        }
    }

    /**
     * @param Request $request
     * @param $_code
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function parentsAction(Request $request, $_code)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        /** @var $userManager UserManager */
        $userManager = $this->get('fos_user.user_manager');

        /** @var ParentInvite $partner */
        $parent = $orm->getRepository('StudySauceBundle:ParentInvite')->findOneBy(['code' => $request->get('_code')]);
        if(empty($parent)) {
            return $this->render('StudySauceBundle:Landing:parents.html.php');
        }
        else {
            $parent->setActivated(true);
            $parentUser = $userManager->findUserByEmail($parent->getEmail());
            if($parentUser != null)
                $parent->setParent($parentUser);
            $orm->merge($parent);
            $orm->flush();
            $response = $this->logoutUser($userManager, 'StudySauceBundle:Landing:parents.html.php');
            $session = $request->getSession();
            $session->set('parent', $_code);
            return $response;
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
    public function studentsAction(Request $request)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var $user User */
        $user = $this->getUser();

        /** @var PartnerInvite $partner */
        $parent = $orm->getRepository('StudySauceBundle:StudentInvite')->findOneBy(['code' => $request->get('_code')]);
        if(!empty($parent)) {
            $parent->setActivated(true);
            $orm->merge($parent);
            $orm->flush();

            if(empty($parent->getParent()) || $parent->getParent()->getId() !=  $user->getId())
            {
                $this->get('security.context')->setToken(null);
                $this->get('request')->getSession()->invalidate();
            }
        }

        return $this->render('StudySauceBundle:Landing:students.html.php');
    }

}

<?php

namespace StudySauce\Bundle\Controller;

use Course1\Bundle\Entity\Course1;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use StudySauce\Bundle\Command\CronSauceCommand;
use StudySauce\Bundle\Entity\GroupInvite;
use StudySauce\Bundle\Entity\ParentInvite;
use StudySauce\Bundle\Entity\PartnerInvite;
use StudySauce\Bundle\Entity\StudentInvite;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
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
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        /** @var User $user */
        $user = $this->getUser();
        $session = $request->getSession();

        // check if we have a user and redirect accordingly.
        list($route, $options) = HomeController::getUserRedirect($user);
        if($route != '_welcome')
            return $this->redirect($this->generateUrl($route, $options));

        /** @var GroupInvite $group */
        // TODO: generalize this for other groups
        $group = $orm->getRepository('StudySauceBundle:GroupInvite')->findOneBy(['code' => $request->get('_code')]);
        if(!empty($group) && $group->getGroup()->getName() == 'Torch And Laurel' ||
            ($session->has('organization') && $session->get('organization') == 'Torch And Laurel'))
        {
            return $this->forward('TorchAndLaurelBundle:Landing:index');
        }

        return $this->render('StudySauceBundle:Landing:index.html.php');
    }

    /**
     * @return JsonResponse
     * @throws \Exception
     */
    public function cronAction()
    {
        $command = new CronSauceCommand();
        $command->setContainer($this->container);
        $input = new ArrayInput([] /* array('some-param' => 10, '--some-option' => true)*/);
        $output = new NullOutput();
        $command->run($input, $output);
        return new JsonResponse(true);
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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function partnersAction(Request $request)
    {
        /** @var $userManager UserManager */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var PartnerInvite $partner */
        $partner = $orm->getRepository('StudySauceBundle:PartnerInvite')->findOneBy(['code' => $request->get('_code')]);
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
            $session->set('partner', $request->get('_code'));
            return $response;
        }
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
        $session = $request->getSession();

        /** @var GroupInvite $group */
        // TODO: generalize this for other groups
        $group = $orm->getRepository('StudySauceBundle:GroupInvite')->findOneBy(['code' => $request->get('_code')]);
        if(!empty($group) && $group->getGroup()->getName() == 'Torch And Laurel' ||
            ($session->has('organization') && $session->get('organization') == 'Torch And Laurel'))
        {
            return $this->forward('TorchAndLaurelBundle:Landing:parents');
        }

        /** @var ParentInvite $parent */
        $parent = $orm->getRepository('StudySauceBundle:ParentInvite')->findOneBy(['code' => $request->get('_code')]);
        if(empty($parent)) {
            return $this->render('StudySauceBundle:Landing:parents.html.php');
        }
        else {
            $parent->setActivated(true);
            /** @var User $parentUser */
            $parentUser = $userManager->findUserByEmail($parent->getEmail());
            if($parentUser != null)
                $parent->setParent($parentUser);
            $orm->merge($parent);
            $orm->flush();
            $response = $this->logoutUser($userManager, 'StudySauceBundle:Landing:parents.html.php');
            $session = $request->getSession();
            $session->set('parent', $request->get('_code'));
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
        /** @var $userManager UserManager */
        $userManager = $this->get('fos_user.user_manager');
        $session = $request->getSession();

        /** @var GroupInvite $group */
        // TODO: generalize this for other groups
        $group = $orm->getRepository('StudySauceBundle:GroupInvite')->findOneBy(['code' => $request->get('_code')]);
        if(!empty($group) && $group->getGroup()->getName() == 'Torch And Laurel' ||
            ($session->has('organization') && $session->get('organization') == 'Torch And Laurel'))
        {
            return $this->forward('TorchAndLaurelBundle:Landing:index');
        }

        /** @var StudentInvite $student */
        $student = $orm->getRepository('StudySauceBundle:StudentInvite')->findOneBy(['code' => $request->get('_code')]);
        if(empty($student)) {
            return $this->render('StudySauceBundle:Landing:students.html.php');
        }
        else {
            $student->setActivated(true);
            /** @var User $studentUser */
            $studentUser = $userManager->findUserByEmail($student->getEmail());
            if($studentUser != null)
                $student->setStudent($studentUser);
            $orm->merge($student);
            $orm->flush();
            $response = $this->logoutUser($userManager, 'StudySauceBundle:Landing:students.html.php');
            $session = $request->getSession();
            $session->set('student', $request->get('_code'));
            return $response;
        }
    }

}

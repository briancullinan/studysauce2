<?php

namespace StudySauce\Bundle\Controller;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use FOS\UserBundle\Security\LoginManager;
use HWI\Bundle\OAuthBundle\Templating\Helper\OAuthHelper;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\GroupInvite;
use StudySauce\Bundle\Entity\Invite;
use StudySauce\Bundle\Entity\ParentInvite;
use StudySauce\Bundle\Entity\PartnerInvite;
use StudySauce\Bundle\Entity\StudentInvite;
use StudySauce\Bundle\Entity\User;
use StudySauce\Bundle\EventListener\InviteListener;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

/**
 * Class AccountController
 * @package StudySauce\Bundle\Controller
 */
class AccountController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $user = $this->getUser();
        $csrfToken = $this->has('form.csrf_provider')
            ? $this->get('form.csrf_provider')->generateCsrfToken('account_update')
            : null;

        return $this->render('StudySauceBundle:Account:tab.html.php', [
                'user' => $user,
                'csrf_token' => $csrfToken
            ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateAction(Request $request)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var $user User */
        $user = $this->getUser();
        $user->setFirst($request->get('first'));
        $user->setLast($request->get('last'));
        if(!empty($request->get('email')))
        {
            // check password
            /** @var EncoderFactory $encoder_service */
            $encoder_service = $this->get('security.encoder_factory');
            /** @var MessageDigestPasswordEncoder $encoder */
            $encoder = $encoder_service->getEncoder($user);
            $encoded_pass = $encoder->encodePassword($request->get('pass'), $user->getSalt());
            if($user->getPassword() == $encoded_pass)
            {
                if(!empty($request->get('newPass')))
                {
                    $password = $encoder->encodePassword($request->get('newPass'), $user->getSalt());
                    $user->setPassword($password);
                }
                $user->setEmail($request->get('email'));
                $orm->merge($user);
                $orm->flush();
            }
            else
                $error = 'Incorrect password';
        }

        $csrfToken = $this->has('form.csrf_provider')
            ? $this->get('form.csrf_provider')->generateCsrfToken('account_update')
            : null;
        $response = ['csrf_token' => $csrfToken];
        if(isset($error))
            $response['error'] = $error;
        return new JsonResponse($response);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deniedAction()
    {
        return $this->render('StudySauceBundle:Exception:error403.html.php');
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction(Request $request)
    {
        // list oauth services
        $services = [];
        /** @var OAuthHelper $oauth */
        $oauth = $this->get('hwi_oauth.templating.helper.oauth');
        foreach($oauth->getResourceOwners() as $o) {
            $services[$o] = $oauth->getLoginUrl($o);
        }

        $csrfToken = $this->has('form.csrf_provider')
            ? $this->get('form.csrf_provider')->generateCsrfToken('account_login')
            : null;
        return $this->render('StudySauceBundle:Account:login.html.php', [
                'email' => $request->get('email'),
                'csrf_token' => $csrfToken,
                'services' => $services
            ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(Request $request)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        // list oauth services
        $services = [];
        /** @var OAuthHelper $oauth */
        $oauth = $this->get('hwi_oauth.templating.helper.oauth');
        foreach($oauth->getResourceOwners() as $o) {
            $services[$o] = $oauth->getLoginUrl($o);
        }

        $csrfToken = $this->has('form.csrf_provider')
            ? $this->get('form.csrf_provider')->generateCsrfToken('account_register')
            : null;

        /** @var Invite $invite */
        if(!empty($request->getSession()->get('partner'))) {
            $invite = $orm->getRepository('StudySauceBundle:PartnerInvite')->findOneBy(['code' => $request->getSession()->get('partner')]);
        }
        if(!empty($request->getSession()->get('student'))) {
            $invite = $orm->getRepository('StudySauceBundle:StudentInvite')->findOneBy(['code' => $request->getSession()->get('student')]);
        }
        if(!empty($request->getSession()->get('parent'))) {
            $invite = $orm->getRepository('StudySauceBundle:ParentInvite')->findOneBy(['code' => $request->getSession()->get('parent')]);
        }
        if(!empty($request->getSession()->get('group'))) {
            $invite = $orm->getRepository('StudySauceBundle:GroupInvite')->findOneBy(['code' => $request->getSession()->get('group')]);
        }

        if(!empty($invite)) {
            return $this->render('StudySauceBundle:Account:register.html.php', [
                    'email' => $invite->getEmail(),
                    'first' => $invite->getFirst(),
                    'last' => $invite->getLast(),
                    'csrf_token' => $csrfToken,
                    'services' => $services
                ]);
        }

        return $this->render('StudySauceBundle:Account:register.html.php', [
                'email' => $request->get('email'),
                'first' => $request->get('first'),
                'last' => $request->get('last'),
                'csrf_token' => $csrfToken,
                'services' => $services
            ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function createAction(Request $request)
    {
        /** @var $userManager UserManager */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        $user = $userManager->findUserByEmail($request->get('email'));
        $csrfToken = $this->has('form.csrf_provider')
            ? $this->get('form.csrf_provider')->generateCsrfToken('account_register')
            : null;
        if($user == null)
        {
            // generate a new guest user in the database
            /** @var $user User */
            $user = $userManager->createUser();
            $user->setUsername($request->get('email'));
            $user->setUsernameCanonical($request->get('email'));
            $encoder_service = $this->get('security.encoder_factory');
            /** @var $encoder PasswordEncoderInterface */
            $encoder = $encoder_service->getEncoder($user);
            $password = $encoder->encodePassword($request->get('pass'), $user->getSalt());
            $user->setPassword($password);
            $user->setEmail($request->get('email'));
            $user->setEmailCanonical($request->get('email'));
            $user->addRole('ROLE_USER');
            // assign user to partner
            InviteListener::setInviteRelationship($orm, $request, $user);
            $user->setEnabled(true);
            $user->setFirst($request->get('first'));
            $user->setLast($request->get('last'));
            $orm->persist($user);
            $orm->flush();

            $context = $this->get('security.context');
            $token = new UsernamePasswordToken($user, $password, 'main', $user->getRoles());
            $context->setToken($token);
            $session = $request->getSession();
            $session->set('_security_main', serialize($token));
            if($user->hasRole('ROLE_PARTNER') || $user->hasRole('ROLE_ADVISER'))
                $response = $this->redirect($this->generateUrl('userlist'));
            else
                $response = $this->redirect($this->generateUrl('home'));

            /** @var LoginManager $loginManager */
            $loginManager = $this->get('fos_user.security.login_manager');
            $loginManager->loginUser('main', $user, $response);

            // send welcome email
            $email = new EmailsController();
            $email->setContainer($this->container);
            if($user->hasRole('ROLE_PARENT')) {

            }
            else if($user->hasRole('ROLE_PARTNER'))
                $email->welcomePartnerAction($user);
            else
                $email->welcomeStudentAction($user);

            return $response;
        }
        else
        {
            return new JsonResponse(['error' => true, 'csrf_token' => $csrfToken]);
        }
    }
}


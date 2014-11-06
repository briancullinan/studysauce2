<?php

namespace StudySauce\Bundle\Controller;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use HWI\Bundle\OAuthBundle\Templating\Helper\OAuthHelper;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

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
            $encoder_service = $this->get('security.encoder_factory');
            $encoder = $encoder_service->getEncoder($user);
            /** @var $encoder PasswordEncoderInterface */
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
        }

        $csrfToken = $this->has('form.csrf_provider')
            ? $this->get('form.csrf_provider')->generateCsrfToken('account_update')
            : null;
        return new JsonResponse(['csrf_token' => $csrfToken]);
    }

    /**
     * @param Request $request
     */
    public function removeAction(Request $request)
    {

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

        $user = $userManager->findUserByUsername($request->get('email'));
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
            $user->addRole('ROLE_USER');
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
            $response = new JsonResponse(['csrf_token' => $csrfToken]);

            $loginManager = $this->get('fos_user.security.login_manager');
            $loginManager->loginUser('main', $user, $response);

            // send welcome email
            $email = new EmailsController();
            $email->setContainer($this->container);
            $email->welcomeStudentAction($user);

            return $response;
        }
        else
        {
            return new JsonResponse(['error' => true, 'csrf_token' => $csrfToken]);
        }
    }
}


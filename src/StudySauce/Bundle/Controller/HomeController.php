<?php

namespace StudySauce\Bundle\Controller;

use FOS\UserBundle\Doctrine\UserManager;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class HomeController
 * @package StudySauce\Bundle\Controller
 */
class HomeController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        /** @var User $user */
        $user = $this->getUser();
        list($route, $options) = self::getUserRedirect($user);
        if($route != 'home' && $route != '_welcome')
            return $this->redirect($this->generateUrl($route, $options));

        $csrfToken = $this->has('form.csrf_provider')
            ? $this->get('form.csrf_provider')->generateCsrfToken('account_update')
            : null;

        $showBookmark = false; // TODO: false in production
        if(empty($user->getProperty('seen_bookmark'))) {
            $showBookmark = true;
            /** @var $userManager UserManager */
            $userManager = $this->get('fos_user.user_manager');
            $user->setProperty('seen_bookmark', true);
            $userManager->updateUser($user);
        }

        return $this->render(
            'StudySauceBundle:Home:tab.html.php',
            [
                'showBookmark' => $showBookmark,
                'user' => $user,
                'csrf_token' => $csrfToken
            ]
        );
    }

    /**
     * @param User $user
     * @return array|string
     */
    public static function getUserRedirect(User $user)
    {
        if($user == 'anon.' || !is_object($user) || $user->hasRole('ROLE_GUEST'))
            return ['_welcome', []];
            // TODO: split this in to separate pages
        elseif($user->hasRole('ROLE_PARTNER') && $user->getInvitedPartners()->count() > 1)
            return ['adviser', ['_user' => $user->getInvitedPartners()->first()->getUser()->getId(), '_tab' => 'metrics']];
        elseif($user->hasRole('ROLE_PARTNER') || $user->hasRole('ROLE_ADVISER') || $user->hasRole('ROLE_MASTER_ADVISER'))
            return ['userlist', []];
        elseif($user->hasRole('ROLE_PARENT'))
            return ['thanks', []];
        elseif($user->hasRole('ROLE_PAID') && ($step = ProfileController::getFunnelState($user)))
            return [$step, ['_format' => 'funnel']];
        elseif(empty($user->getProperty('first_time')))
            return ['course1_introduction', []];
        return ['home', []];
    }
}
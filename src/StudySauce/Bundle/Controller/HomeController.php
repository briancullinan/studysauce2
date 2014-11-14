<?php

namespace StudySauce\Bundle\Controller;

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
        if($user->hasRole('ROLE_PARTNER') || $user->hasRole('ROLE_ADVISER'))
            return $this->redirect($this->generateUrl('userlist'));
        elseif($user->hasRole('ROLE_PAID') && ($step = ProfileController::getFunnelState($user)))
            return $this->redirect($this->generateUrl($step, ['_format' => 'funnel']));

        $csrfToken = $this->has('form.csrf_provider')
            ? $this->get('form.csrf_provider')->generateCsrfToken('account_update')
            : null;

        return $this->render(
            'StudySauceBundle:Home:tab.html.php',
            [
                'user' => $user,
                'csrf_token' => $csrfToken
            ]
        );
    }
}
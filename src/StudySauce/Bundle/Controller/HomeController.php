<?php

namespace StudySauce\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
        $user = $this->getUser();
        if($user->hasRole('ROLE_PARTNER') || $user->hasRole('ROLE_ADVISER'))
            return $this->redirect($this->generateUrl('userlist'));

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
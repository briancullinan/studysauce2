<?php

namespace StudySauce\Bundle\Controller;

use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BusinessController
 * @package StudySauce\Bundle\Controller
 */
class BusinessController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function signupAction()
    {
        $csrfToken = $this->has('form.csrf_provider')
            ? $this->get('form.csrf_provider')->generateCsrfToken('business')
            : null;

        /** @var User $user */
        $user = $this->getUser();
        $first = $user->getFirst();
        $last = $user->getLast();
        $email = $user->getEmail();

        return $this->render('StudySauceBundle:Business:signup.html.php', [
            'students' => 0,
            'email' => $email,
            'first' => $first,
            'last' => $last,
            'organization' => '',
            'title' => '',
            'phone' => '',
            'csrf_token' => $csrfToken
        ]);
    }

    /**
     * @param Request $request
     */
    public function signupSaveAction(Request $request)
    {

    }
}
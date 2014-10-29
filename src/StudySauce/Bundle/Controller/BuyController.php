<?php

namespace StudySauce\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class BuyController
 * @package StudySauce\Bundle\Controller
 */
class BuyController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function checkoutAction()
    {
        $user = $this->getUser();
        $csrfToken = $this->has('form.csrf_provider')
            ? $this->get('form.csrf_provider')->generateCsrfToken('cart_checkout')
            : null;

        return $this->render('StudySauceBundle:Buy:checkout.html.php', [
                'user' => $user,
                'csrf_token' => $csrfToken
            ]);
    }
}
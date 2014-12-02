<?php

namespace TorchAndLaurel\Bundle\Controller;

use StudySauce\Bundle\Controller\HomeController;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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
        $request->getSession()->set('organization', 'Torch And Laurel');
        // check if we have a user and redirect accordingly.
        list($route, $options) = HomeController::getUserRedirect($user);
        if ($route != '_welcome') {
            return $this->redirect($this->generateUrl($route, $options));
        }

        return $this->render('StudySauceBundle:Landing:index.html.php');
    }
}


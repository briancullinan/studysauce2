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
        $request->getSession()->set('organization', 'Torch And Laurel');
        return $this->render('TorchAndLaurelBundle:Landing:index.html.php');
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function parentsAction(Request $request)
    {
        $session = $request->getSession();
        if(empty($session->get('parent')))
            $session->set('parent', true);

        $request->getSession()->set('organization', 'Torch And Laurel');
        return $this->render('TorchAndLaurelBundle:Landing:parents.html.php');
    }
}


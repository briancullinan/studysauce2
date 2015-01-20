<?php


namespace TorchAndLaurel\Bundle\Controller;

use StudySauce\Bundle\Entity\ParentInvite;
use StudySauce\Bundle\Entity\StudentInvite;
use StudySauce\Bundle\Entity\User;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class EmailsController
 * @package StudySauce\Bundle\Controller
 */
class AccountController extends Controller
{

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(Request $request)
    {
        $session = $request->getSession();
        if(empty($session->get('parent')))
            $session->set('parent', true);

        $account = new \StudySauce\Bundle\Controller\AccountController();
        $account->setContainer($this->container);
        $account->registerAction($request);
    }

}
<?php

namespace TorchAndLaurel\Bundle\Controller;

use Doctrine\ORM\EntityManager;
use StudySauce\Bundle\Controller\HomeController;
use StudySauce\Bundle\Entity\Group;
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
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        /** @var User $user */
        $user = $this->getUser();
        $request->getSession()->set('organization', 'Torch And Laurel');
        // check if we have a user and redirect accordingly.
        list($route, $options) = HomeController::getUserRedirect($user);
        if ($route != '_welcome') {
            return $this->redirect($this->generateUrl($route, $options));
        }

        $group = $orm->getRepository('StudySauceBundle:Group')->findOneBy(['name' => 'Torch And Laurel']);
        if($group == null) {
            $group = new Group();
            $group->setName('Torch And Laurel');
            $group->setDescription('');
            $orm->persist($group);
            $orm->flush();
        }

        return $this->render('TorchAndLaurelBundle:Landing:index.html.php');
    }
}


<?php

namespace StudySauce\Bundle\Controller;

use Course1\Bundle\Entity\Course1;
use Doctrine\ORM\EntityManager;
use StudySauce\Bundle\Command\CronSauceCommand;
use StudySauce\Bundle\Entity\GroupInvite;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        $session = $request->getSession();

        // check if we have a user and redirect accordingly.
        list($route, $options) = HomeController::getUserRedirect($user);
        if($route != '_welcome')
            return $this->redirect($this->generateUrl($route, $options));

        /** @var GroupInvite $group */
        // TODO: generalize this for other groups
        $group = $orm->getRepository('StudySauceBundle:GroupInvite')->findOneBy(['code' => $request->get('_code')]);
        if(!empty($group) && $group->getGroup()->getName() == 'Torch And Laurel' ||
            ($session->has('organization') && $session->get('organization') == 'Torch And Laurel'))
        {
            return $this->forward('TorchAndLaurelBundle:Landing:index');
        }

        return $this->render('StudySauceBundle:Landing:index.html.php');
    }

    /**
     * @return JsonResponse
     * @throws \Exception
     */
    public function cronAction()
    {
        $command = new CronSauceCommand();
        $command->setContainer($this->container);
        $input = new ArrayInput([] /* array('some-param' => 10, '--some-option' => true)*/);
        $output = new NullOutput();
        $command->run($input, $output);
        return new JsonResponse(true);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function termsAction()
    {
        return $this->render('StudySauceBundle:Landing:terms.html.php');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function privacyAction()
    {
        return $this->render('StudySauceBundle:Landing:privacy.html.php');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function aboutAction()
    {
        return $this->render('StudySauceBundle:Landing:about.html.php');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function refundAction()
    {
        return $this->render('StudySauceBundle:Landing:refund.html.php');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function contactAction()
    {
        return $this->render('StudySauceBundle:Landing:contact.html.php');
    }

    /**
     * Do nothing
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function visitAction(Request $request)
    {
        // call visits for other bundles
        //$course = new

        // TODO: recording logic
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        $visits = $request->get('__visits');
        $visits[]['path'] = $request->getPathInfo();
        foreach ($visits as $i => $v) {
            if(substr(str_replace($request->getBaseUrl(), '', $v['path']), 0, 10) == '/course/1/')
                // TODO: check for quiz completeness
                if(preg_match('/lesson\/([0-9]+)\/step\/?([0-9]+)?/', $v['path'], $matches))
                {
                    // compare course progress

                    /** @var $user User */
                    $user = $this->getUser();

                    /** @var Course1 $course */
                    $course = $user->getCourse1s()->first();

                    if(!empty($course))
                    {
                        if($course->getLevel() * 10 + $course->getStep() < intval($matches[1]) * 10 + intval(isset($matches[2]) ? $matches[2] : 0)) {
                            $course->setLevel(intval($matches[1]));
                            $course->setStep(intval(isset($matches[2]) ? $matches[2] : 0));
                            $orm->merge($course);
                            $orm->flush();
                        }
                    }
                }
        }

        return new JsonResponse(true);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function partnersAction(Request $request)
    {
        $session = $request->getSession();

        if(empty($session->get('partner')))
            $session->set('partner', true);

        return $this->render('StudySauceBundle:Landing:partners.html.php');
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function parentsAction(Request $request)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        $session = $request->getSession();

        /** @var GroupInvite $group */
        // TODO: generalize this for other groups
        $group = $orm->getRepository('StudySauceBundle:GroupInvite')->findOneBy(['code' => $request->get('_code')]);
        if(!empty($group) && $group->getGroup()->getName() == 'Torch And Laurel' ||
            ($session->has('organization') && $session->get('organization') == 'Torch And Laurel'))
        {
            return $this->forward('TorchAndLaurelBundle:Landing:parents');
        }

        if(empty($session->get('parent')))
            $session->set('parent', true);
        return $this->render('StudySauceBundle:Landing:parents.html.php');
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function studentsAction(Request $request)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        $session = $request->getSession();

        /** @var GroupInvite $group */
        // TODO: generalize this for other groups
        $group = $orm->getRepository('StudySauceBundle:GroupInvite')->findOneBy(['code' => $request->get('_code')]);
        if(!empty($group) && $group->getGroup()->getName() == 'Torch And Laurel' ||
            ($session->has('organization') && $session->get('organization') == 'Torch And Laurel'))
        {
            return $this->forward('TorchAndLaurelBundle:Landing:index');
        }

        return $this->render('StudySauceBundle:Landing:students.html.php');
    }

}

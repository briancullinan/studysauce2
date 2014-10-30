<?php

namespace StudySauce\Bundle\Controller;

use Course1\Bundle\Entity\Course1;
use Doctrine\ORM\EntityManager;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class LandingController
 * @package StudySauce\Bundle\Controller
 */
class LandingController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        /*
         * The action's view can be rendered using render() method
         * or @Template annotation as demonstrated in DemoController.
         *
         */

        return $this->render('StudySauceBundle:Landing:index.html.php');
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
        /*
         * The action's view can be rendered using render() method
         * or @Template annotation as demonstrated in DemoController.
         *
         */
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        $partner = $orm->getRepository('StudySauceBundle:Partner')->findOneBy(['code' => $request->get('_code')]);
        $partner->setActivated(true);
        $orm->merge($partner);
        $orm->flush();

        return $this->render('StudySauceBundle:Landing:index.html.php');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function scrAction()
    {
        return $this->render('StudySauceBundle:Landing:scr.html.php');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function bannerAction()
    {
        return $this->render('StudySauceBundle:Landing:banner.html.php');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function featuresAction()
    {
        return $this->render('StudySauceBundle:Landing:features.html.php');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function videoAction()
    {
        return $this->render('StudySauceBundle:Landing:video.html.php');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function testimonyAction()
    {
        return $this->render('StudySauceBundle:Landing:testimony.html.php');
    }
}

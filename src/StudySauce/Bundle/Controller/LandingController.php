<?php

namespace StudySauce\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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

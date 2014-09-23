<?php

namespace StudySauce\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LandingController extends Controller
{
    public function indexAction()
    {
        /*
         * The action's view can be rendered using render() method
         * or @Template annotation as demonstrated in DemoController.
         *
         */

        return $this->render('StudySauceBundle:Landing:index.html.php');
    }

    public function scrAction()
    {
        return $this->render('StudySauceBundle:Landing:scr.html.php');
    }

    public function bannerAction()
    {
        return $this->render('StudySauceBundle:Landing:banner.html.php');
    }

    public function featuresAction()
    {
        return $this->render('StudySauceBundle:Landing:features.html.php');
    }

    public function videoAction()
    {
        return $this->render('StudySauceBundle:Landing:video.html.php');
    }

    public function testimonyAction()
    {
        return $this->render('StudySauceBundle:Landing:testimony.html.php');
    }
}

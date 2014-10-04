<?php

namespace StudySauce\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class CheckinController
 * @package StudySauce\Bundle\Controller
 */
class CheckinController extends Controller
{
    /**
     * @param string $_format
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($_format = 'index')
    {
        //$request = $this->container->get('request');
        //$routeName = $request->get('_format');

        return $this->render('StudySauceBundle:Checkin:tab.html.php');
    }
}


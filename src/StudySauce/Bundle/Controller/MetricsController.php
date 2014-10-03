<?php

namespace StudySauce\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class MetricsController
 * @package StudySauce\Bundle\Controller
 */
class MetricsController extends Controller
{
    /**
     * @param string $_format
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($_format = 'index')
    {
        return $this->render('StudySauceBundle:Metrics:tab.html.php');
    }
}


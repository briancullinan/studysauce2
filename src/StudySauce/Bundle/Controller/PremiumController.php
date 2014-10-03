<?php

namespace StudySauce\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class PremiumController
 * @package StudySauce\Bundle\Controller
 */
class PremiumController extends Controller
{
    /**
     * @param string $_format
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($_format = 'index')
    {
        return $this->render('StudySauceBundle:Premium:tab.html.php');
    }
}


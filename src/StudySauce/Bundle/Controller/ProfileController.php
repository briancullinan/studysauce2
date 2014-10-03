<?php

namespace StudySauce\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class ProfileController
 * @package StudySauce\Bundle\Controller
 */
class ProfileController extends Controller
{
    /**
     * @param string $_format
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($_format = 'index')
    {
        return $this->render('StudySauceBundle:Profile:tab.html.php');
    }
}


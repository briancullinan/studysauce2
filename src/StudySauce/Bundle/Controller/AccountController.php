<?php

namespace StudySauce\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class AccountController
 * @package StudySauce\Bundle\Controller
 */
class AccountController extends Controller
{
    /**
     * @param string $_format
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($_format = 'index')
    {
        return $this->render('StudySauceBundle:Account:tab.html.php');
    }
}


<?php

namespace Course1\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class Course3Controller
 * @package Course1\Bundle\Controller
 */
class Lesson3Controller extends Controller
{
    /**
     * @param $_format
     * @param $_step
     */
    public function wizardAction($_format = 'index', $_step = 0)
    {
        switch($_step)
        {
            case 0:
                return $this->render('Course1Bundle:Lesson3:tab.html.php');
                break;
            case 1:
                return $this->render('Course1Bundle:Lesson3:video.html.php');
                break;
            case 2:
                return $this->render('Course1Bundle:Lesson3:quiz.html.php');
                break;
            case 3:
                return $this->render('Course1Bundle:Lesson3:reward.html.php');
                break;
            case 4:
                return $this->render('Course1Bundle:Lesson3:investment.html.php');
                break;
            default:
                throw new NotFoundHttpException();
        }

    }
}


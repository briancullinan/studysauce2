<?php

namespace StudySauce\Bundle\Controller;

use Course1\Bundle\Entity\Course1;
use Doctrine\ORM\EntityManager;
use StudySauce\Bundle\Entity\PartnerInvite;
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
        /** @var User $user */
        $user = $this->getUser();
        if($user != 'anon.' && !$user->hasRole('ROLE_GUEST'))
            return $this->redirect($this->generateUrl('home'));

        return $this->render('StudySauceBundle:Landing:index.html.php');
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
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var $user User */
        $user = $this->getUser();

        /** @var PartnerInvite $partner */
        $partner = $orm->getRepository('StudySauceBundle:PartnerInvite')->findOneBy(['code' => $request->get('_code')]);
        if(!empty($partner)) {
            $partner->setActivated(true);
            $orm->merge($partner);
            $orm->flush();

            if (empty($partner->getPartner()) || $partner->getPartner()->getId() != $user->getId()) {
                $this->get('security.context')->setToken(null);
                $this->get('request')->getSession()->invalidate();
            }
        }

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

        /** @var $user User */
        $user = $this->getUser();

        /** @var Partner $partner */
        $parent = $orm->getRepository('StudySauceBundle:ParentInvite')->findOneBy(['code' => $request->get('_code')]);
        if(!empty($parent)) {
            $parent->setActivated(true);
            $orm->merge($parent);
            $orm->flush();

            if(empty($parent->getParent()) || $parent->getParent()->getId() !=  $user->getId())
            {
                $this->get('security.context')->setToken(null);
                $this->get('request')->getSession()->invalidate();
            }
        }

        return $this->render('StudySauceBundle:Landing:parents.html.php');
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

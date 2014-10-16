<?php

namespace StudySauce\Bundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use StudySauce\Bundle\Entity\Goal;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class GoalsController
 * @package StudySauce\Bundle\Controller
 */
class GoalsController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $user = $this->getUser();

        $csrfToken = $this->has('form.csrf_provider')
            ? $this->get('form.csrf_provider')->generateCsrfToken('update_schedule')
            : null;

        /** @var  $goals ArrayCollection */
        $goals = $user->getGoals();

        return $this->render('StudySauceBundle:Goals:tab.html.php', [
                'behavior' => $goals->filter(function (Goal $x) {return $x->getType() == 'behavior';})->first(),
                'outcome' => $goals->filter(function (Goal $x) {return $x->getType() == 'outcome';})->first(),
                'milestone' => $goals->filter(function (Goal $x) {return $x->getType() == 'milestone';})->first(),
                'csrf_token' => $csrfToken
            ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function widgetAction()
    {
        $user = $this->getUser();

        /** @var  $goals ArrayCollection */
        $goals = $user->getGoals();

        return $this->render('StudySauceBundle:Goals:widget.html.php', [
                'behavior' => $goals->filter(function (Goal $x) {return $x->getType() == 'behavior';})->first(),
                'outcome' => $goals->filter(function (Goal $x) {return $x->getType() == 'outcome';})->first(),
                'milestone' => $goals->filter(function (Goal $x) {return $x->getType() == 'milestone';})->first(),
            ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateAction(Request $request)
    {
        if (false === $this->get('form.csrf_provider')->isCsrfTokenValid('update_schedule',$request->get('csrf_token'))) {
            throw new AccessDeniedHttpException('Invalid CSRF token.');
        }
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        $user = $this->getUser();

        $goals = $request->get('goals');
        foreach($goals as $i => $g)
        {
            /** @var $goal Goal */
            $goal = $user->getGoals()->filter(function (Goal $x) use($g) {return $x->getType() == $g['type'];})->first();
            $new = false;
            if($goal == null)
            {
                $goal = new Goal();
                $goal->setType($g['type']);
                $goal->setUser($user);
                $goal->setCreated(new \DateTime());
                $new = true;
            }
            $goal->setGoal($g['value']);
            $goal->setReward(($g['reward']));

            $user->addGoal($goal);
            if($new)
            {
                $orm->persist($goal);
            }
            else
                $orm->merge($goal);
            $orm->flush();
        }

        /** @var  $request Request */
        //$request = $this->container->get('request');
        /** @var  $goals Response */
        return $this->forward('StudySauceBundle:Goals:index', ['_format' => 'tab']);
    }

}
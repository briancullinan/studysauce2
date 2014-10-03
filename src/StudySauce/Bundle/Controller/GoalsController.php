<?php

namespace StudySauce\Bundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use StudySauce\Bundle\Entity\Goal;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class GoalsController
 * @package StudySauce\Bundle\Controller
 */
class GoalsController extends Controller
{
    /**
     * @param string $_format
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($_format = 'index')
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
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateAction(Request $request)
    {
        if (false === $this->get('form.csrf_provider')->isCsrfTokenValid(
                'update_schedule',
                $request->get('csrf_token')
            )
        ) {
            throw new AccessDeniedHttpException('Invalid CSRF token.');
        }
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        // get a new token so double click can't affect the post
        $csrfToken = $this->has('form.csrf_provider')
            ? $this->get('form.csrf_provider')->generateCsrfToken('update_schedule')
            : null;

        /** @var $user User */
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

        return new JsonResponse(['csrf_token' => $csrfToken, 'goals' => $this->indexAction('tab')->getContent()]);
    }

}
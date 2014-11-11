<?php

namespace StudySauce\Bundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use StudySauce\Bundle\Entity\Claim;
use StudySauce\Bundle\Entity\File;
use StudySauce\Bundle\Entity\Goal;
use StudySauce\Bundle\Entity\PartnerInvite;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @param User $user
     * @param array $template Helps partner actions work
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(User $user = null, $template = ['Goals', 'tab'])
    {
        if(empty($user))
            $user = $this->getUser();

        $csrfToken = $this->has('form.csrf_provider')
            ? $this->get('form.csrf_provider')->generateCsrfToken('update_schedule')
            : null;

        /** @var  $goals ArrayCollection */
        $goals = $user->getGoals();

        $claims = [];
        foreach($goals->toArray() as $g)
        {
            /** @var Goal $g */
            foreach($g->getClaims() as $c)
            {
                /** @var Claim $c */
                $claims[$c->getCreated()->getTimestamp()] = $c;
            }
        }
        ksort($claims);

        return $this->render('StudySauceBundle:' . $template[0] . ':' . $template[1] . '.html.php', [
                'behavior' => $goals->filter(function (Goal $x) {return $x->getType() == 'behavior';})->first(),
                'outcome' => $goals->filter(function (Goal $x) {return $x->getType() == 'outcome';})->first(),
                'milestone' => $goals->filter(function (Goal $x) {return $x->getType() == 'milestone';})->first(),
                'claims' => $claims,
                'csrf_token' => $csrfToken,
                'user' => $user
            ]);
    }

    /**
     * @param $_user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function partnerAction($_user)
    {
        /** @var $userManager UserManager */
        $userManager = $this->get('fos_user.user_manager');

        /** @var $user User */
        $user = $userManager->findUserBy(['id' => intval($_user)]);

        return $this->indexAction($user, ['Partner', 'goals']);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function widgetAction()
    {
        $user = $this->getUser();

        /** @var ArrayCollection $goals */
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

        /** @var User $user */
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
                $new = true;
            }
            $goal->setGoal($g['value']);
            $goal->setReward(($g['reward']));
            if($new)
            {
                $user->addGoal($goal);
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

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function notifyClaimAction(Request $request)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();

        /** @var User $user */
        $user = $this->getUser();

        /** @var Goal $goal */
        $goal = $user->getGoals()->filter(function (Goal $g) use($request) {return $g->getId() == $request->get('id');})->first();

        /** @var File $photo */
        $photo = $user->getFiles()->filter(function (File $g) use($request) {return $g->getId() == $request->get('photo');})->first();
        if(!empty($goal))
        {
            // create claim entity
            $claim = new Claim();
            $claim->setPhoto(empty($photo) ? null : $photo);
            $claim->setMessage($request->get('message'));
            $claim->setGoal($goal);
            $claim->setCode(md5(microtime(true)));
            $goal->addClaim($claim);
            $orm->persist($claim);
            $orm->flush();

            // send partner email
            $partner = $user->getPartnerInvites()->filter(function (PartnerInvite $p) {return $p->getActivated();})->first();
            if($partner)
            {
                $email = new EmailsController();
                $email->setContainer($this->container);
                $email->achievementAction($user, $partner);
            }
        }

        $claims = [];
        $goals = $user->getGoals()->toArray();
        foreach($goals as $g)
        {
            /** @var Goal $g */
            foreach($g->getClaims() as $c)
            {
                /** @var Claim $c */
                $claims[$c->getCreated()->getTimestamp()] = $c;
            }
        }
        ksort($claims);

        // update achievement list
        return new JsonResponse(['achievements' => $this->render('StudySauceBundle:Goals:claims.html.php', [
                    'claims' => $claims
                ])->getContent()]);
    }
}
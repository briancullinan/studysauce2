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
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContext;

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
            ? $this->get('form.csrf_provider')->generateCsrfToken('update_goals')
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
     * @param ContainerInterface $container
     */
    public static function getDemoGoals($container)
    {
        /** @var $orm EntityManager */
        $orm = $container->get('doctrine')->getManager();
        /** @var $userManager UserManager */
        $userManager = $container->get('fos_user.user_manager');
        /** @var SecurityContext $context */
        /** @var TokenInterface $token */
        /** @var User $user */
        /** @var User $guest */
        if(!empty($context = $container->get('security.context')) && !empty($token = $context->getToken()) &&
            !empty($user = $token->getUser()) && $user->hasRole('ROLE_DEMO')) {
            $guest = $user;

        }
        else {
            $guest = $userManager->findUserByUsername('guest');
        }

        $behaviorGoals = ['25', '30', '35', '40', '45', '50'];
        $behaviorRewards = ['Lunch with parents', 'Movie night', 'Date night'];
        $behavior = new Goal();
        $behavior->setType('behavior');
        $behavior->setGoal($behaviorGoals[array_rand($behaviorGoals, 1)]);
        $behavior->setReward($behaviorRewards[array_rand($behaviorRewards, 1)]);
        $behavior->setUser($guest);
        $guest->addGoal($behavior);
        $orm->persist($behavior);

        $milestoneGoals = ['B-', 'B', 'B+', 'A-', 'A', 'A+'];
        $milestoneRewards = ['Frozen yogurt', 'Fancy dinner', 'Shopping trip', '$50 gift card', 'A night off from studying.'];
        $milestone = new Goal();
        $milestone->setType('milestone');
        $milestone->setGoal($milestoneGoals[array_rand($milestoneGoals, 1)]);
        $milestone->setReward($milestoneRewards[array_rand($milestoneRewards, 1)]);
        $milestone->setUser($guest);
        $guest->addGoal($milestone);
        $orm->persist($milestone);

        $outcomeGoals = ['3.00', '3.25', '3.50', '3.75', '4.00'];
        $outcomeRewards = ['Spa day', 'Spring break trip!', 'Semester abroad'];
        $outcome = new Goal();
        $outcome->setType('outcome');
        $outcome->setGoal($outcomeGoals[array_rand($outcomeGoals, 1)]);
        $outcome->setReward($outcomeRewards[array_rand($outcomeRewards, 1)]);
        $outcome->setUser($guest);
        $guest->addGoal($outcome);
        $orm->persist($outcome);

        $orm->flush();
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

        $request->attributes->set('_format', 'tab');
        /** @var Response $goals */
        $goals = $this->indexAction();
        /** @var Response $widget */
        $widget = $this->widgetAction();
        return new Response($goals->getContent() . $widget->getContent());
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
            $claim->setCode(md5(microtime()));
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
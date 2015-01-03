<?php

namespace StudySauce\Bundle\Controller;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use StudySauce\Bundle\Entity\Coupon;
use StudySauce\Bundle\Entity\Invite;
use StudySauce\Bundle\Entity\ParentInvite;
use StudySauce\Bundle\Entity\PartnerInvite;
use StudySauce\Bundle\Entity\Payment;
use StudySauce\Bundle\Entity\StudentInvite;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use TorchAndLaurel\Bundle\Controller\EmailsController as TorchEmailsController;


/**
 * Class BuyController
 * @package StudySauce\Bundle\Controller
 */
class BuyController extends Controller
{

    const AUTHORIZENET_API_LOGIN_ID = "698Cy7dL8U";
    const AUTHORIZENET_TRANSACTION_KEY = "6AWm5h4nSu472Z52";
    const AUTHORIZENET_SANDBOX = true;

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function checkoutAction(Request $request)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        /** @var User $user */
        $user = $this->getUser();
        $csrfToken = $this->has('form.csrf_provider')
            ? $this->get('form.csrf_provider')->generateCsrfToken('checkout')
            : null;

        if(!$user->hasRole('ROLE_GUEST')) {
            $first = $user->getFirst();
            $last = $user->getLast();
            $email = $user->getEmail();
            $studentfirst = '';
            $studentlast = '';
            $studentemail = '';
        }
        else
        {
            $first = '';
            $last = '';
            $email = '';
            $studentfirst = '';
            $studentlast = '';
            $studentemail = '';
        }
        /** @var Invite $invite */
        if(!empty($request->getSession()->get('parent')))
        {
            /** @var ParentInvite $invite */
            $invite = $orm->getRepository('StudySauceBundle:ParentInvite')->findOneBy(['code' => $request->getSession()->get('parent')]);
            if(!empty($invite)) {
                $studentfirst = $invite->getFromFirst();
                $studentlast = $invite->getFromLast();
                $studentemail = $invite->getFromEmail();
            }
        }
        if(!empty($request->getSession()->get('partner')))
        {
            $invite = $orm->getRepository('StudySauceBundle:PartnerInvite')->findOneBy(['code' => $request->getSession()->get('partner')]);
        }
        if(!empty($request->getSession()->get('group')))
        {
            $invite = $orm->getRepository('StudySauceBundle:GroupInvite')->findOneBy(['code' => $request->getSession()->get('group')]);
        }
        if(!empty($request->getSession()->get('student')))
        {
            $invite = $orm->getRepository('StudySauceBundle:StudentInvite')->findOneBy(['code' => $request->getSession()->get('student')]);
        }
        if(empty($studentfirst) && !empty($invite = $user->getInvitedPartners()->filter(
                function (PartnerInvite $p) {return !$p->getUser()->hasRole('ROLE_PAID');})->first())) {
            $studentfirst = $invite->getUser()->getFirst();
            $studentlast = $invite->getUser()->getLast();
            $studentemail = $invite->getUser()->getEmail();
        }
        if(!empty($invite))
        {
            $first = $invite->getFirst();
            $last = $invite->getLast();
            $email = $invite->getEmail();
        }

        // check for coupon
        if($request->getSession()->has('coupon')) {
            $code = $request->getSession()->get('coupon');
            $coupon = $this->getCoupon($code);
            if (!empty($coupon)) {
                $options = $this->getCouponPrice($coupon);
            }
        }

        return $this->render('StudySauceBundle:Buy:checkout.html.php', [
                'email' => $email,
                'first' => $first,
                'last' => $last,
                'studentemail' => $studentemail,
                'studentfirst' => $studentfirst,
                'studentlast' => $studentlast,
                'coupon' => isset($options) ? $options : null,
                'csrf_token' => $csrfToken
            ]);
    }

    /**
     * @param $coupon
     * @return \StudySauce\Bundle\Entity\Coupon
     */
    private function getCoupon($coupon)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        $result = $orm->getRepository('StudySauceBundle:Coupon')->findAll();
        foreach($result as $i => $c) {
            /** @var Coupon $c */
            if(strtolower(substr($coupon, 0, strlen($c->getName()))) == strtolower($c->getName())) {
                // one use coupons should match exactly
                if($c->getMaxUses() <= 1 && strtolower($coupon) == strtolower($c->getName()))
                    return $c;

                // ensure code exists in random value
                for ($i = 0; $i < $c->getMaxUses(); $i++) {
                    $compareCode = $c->getName() . substr(md5($c->getSeed() . $i), 0, 6);
                    if (strtolower($coupon) == strtolower($compareCode)) {
                        return $c;
                    }
                }
            }
        }
        return null;
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function applyCouponAction(Request $request)
    {
        if(!empty($request->get('remove'))) {
            $request->getSession()->remove('coupon');
            return $this->forward('StudySauceBundle:Buy:checkout', ['_format' => 'tab']);
        }
        $code = $request->get('coupon');
        $coupon = $this->getCoupon($code);
        if(!empty($coupon)) {
            // store coupon in session for use at checkout
            $request->getSession()->set('coupon', $code);
            return $this->forward('StudySauceBundle:Buy:checkout', ['_format' => 'tab']);
        }
        return new JsonResponse(['error' => 'Coupon not found.']);
    }

    /**
     * @param Coupon $coupon
     * @return array|null
     */
    public static function getCouponPrice(Coupon $coupon)
    {
        // percentage discount
        if(substr($coupon->getType(), 0, 1) == '.') {
            $percent = floatval($coupon->getType());
            if($coupon->getTerm() !== null)
                return ['options' => [number_format(99 * $percent, 2)], 'term' => $coupon->getTerm(), 'lines' => [$coupon->getDescription()]];
            else
                return ['options' => [number_format(9.99 * $percent, 2), number_format(99 * $percent, 2)], 'lines' => [$coupon->getDescription()]];
        }
        if(substr($coupon->getType(), 0, 1) == '=') {
            $value = floatval(substr($coupon->getType(), 1));
            if($coupon->getTerm() !== null)
                return ['options' => [number_format($value, 2)], 'term' => $coupon->getTerm(), 'lines' => [$coupon->getDescription()]];
            else
                return ['options' => [number_format($value, 2)], 'term' => 12, 'lines' => [$coupon->getDescription()]];
        }
        throw new Exception('Unknown coupon type');
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function payAction(Request $request)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        /** @var $userManager UserManager */
        $userManager = $this->get('fos_user.user_manager');

        // find or create user from checkout form
        /** @var $user \StudySauce\Bundle\Entity\User */
        $user = $this->findAndCreateUser($request);

        $amount = $request->get('reoccurs') == 'yearly' ? '99.00' : '9.99';
        // apply coupon if it exists
        if($request->getSession()->has('coupon')) {
            $code = $request->getSession()->get('coupon');
            $coupon = $this->getCoupon($code);
            if(!empty($coupon)) {
                if(!empty($options = $this->getCouponPrice($coupon)))
                    $amount = $request->get('reoccurs') == 'custom'
                        ? $options['options'][0]
                        : ($request->get('reoccurs') == 'yearly'
                            ? $options['options'][1]
                            : $options['options'][0]);
            }
        }

        // create a new payment entity
        $payment = new Payment();
        $payment->setUser($user);
        $user->addPayment($payment);
        $payment->setAmount($amount);
        $payment->setFirst($request->get('first'));
        $payment->setLast($request->get('last'));
        $payment->setProduct($request->get('reoccurs'));
        $payment->setEmail($user->getEmail());

        try {
            $sale = new \AuthorizeNetAIM(self::AUTHORIZENET_API_LOGIN_ID, self::AUTHORIZENET_TRANSACTION_KEY);
            $sale->setField('amount', $amount);
            $sale->setField('card_num', $request->get('number'));
            $sale->setField('exp_date', $request->get('month') . '/' . $request->get('year'));
            $sale->setField('first_name', $request->get('first'));
            $sale->setField('last_name', $request->get('last'));
            $sale->setField(
                'address',
                $request->get('street1') .
                (empty(trim($request->get('street2'))) ? '' : ("\n" . $request->get('street2')))
            );
            $sale->setField('city', $request->get('city'));
            $sale->setField('zip', $request->get('zip'));
            $sale->setField('state', $request->get('state'));
            $sale->setField('country', $request->get('country'));
            $sale->setField('card_code', $request->get('ccv'));
            $sale->setField('recurring_billing', true);
            if($this->container->getParameter('authorize_test_mode'))
                $sale->setField('test_request', true);
            else
                $sale->setField('test_request', false);
            $sale->setField('duplicate_window', 120);
            $sale->setSandbox(false);
            $aimResponse = $sale->authorizeAndCapture();
            if ($aimResponse->approved) {
                $payment->setPayment($aimResponse->transaction_id);
            } else {
                $error = $aimResponse->response_reason_text;
            }

            // only set up reoccurring if the term is greater than zero
            if($request->get('reoccurs') != 'custom' || (isset($options['term']) && $options['term'] > 0)) {
                $subscription = new \AuthorizeNet_Subscription();
                $subscription->name = 'Study Sauce ' . ($request->get(
                        'reoccurs'
                    ) == 'yearly' ? 'Monthly' : 'Yearly') . ' Plan';
                $subscription->intervalLength = $request->get('reoccurs') == 'custom' && isset($options['term'])
                    ? $options['term']
                    : ($request->get('reoccurs') == 'yearly' ? '12' : '1');
                $subscription->intervalUnit = 'months';
                $subscription->startDate = date('Y-m-d');
                $subscription->amount = $amount;
                $subscription->creditCardCardNumber = $request->get('number');
                $subscription->creditCardExpirationDate = '20' . $request->get('year') . '-' . $request->get('month');
                $subscription->creditCardCardCode = $request->get('ccv');
                $subscription->billToFirstName = $request->get('first');
                $subscription->billToLastName = $request->get('last');
                $subscription->billToAddress = $request->get('street1') .
                    (empty(trim($request->get('street2'))) ? '' : ("\n" . $request->get('street2')));
                $subscription->billToCity = $request->get('city');
                $subscription->billToZip = $request->get('zip');
                $subscription->billToState = $request->get('state');
                $subscription->billToCountry = $request->get('country');
                $subscription->totalOccurrences = 9999;

                // TODO: if there is a duplicate subscription, increase the price

                // Create the subscription.
                $arbRequest = new \AuthorizeNetARB(self::AUTHORIZENET_API_LOGIN_ID, self::AUTHORIZENET_TRANSACTION_KEY);
                $arbRequest->setSandbox(false);
                $arbResponse = $arbRequest->createSubscription($subscription);
                if ($arbResponse->isOk()) {
                    $payment->setSubscription($arbResponse->getSubscriptionId());
                } else {
                    $error = $arbResponse->getMessageText();
                }
            }

            if (isset($error)) {
                $response = new JsonResponse(['error' => $error]);
            }
            // success
            else {
                // update paid status
                $user->addRole('ROLE_PAID');
                // set group for coupon is necessary
                if(!empty($coupon) && !empty($coupon->getGroup()) && !$user->hasGroup($coupon->getGroup()->getName())) {
                    $user->addGroup($coupon->getGroup());
                }
                $userManager->updateUser($user, false);
                if($user->hasRole('ROLE_PARENT') || $user->hasRole('ROLE_PARTNER') || $user->hasRole('ROLE_ADVISER')) {
                    $response = $this->redirect($this->generateUrl('thanks', ['_format' => 'funnel']));
                }
                // redirect to buy funnel
                else
                    $response = $this->redirect($this->generateUrl('profile', ['_format' => 'funnel']));

            }
        } catch(\AuthorizeNetException $ex) {
            $this->get('logger')->error('Authorize.Net payment failed');
            $response = new JsonResponse(['error' => 'Could not process payment, please try again later.']);
        }

        $orm->persist($payment);
        $orm->flush();
        if($payment->getPayment() !== null) {
            // send receipt
            $address = $request->get('street1') .
                (empty(trim($request->get('street2'))) ? '' : ("<br />" . $request->get('street2'))) . '<br />' .
                $request->get('city') . ' ' . $request->get('state') . '<br />' .
                $request->get('zip');

            $emails = new EmailsController();
            $emails->setContainer($this->container);
            $emails->invoiceAction($user, $payment, $address);

            // send partner prepay emails if needed
            $this->sendPartnerPrepay($user, $request);
        }

        $loginManager = $this->get('fos_user.security.login_manager');
        $loginManager->loginUser('main', $user, $response);
        return $response;
    }

    /**
     * @param User $user
     * @param Request $request
     */
    private function sendPartnerPrepay(User $user, Request $request)
    {
        $session = $request->getSession();
        if(($session->has('organization') && $session->get('organization') == 'Torch And Laurel') ||
            $user->hasGroup('Torch And Laurel')) {
            $email = new TorchEmailsController();
            $email->setContainer($this->container);
        }
        else {
            $email = new EmailsController();
            $email->setContainer($this->container);
        }

        /** @var $userManager UserManager */
        $userManager = $this->get('fos_user.user_manager');

        /** @var Invite $invite */
        /** @var User $student */
        if(!empty($invite = $user->getStudentInvites()->first())) {
            /** @var StudentInvite $invite */
            $student = $invite->getStudent();
            if(empty($student)) {
                $studentEmail = $invite->getEmail();
                $studentFirst = $invite->getFirst();
                $studentLast = $invite->getLast();
            }
        }
        else if ($user->hasRole('ROLE_PARENT')) {
            // find connected students
            /** @var ParentInvite $invite */
            $invite = $user->getInvitedParents()->first();
            $student = $invite->getUser();
            if(empty($student)) {
                $studentEmail = $invite->getFromEmail();
                $studentFirst = $invite->getFromFirst();
                $studentLast = $invite->getFromLast();
            }
        }
        else if($user->hasRole('ROLE_PARTNER')) {
            // find connected students
            /** @var Invite $invite */
            $invite = $user->getInvitedPartners()->first();
            $student = $invite->getUser();
        }

        // maybe the parent just invited their student
        if(!empty($invite)) {
            // TODO: update student account after registration
            if(!empty($student)) {
                $student->addRole('ROLE_PAID');
                $userManager->updateUser($student);
                $email->parentPrepayAction($user, $student->getEmail(), $student->getFirst(), $student->getLast(), $invite->getCode());
            }
            elseif(isset($studentEmail) && isset($studentFirst) && isset($studentLast)) {
                $email->parentPrepayAction($user, $studentEmail, $studentFirst, $studentLast, $invite->getCode());
            }
        }
    }

    /**
     * @param Request $request
     * @return \StudySauce\Bundle\Entity\User
     */
    private function findAndCreateUser(Request $request)
    {
        /** @var $orm EntityManager */
        $orm = $this->get('doctrine')->getManager();
        /** @var $userManager UserManager */
        $userManager = $this->get('fos_user.user_manager');
        $user = $this->getUser();

        // create a mock invite
        if(!empty($request->get('invite')))
        {
            /** @var User $inviteUser */
            $inviteUser = $userManager->findUserByEmail($request->get('invite')['email']);
            /** @var StudentInvite $invite */
            $invite = new StudentInvite();
            $invite->setUser($user); // might be guest here
            $invite->setFirst($request->get('invite')['first']);
            $invite->setLast($request->get('invite')['last']);
            $invite->setEmail($request->get('invite')['email']);
            $invite->setFromFirst($request->get('first'));
            $invite->setFromLast($request->get('last'));
            $invite->setFromEmail($request->get('email'));
            $invite->setCode(md5(microtime()));
            if(!empty($inviteUser))
                $invite->setStudent($inviteUser);
            $orm->persist($invite);
            $orm->flush();
        }

        // create a user from checkout only if we are currently logged in as guests
        if($user->hasRole('ROLE_GUEST')) {
            // look up existing user by email address
            /** @var User $user */
            $user = $userManager->findUserByEmail($request->get('email'));

            // create a user if anonymous
            if(empty($user)) {
                $account = new AccountController();
                $account->setContainer($this->container);
                $account->createAction($request);
                $user = $userManager->findUserByEmail($request->get('email'));
            }
            // change invite owner to the actual user
            if(isset($invite)) {
                $invite->setUser($user);
                $user->addStudentInvite($invite);
                $orm->merge($invite);
            }
            $orm->flush();

            // set the context for this load, and log in after transaction is complete
            $context = $this->get('security.context');
            $token = new UsernamePasswordToken($user, $user->getPassword(), 'main', $user->getRoles());
            $context->setToken($token);
            $session = $request->getSession();
            $session->set('_security_main', serialize($token));
        }

        return $user;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function thanksAction()
    {
        return $this->render('StudySauceBundle:Buy:thanks.html.php');
    }

    /**
     * @param User $user
     * @throws \Exception
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function cancelPaymentAction(User $user = null)
    {
        /** @var $user User */
        if(empty($user))
            $user = $this->getUser();

        $payments = $user->getPayments()->toArray();
        foreach($payments as $i => $p)
        {
            /** @var Payment $p */
            try {
                $arbRequest = new \AuthorizeNetARB(self::AUTHORIZENET_API_LOGIN_ID, self::AUTHORIZENET_TRANSACTION_KEY);
                $arbRequest->setSandbox(false);
                $arbResponse = $arbRequest->cancelSubscription($p->getSubscription());
                if ($arbResponse->isOk()) {

                }
                else {
                    throw new \Exception($arbResponse->getMessageText());
                }
            }
            catch (\Exception $ex){
                $this->get('logger')->error('Authorize.Net cancel failed: ' . $p->getSubscription());
                throw $ex;
            }
        }

        $user->removeRole('ROLE_PAID');
        /** @var $userManager UserManager */
        $userManager = $this->get('fos_user.user_manager');
        $userManager->updateUser($user);
        return new JsonResponse(true);
    }
}
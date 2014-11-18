<?php

namespace StudySauce\Bundle\Controller;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager;
use StudySauce\Bundle\Entity\ParentInvite;
use StudySauce\Bundle\Entity\PartnerInvite;
use StudySauce\Bundle\Entity\Payment;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

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

        $first = $user->getFirst();
        $last = $user->getLast();
        if(!empty($request->getSession()->get('parent')))
        {
            /** @var ParentInvite $parent */
            $parent = $orm->getRepository('StudySauceBundle:ParentInvite')->findOneBy(['code' => $request->getSession()->get('parent')]);
            $first = $parent->getFirst();
            $last = $parent->getLast();
        }

        return $this->render('StudySauceBundle:Buy:checkout.html.php', [
                'user' => $user,
                'first' => $first,
                'last' => $last,
                'csrf_token' => $csrfToken
            ]);
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

        /** @var $user \StudySauce\Bundle\Entity\User */
        $user = $this->getUser();

        $amount = $request->get('reoccurs') == 'yearly' ? '99.00' : '9.99';

        // create a new payment entity
        $payment = new Payment();
        // TODO: create a user if anonymous
        if(!empty($request->getSession()->get('parent')))
        {
            /** @var ParentInvite $parent */
            $parent = $orm->getRepository('StudySauceBundle:ParentInvite')->findOneBy(['code' => $request->getSession()->get('parent')]);
            if(!empty($parent->getParent()))
                $user = $parent->getParent();
            else {
                $user = $userManager->createUser();
                $user->setFirst($request->get('first'));
                $user->setLast($request->get('last'));
                $user->setEmail($parent->getEmail());
                $user->setEmailCanonical($parent->getEmail());
                $encoder_service = $this->get('security.encoder_factory');
                /** @var $encoder PasswordEncoderInterface */
                $encoder = $encoder_service->getEncoder($user);
                $password = $encoder->encodePassword(md5(uniqid(mt_rand(), true)), $user->getSalt());
                $user->setPassword($password);
                $user->addRole('ROLE_USER');
                $user->addRole('ROLE_PARENT');
                $user->setEnabled(true);
                $user->setUsername($parent->getEmail());
                $user->setUsernameCanonical($parent->getEmail());
                $parent->setParent($user);
                $orm->persist($user);
                $orm->merge($parent);
                $orm->flush();
            }
            if($user->hasRole('ROLE_GUEST')) {
                $context = $this->get('security.context');
                $token = new UsernamePasswordToken($user, $user->getPassword(), 'main', $user->getRoles());
                $context->setToken($token);
                $session = $request->getSession();
                $session->set('_security_main', serialize($token));
            }
        }
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
            $sale->setField('test_request', true);
            $sale->setField('duplicate_window', 120);
            $sale->setSandbox(false);
            $response = $sale->authorizeAndCapture();
            if ($response->approved) {
                $payment->setPayment($response->transaction_id);
            } else {
                $error = $response->response_reason_text;
            }

            $subscription = new \AuthorizeNet_Subscription();
            $subscription->name = 'Study Sauce ' . ($request->get(
                    'reoccurs'
                ) == 'yearly' ? 'Monthly' : 'Yearly') . ' Plan';
            $subscription->intervalLength = $request->get('reoccurs') == 'yearly' ? '12': '1';
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
            $sr = new \AuthorizeNetARB(self::AUTHORIZENET_API_LOGIN_ID, self::AUTHORIZENET_TRANSACTION_KEY);
            $sr->setSandbox(false);
            $response = $sr->createSubscription($subscription);
            if ($response->isOk()) {
                $payment->setSubscription($response->getSubscriptionId());
            }
            else {
                $error = $response->getMessageText();
            }

            if (isset($error)) {
                $response = new JsonResponse(['error' => $error]);
            }
            // success
            else {
                // update paid status
                $user->addRole('ROLE_PAID');
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

            if ($user->hasRole('ROLE_PARENT') && isset($parent)) {
                // send student email
                $student = $parent->getUser();
                $student->addRole('ROLE_PAID');
                $userManager->updateUser($student);
                $emails->parentPrepayAction($user, $student, $parent->getCode());
            }
            if($user->hasRole('ROLE_PARTNER')) {
                // send student email
                /** @var PartnerInvite $partner */
                $partner = $orm->getRepository('StudySauceBundle:PartnerInvite')->findBy(
                    ['partner' => $user->getId()]
                );
                $student = $partner->getUser();
                $student->addRole('ROLE_PAID');
                $userManager->updateUser($student);
                // TODO: create student invite?
                $emails->parentPrepayAction($user, $student, $partner->getCode());
            }
        }

        $loginManager = $this->get('fos_user.security.login_manager');
        $loginManager->loginUser('main', $user, $response);
        return $response;
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
     * @param Payment $payment
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function cancelPaymentAction(User $user = null, Payment $payment = null)
    {
        /** @var $user User */
        if(empty($user))
            $user = $this->getUser();

        $payments = $user->getPayments()->toArray();
        foreach($payments as $i => $p)
        {
            /** @var Payment $p */
            try {
                $request = new \AuthorizeNetARB(self::AUTHORIZENET_API_LOGIN_ID, self::AUTHORIZENET_TRANSACTION_KEY);
                $response = $request->cancelSubscription($p->getSubscription());
                if ($response->isOk()) {
                    $payment->setSubscription($response->getSubscriptionId());
                }
                else {
                    throw new \Exception($response->getMessageText());
                }
            }
            catch (\Exception $ex){
                $this->get('logger')->error('Authorize.Net cancel failed: ' . $p->getSubscription());
            }
        }

        $user->removeRole('ROLE_PAID');
        /** @var $userManager UserManager */
        $userManager = $this->get('fos_user.user_manager');
        $userManager->updateUser($user);
        return new JsonResponse(true);
    }
}
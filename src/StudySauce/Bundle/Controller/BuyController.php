<?php

namespace StudySauce\Bundle\Controller;

use Doctrine\ORM\EntityManager;
use StudySauce\Bundle\Entity\Payment;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function checkoutAction()
    {
        $user = $this->getUser();
        $csrfToken = $this->has('form.csrf_provider')
            ? $this->get('form.csrf_provider')->generateCsrfToken('checkout')
            : null;

        return $this->render('StudySauceBundle:Buy:checkout.html.php', [
                'user' => $user,
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

        /** @var $user \StudySauce\Bundle\Entity\User */
        $user = $this->getUser();

        $amount = $request->get('reoccurs') == 'yearly' ? '99.00' : '9.99';

        // create a new payment entity
        $payment = new Payment();
        // TODO: create a user if anonymous
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
            $response = $sale->authorizeOnly();
            if ($response->approved) {
                $payment->setPayment($response->transaction_id);
            } else {
                $error = $response->response_reason_text;
            }

            $subscription = new \AuthorizeNet_Subscription();
            $subscription->name = 'Study Sauce ' . ($request->get(
                    'reoccurs'
                ) == 'yearly' ? 'Monthly' : 'Yearly') . ' Plan';
            $subscription->intervalLength = '1';
            $subscription->intervalUnit = $request->get('reoccurs') == 'yearly' ? 'years' : 'months';
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
            $request = new \AuthorizeNetARB(self::AUTHORIZENET_API_LOGIN_ID, self::AUTHORIZENET_TRANSACTION_KEY);
            $request->setSandbox(false);
            $response = $request->createSubscription($subscription);
            if ($response->isOk()) {
                $payment->setSubscription($response->getSubscriptionId());
            }
            else {
                $error = $response->getMessageText();
            }

            if (isset($error)) {
                return new JsonResponse(['error' => $error]);
            }
            else {
                // redirect to buy funnel
                return $this->redirect($this->container->get('router')->generate('profile', ['_format' => 'funnel']));
            }
        } catch(\AuthorizeNetException $ex) {
            $this->get('logger')->error('Authorize.Net payment failed');
        }
        finally {
            $orm->persist($payment);
            $orm->flush();
        }
        return new JsonResponse(['error' => 'Could not process payment, please try again later.']);
    }

    /**
     * @param User $user
     * @param Payment $payment
     */
    public function cancelPaymentAction(User $user = null, Payment $payment = null)
    {
        /** @var $user User */
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
    }
}
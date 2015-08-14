<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Plan;

//paypal

use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;

use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\CreditCard;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\FundingInstrument;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;


class SubscriptionController extends Controller
{
    protected $plan;

    public function __construct(Plan $plan){
        $this->plan = $plan;
    }


    // Replace these values by entering your own ClientId and Secret by visiting https://developer.paypal.com/webapps/developer/applications/myapps
    protected $clientId = 'AUPqBRuH8RtA5SRYjv9ngtnt_DjOU5qzS0ee28FzhADrkF4bUoLvEWFFPaJpGaa_14ioc7BWyOeSHbLB';
    protected $clientSecret = 'EPbSP5bUkWT5LUw3K7VQHv9Ro_8BrHUlJPF5Z4UVvhTRWDoQdUmcLULchfREuC5Rbwmq1P6cAX-EWfk4';

    /** @var \Paypal\Rest\ApiContext $apiContext */



    /**
     * Helper method for getting an APIContext for all calls
     * @param string $clientId Client ID
     * @param string $clientSecret Client Secret
     * @return PayPal\Rest\ApiContext
     */
    private function getApiContext($clientId, $clientSecret)
    {

        // #### SDK configuration
        // Register the sdk_config.ini file in current directory
        // as the configuration source.
        /*
        if(!defined("PP_CONFIG_PATH")) {
            define("PP_CONFIG_PATH", __DIR__);
        }
        */


        // ### Api context
        // Use an ApiContext object to authenticate
        // API calls. The clientId and clientSecret for the
        // OAuthTokenCredential class can be retrieved from
        // developer.paypal.com

        $apiContext = new ApiContext(
            new OAuthTokenCredential(
                $clientId,
                $clientSecret
            )
        );

        // Comment this line out and uncomment the PP_CONFIG_PATH
        // 'define' block if you want to use static file
        // based configuration

        $apiContext->setConfig(
            array(
                'mode' => 'sandbox',
                'log.LogEnabled' => true,
                'log.FileName' => '../PayPal.log',
                'log.LogLevel' => 'DEBUG', // PLEASE USE `FINE` LEVEL FOR LOGGING IN LIVE ENVIRONMENTS
                'validation.level' => 'log',
                'cache.enabled' => true,
                 'http.CURLOPT_CONNECTTIMEOUT' => 30
                // 'http.headers.PayPal-Partner-Attribution-Id' => '123123123'
            )
        );

        // Partner Attribution Id
        // Use this header if you are a PayPal partner. Specify a unique BN Code to receive revenue attribution.
        // To learn more or to request a BN Code, contact your Partner Manager or visit the PayPal Partner Portal
        // $apiContext->addRequestHeader('PayPal-Partner-Attribution-Id', '123123123');

        return $apiContext;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $plans = $this->plan->paginate(10);


        return view('subscription.index',compact('plans'));
    }

    public function payment($id){

        $plan = $this->plan->find($id);

        return view('subscription.payment',compact('plan'));

    }

    public function subscribe($id,Requests\SubscriptionRequest $request){

        $plan = $this->plan->find($id);

        if(!$request->has('btnPaypal')) {
// ### CreditCard
// A resource representing a credit card that can be
// used to fund a payment.
            $card = new CreditCard();
            $card->setType($request->type)
                ->setNumber($request->get('credit_card_number'))
                ->setExpireMonth($request->get('expiration_month'))
                ->setExpireYear($request->get('expiration_year'))
                ->setCvv2($request->get('cvv'))
                ->setFirstName($request->user()->first_name)
                ->setLastName($request->user()->last_name);

// ### FundingInstrument
// A resource representing a Payer's funding instrument.
// For direct credit card payments, set the CreditCard
// field on this object.
            $fi = new FundingInstrument();
            $fi->setCreditCard($card);
        }

// ### Payer
// A resource representing a Payer that funds a payment
// For direct credit card payments, set payment method
// to 'credit_card' and add an array of funding instruments.
        if(!$request->has('btnPaypal')) {
            $payer = new Payer();
            $payer->setPaymentMethod("credit_card")
                ->setFundingInstruments(array($fi));
        }
        else{
            $payer = new Payer();
            $payer->setPaymentMethod("paypal");
        }

// ### Itemized information
// (Optional) Lets you specify item wise
// information
        $item1 = new Item();
        $item1->setName($plan->title)
            ->setDescription($plan->description)
            ->setCurrency($plan->currency_code)
            ->setQuantity(1)
            ->setTax(0)
            ->setPrice($plan->amount);


        $itemList = new ItemList();
        $itemList->setItems(array($item1));

// ### Additional payment details
// Use this optional field to set additional
// payment information such as tax, shipping
// charges etc.
//        $details = new Details();
//        $details->setShipping(1.2)
//            ->setTax(1.3)
//            ->setSubtotal(17.5);
        $details = new Details();
        $details->setSubtotal($plan->amount);

// ### Amount
// Lets you specify a payment amount.
// You can also specify additional details
// such as shipping, tax.
        $amount = new Amount();
        $amount->setCurrency($plan->currency_code)
            ->setTotal($plan->amount)
            ->setDetails($details);

// ### Transaction
// A transaction defines the contract of a
// payment - what is the payment for and who
// is fulfilling it.
        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($itemList)
            ->setDescription($plan->description)
            ->setInvoiceNumber(uniqid());




            $redirectUrls = new RedirectUrls();
            $redirectUrls->setReturnUrl(url("ExecutePayment?success=true"))
                ->setCancelUrl(url("/ExecutePayment?success=false"));


// ### Payment
// A Payment Resource; create one using
// the above types and intent set to sale 'sale'
        $payment = new Payment();
        $payment->setIntent("sale")
            ->setPayer($payer)
            ->setRedirectUrls($redirectUrls)
            ->setTransactions(array($transaction));




        try {
            $apiContext = $this->getApiContext($this->clientId, $this->clientSecret);
            $payment->create($apiContext);

            print_r($payment->getTransactions());

            if($request->has('btnPaypal')) {
                $approvalUrl = $payment->getApprovalLink();
                echo "<a href='$approvalUrl' >$approvalUrl</a>";
            }

        } catch (\PayPal\Exception\PayPalConnectionException $ex) {

            // Don't spit out errors or use "exit" like this in production code
            return redirect('/payment/'.$id)->withInput(\Input::all())->with('paypalErrors',json_decode($ex->getData()));

            exit;
        }
       exit();
    }
}

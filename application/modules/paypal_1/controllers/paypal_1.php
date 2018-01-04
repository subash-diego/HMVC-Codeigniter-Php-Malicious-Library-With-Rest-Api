<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH.'libraries/Paypal_PHP_SDK/paypal/rest-api-sdk-php/sample/bootstrap.php');

/*include class*/

use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Api\PaymentExecution;


class paypal_1 extends CI_Controller{

	public $_api_context;

	public function __construct(){
		parent::__construct();
		
		//Load Paypal Credentials
		$this->config->load('paypal_conf');
		//Create object of Paypal Class
		$this->_api_context = new \PayPal\Rest\ApiContext(new \PayPal\Auth\OAuthTokenCredential($this->config->item('client_id'),$this->config->item('secret')));

	}

	public function index($param = ''){
		
		//echo "<pre>";print_r(get_included_files());

		//view for added details of product
		//echo $this->config->item('client_id').' and '.$this->config->item('secret');
		//echo "<pre>";print_r($this->_api_context);

		$this->load->view('create_payment');
	}



	public function create_payment_using_paypal($param = ''){


		// # Create Payment using PayPal as payment method
		// This sample code demonstrates how you can process a 
		// PayPal Account based Payment.
		// API used: /v1/payments/payment

		// ### Payer
		// A resource representing a Payer that funds a payment
		// For paypal account payments, set payment method
		// to 'paypal'.

		//load settings 
		$this->_api_context->setConfig($this->config->item('settings'));


		$payer = new Payer();
		$payer->setPaymentMethod("paypal");

		$items = array(array(
			'name'		=> 'coffee cup',
			'currency'  => 'USD',
			'quantity'  => 1,
			'sku'		=> "123123",
			'price'		=> "7.50"
			),array(
			'name'		=> 'coffee bars',
			'currency'  => 'USD',
			'quantity'  => 5,
			'sku'		=> "321321",
			'price'		=> "2"
			));

		// ### Itemized information
		// (Optional) Lets you specify item wise
		// information

		$itemList = new ItemList();
		$itemList->setItems($items);

		// ### Additional payment details
		// Use this optional field to set additional
		// payment information such as tax, shipping
		// charges etc.

		$details = array(
			'shipping' => "1.20",
			'tax'      => "1.30",
			'subtotal' => "17.50"
			);

		// ### Amount
		// Lets you specify a payment amount.
		// You can also specify additional details
		// such as shipping, tax.
		$amount = array(
			'currency' => 'USD',
			'total'    => "20",
			'details'  => $details
			);

		// ### Transaction
		// A transaction defines the contract of a
		// payment - what is the payment for and who
		// is fulfilling it. 

		$transaction = array(
			'amount'	=> $amount,
			'item_list' => $itemList,
			'description'=> 'payment description',
			'invoice_number'=> uniqid()
			);

		// ### Redirect urls
		// Set the urls that the buyer must be redirected to after 
		// payment approval/ cancellation.
		$baseUrl = HOMEURL;
		$redirectUrls = new RedirectUrls();
		$redirectUrls->setReturnUrl($baseUrl."paypal_1/statuspayment")
		    ->setCancelUrl($baseUrl."paypal_1/cancelpayment");

		// ### Payment
		// A Payment Resource; create one using
		// the above types and intent set to 'sale'
		$payment = new Payment();
		$payment->setIntent("sale")
		    ->setPayer($payer)
		    ->setRedirectUrls($redirectUrls)
		    ->setTransactions(array($transaction));


		// For Sample Purposes Only.
		$request = clone $payment;

		// ### Create Payment
		// Create a payment by calling the 'create' method
		// passing it a valid apiContext.
		// (See bootstrap.php for more on `ApiContext`)
		// The return object contains the state and the
		// url to which the buyer must be redirected to
		// for payment approval
		try {
		    $payment->create($this->_api_context);
		} catch (Exception $ex) {
		    // NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
		    ResultPrinter::printError("Created Payment Using PayPal. Please visit the URL to Approve.", "Payment", null, $request, $ex);
		    exit(1);
		}

		// ### Get redirect url
		// The API response provides the url that you must redirect
		// the buyer to. Retrieve the url from the $payment->getApprovalLink()
		// method
		$approvalUrl = $payment->getApprovalLink();

		// NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
		//ResultPrinter::printResult("Created Payment Using PayPal. Please visit the URL to Approve.", "Payment", "<a href='$approvalUrl' >$approvalUrl</a>", $request, $payment);

		//return $payment;
		//echo "<pre>";print_r($payment);die;

		//process payment function redirection

		foreach ($payment->getLinks() as $link) {
			if($link->getRel() == 'approval_url'){
				$redirect_url = $link->getHref();
				break;
			}
		}

		if(isset($redirect_url)){
			redirect($redirect_url);
		}

		echo "Some Error Was Accured please try again...!";

		

	}

	public function statuspayment($param = ''){

		$post_input = $this->input->post();
		$get_input  = $this->input->get();

		//print_r($post_input);echo "out"; print_r($get_input);

		//further process for storing purpose 

		if(!empty($get_input['paymentId']) || !empty($get_input['PayerID'])){

			$payment = Payment::get($get_input['paymentId'],$this->_api_context);

			$execution = new PaymentExecution();
			$execution->setPayerId($get_input['PayerID']);

			//get result
			$result = $payment->execute($execution,$this->_api_context);

			echo "<pre>";print_r($result);die;

		}else{
			echo "transaction failed ..payment not success";
		}

	}

	public function cancelpayment($value='')
	{
		$post_input = $this->input->post();
		$get_input  = $this->input->get();

		print_r($post_input);echo "out"; print_r($get_input);
		echo "payment is cancelled";
	}
	


	
}
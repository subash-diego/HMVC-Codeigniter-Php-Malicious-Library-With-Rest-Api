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
//refund classes
use PayPal\Api\Refund;
use PayPal\Api\RefundRequest;
use PayPal\Api\Sale;



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
		$subash =array('hi' => 'hi subash ');
		
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

		echo "Some Error Was occured please try again...!";

		

	}

	public function statuspayment($param = ''){

		$post_input = $this->input->post();
		$get_input  = $this->input->get();

		//further process for storing purpose 

		if(!empty($get_input['paymentId']) || !empty($get_input['PayerID'])){

			$payment = Payment::get($get_input['paymentId'],$this->_api_context);

			$execution = new PaymentExecution();
			$execution->setPayerId($get_input['PayerID']);

			//get result
			$result = $payment->execute($execution,$this->_api_context);

			$payment_success = array();

			//result allocation
			if($result->getState() == 'approved'){

				//get proceed transaction
				$trans = $result->getTransactions();

				//processing transaction

				if(count($trans)>0){
					foreach ($trans as $key => $return_data) {
						$payment_success['total'] = !empty($return_data->amount->total)?$return_data->amount->total:FALSE;
						$payment_success['currency'] = !empty($return_data->amount->currency)?$return_data->amount->currency:FALSE;

						$payment_success['subtotal'] = !empty($return_data->amount->details->subtotal)?$return_data->amount->details->subtotal:FALSE;
						$payment_success['tax'] = !empty($return_data->amount->details->tax)?$return_data->amount->details->tax:FALSE;
						$payment_success['shipping'] = !empty($return_data->amount->details->shipping)?$return_data->amount->details->shipping:FALSE;

						$payment_success['payee'] = array(
							'merchant_id' => !empty($return_data->payee->merchant_id)?$return_data->payee->merchant_id:FALSE,
							'email' => !empty($return_data->payee->email)?$return_data->payee->email:FALSE
							);

						$payment_success['description'] = !empty($return_data->description)?$return_data->description:FALSE;
						$payment_success['invoice_number'] = !empty($return_data->invoice_number)?$return_data->invoice_number:FALSE;

						$payment_success['items'] = !empty($return_data->item_list->items)?$return_data->item_list->items:FALSE;


						$payment_success['shipping_address'] =  !empty($return_data->item_list->shipping_address)?$return_data->item_list->shipping_address:FALSE;

						//getting related resources

						if(!empty($return_data->related_resources)){
							if(count($return_data->related_resources)>0){

								//related data

								$resources_related = $return_data->related_resources;

									foreach ($resources_related as $key => $related_data) {

										$payment_success['related_resources']['id'] = !empty($related_data->sale->id)?$related_data->sale->id:FALSE;
										$payment_success['related_resources']['state'] = !empty($related_data->sale->state)?$related_data->sale->state:FALSE;
										$payment_success['related_resources']['amount'] = !empty($related_data->sale->amount)?$related_data->sale->amount:FALSE;

										$payment_success['related_resources']['payment_mode'] = !empty($related_data->sale->payment_mode)?$related_data->sale->payment_mode:FALSE;

										$payment_success['related_resources']['protection_eligibility'] = !empty($related_data->sale->protection_eligibility)?$related_data->sale->protection_eligibility:FALSE;

										$payment_success['related_resources']['protection_eligibility_type'] = !empty($related_data->sale->protection_eligibility_type)?$related_data->sale->protection_eligibility_type:FALSE;

										$payment_success['related_resources']['transaction_fee'] = !empty($related_data->sale->transaction_fee)?$related_data->sale->transaction_fee:FALSE;

										$payment_success['related_resources']['parent_payment_id'] = !empty($related_data->sale->parent_payment)?$related_data->sale->parent_payment:FALSE;

										$payment_success['related_resources']['create_time'] = !empty($related_data->sale->create_time)?$related_data->sale->create_time:FALSE;

										$payment_success['related_resources']['update_time'] = !empty($related_data->sale->update_time)?$related_data->sale->update_time:FALSE;

										$payment_success['related_resources']['self_url'] = !empty($related_data->sale->links[0]->rel)?($related_data->sale->links[0]->rel=='self'?$related_data->sale->links[0]->href:FALSE):FALSE;

										$payment_success['related_resources']['refund_url'] = !empty($related_data->sale->links[1]->rel)?($related_data->sale->links[1]->rel=='refund'?$related_data->sale->links[1]->href:FALSE):FALSE;

										$payment_success['related_resources']['parent_payment_url'] = !empty($related_data->sale->links[2]->rel)?($related_data->sale->links[2]->rel=='parent_payment'?$related_data->sale->links[2]->href:FALSE):FALSE;
									}
							}
						}
					}
				}else{
					echo "transaction failed ..while getting result time";
				}


				//processing the payment to storing data

				//if($payment_success[''])
				echo "<pre>";print_r($payment_success);
				
			}else{
				echo "transaction failed ..while approving time";
			}

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

	public function processing_transaction($value = ''){

		$payments = $this->session->userdata('last_transaction');

		echo "<pre>";print_r($payments);

		$payment_success = array();

			foreach ($payments as $key => $return_data) {

				$payment_success['total'] = !empty($return_data->amount->total)?$return_data->amount->total:FALSE;
				$payment_success['currency'] = !empty($return_data->amount->currency)?$return_data->amount->currency:FALSE;

				$payment_success['subtotal'] = !empty($return_data->amount->details->subtotal)?$return_data->amount->details->subtotal:FALSE;
				$payment_success['tax'] = !empty($return_data->amount->details->tax)?$return_data->amount->details->tax:FALSE;
				$payment_success['shipping'] = !empty($return_data->amount->details->shipping)?$return_data->amount->details->shipping:FALSE;

				$payment_success['payee'] = array(
					'merchant_id' => !empty($return_data->payee->merchant_id)?$return_data->payee->merchant_id:FALSE,
					'email' => !empty($return_data->payee->email)?$return_data->payee->email:FALSE
					);

				$payment_success['description'] = !empty($return_data->description)?$return_data->description:FALSE;
				$payment_success['invoice_number'] = !empty($return_data->invoice_number)?$return_data->invoice_number:FALSE;

				$payment_success['items'] = !empty($return_data->item_list->items)?$return_data->item_list->items:FALSE;


				$payment_success['shipping_address'] =  !empty($return_data->item_list->shipping_address)?$return_data->item_list->shipping_address:FALSE;

				if(!empty($return_data->related_resources)){

					if(count($return_data->related_resources)>0){

						//getting other resource 

						$resources_related = $return_data->related_resources;

						if($resources_related!=''){
							foreach ($resources_related as $key => $related_data) {

								$payment_success['related_resources']['id'] = !empty($related_data->sale->id)?$related_data->sale->id:FALSE;
								$payment_success['related_resources']['state'] = !empty($related_data->sale->state)?$related_data->sale->state:FALSE;
								$payment_success['related_resources']['amount'] = !empty($related_data->sale->amount)?$related_data->sale->amount:FALSE;

								$payment_success['related_resources']['payment_mode'] = !empty($related_data->sale->payment_mode)?$related_data->sale->payment_mode:FALSE;

								$payment_success['related_resources']['protection_eligibility'] = !empty($related_data->sale->protection_eligibility)?$related_data->sale->protection_eligibility:FALSE;

								$payment_success['related_resources']['protection_eligibility_type'] = !empty($related_data->sale->protection_eligibility_type)?$related_data->sale->protection_eligibility_type:FALSE;

								$payment_success['related_resources']['transaction_fee'] = !empty($related_data->sale->transaction_fee)?$related_data->sale->transaction_fee:FALSE;

								$payment_success['related_resources']['parent_payment_id'] = !empty($related_data->sale->parent_payment)?$related_data->sale->parent_payment:FALSE;

								$payment_success['related_resources']['create_time'] = !empty($related_data->sale->create_time)?$related_data->sale->create_time:FALSE;

								$payment_success['related_resources']['update_time'] = !empty($related_data->sale->update_time)?$related_data->sale->update_time:FALSE;

								$payment_success['related_resources']['self_url'] = !empty($related_data->sale->links[0]->rel)?($related_data->sale->links[0]->rel=='self'?$related_data->sale->links[0]->href:FALSE):FALSE;

								$payment_success['related_resources']['refund_url'] = !empty($related_data->sale->links[1]->rel)?($related_data->sale->links[1]->rel=='refund'?$related_data->sale->links[1]->href:FALSE):FALSE;

								$payment_success['related_resources']['parent_payment_url'] = !empty($related_data->sale->links[2]->rel)?($related_data->sale->links[2]->rel=='parent_payment'?$related_data->sale->links[2]->href:FALSE):FALSE;
							}
						}

					}

				}

			}

			echo "<pre>";print_r($payment_success);

	}

	//refund

	public function refund($param = ''){
		$this->load->view('refund');
	}

	//processing refund

	public function refund_amount($param = ''){
		
		$refund_amount = $this->input->post('amount');
		$sale_id	   = $this->input->post('id');

		//
		$amt = new Amount();
		$amt->setCurrency('USD')
			->setTotal($refund_amount);
		
		//refund object

		$refundRequest = new RefundRequest();
		$refundRequest->setAmount($amt);

		//sale

		$sale = new Sale();
		$sale->setId($sale_id);


		//
		try{
			$refundedSale = $sale->refundSale($refundRequest, $this->_api_context);
		}catch(Exception $e){
			echo "refund error ->";print_r($e);
		}

		//store refund data
		$refund_statement = array();
		$refund_statement['refund_id'] = !empty($refund->id)?$refund->id:FALSE;
		$refund_statement['create_time'] = !empty($refund->create_time)?$refund->create_time:FALSE;
		$refund_statement['update_time'] = !empty($refund->update_time)?$refund->update_time:FALSE;
		$refund_statement['state'] = !empty($refund->state)?$refund->state:FALSE;
		$refund_statement['amount']['total'] = !empty($refund->amount->total)?$refund->amount->total:FALSE;
		$refund_statement['amount']['currency'] = !empty($refund->amount->currency)?$refund->amount->currency:FALSE;
		$refund_statement['refund_transaction_fee'] = !empty($refund->refund_from_transaction_fee->value)?$refund->refund_from_transaction_fee->value:FALSE;
		$refund_statement['refund_from_received_amount'] = !empty($refund->refund_from_received_amount->value)?$refund->refund_from_received_amount->value:FALSE;
		$refund_statement['sale_id'] = !empty($refund->sale_id)?$refund->sale_id:FALSE;
		$refund_statement['parent_payment'] = !empty($refund->parent_payment)?$refund->parent_payment:FALSE;
		$refund_statement['links'] = !empty($refund->links)?$refund->links:FALSE;



	}


	public function refund_process($value='')
	{
		$refund  = $this->session->userdata('refunds');
		$refund_statement = array();
		$refund_statement['refund_id'] = !empty($refund->id)?$refund->id:FALSE;
		$refund_statement['create_time'] = !empty($refund->create_time)?$refund->create_time:FALSE;
		$refund_statement['update_time'] = !empty($refund->update_time)?$refund->update_time:FALSE;
		$refund_statement['state'] = !empty($refund->state)?$refund->state:FALSE;
		$refund_statement['amount']['total'] = !empty($refund->amount->total)?$refund->amount->total:FALSE;
		$refund_statement['amount']['currency'] = !empty($refund->amount->currency)?$refund->amount->currency:FALSE;
		$refund_statement['refund_transaction_fee'] = !empty($refund->refund_from_transaction_fee->value)?$refund->refund_from_transaction_fee->value:FALSE;
		$refund_statement['refund_from_received_amount'] = !empty($refund->refund_from_received_amount->value)?$refund->refund_from_received_amount->value:FALSE;
		$refund_statement['sale_id'] = !empty($refund->sale_id)?$refund->sale_id:FALSE;
		$refund_statement['parent_payment'] = !empty($refund->parent_payment)?$refund->parent_payment:FALSE;
		$refund_statement['links'] = !empty($refund->links)?$refund->links:FALSE;
		echo "<pre>";print_r(date('Y-m-d H:i:s', strtotime($refund_statement['create_time'])));
	}
	


	
}
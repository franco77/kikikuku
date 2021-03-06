<?php defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class API extends REST_Controller
{

	public function __construct()
	{

		parent::__construct();

		$this->load->model('M_api', 'api');
		$this->load->model('M_user', 'user');
		$this->load->model('M_cart', 'carts');
		$this->load->model('M_category', 'category');
		$this->load->model('M_profile', 'profile');
		$this->load->model('M_reset', 'reset');
		$this->load->library('email');
		$this->load->library('incube');
		// $this->output->enable_profiler(TRUE);

		//DEBUG LAST QUERY
		// echo $this->db->last_query();

	}

	public function index_get()
	{

		$this->response([
			'status' => 'error',
			'message' => 'api path error',
			'code' => REST_Controller::HTTP_NOT_FOUND
		], REST_Controller::HTTP_NOT_FOUND);
	}

	//CUSTOM HOME PAGE
	public function home_get()
	{

		$randomPage 		= mt_rand(1, 500);
		$pageSize   		= $this->input->get('pageSize');
		$pageCounter 		= $this->input->get('page');
		$counter    		= 0;
		$marginParameter 	= $this->product->getMarginPrice();
		$convertRate		= $this->product->getConvertRate();

		if ($pageSize != null) {
			$json 	= file_get_contents("https://en.yiwugo.com/ywg/productlist.html?account=Wien.suh@gmail.com&pageSize=" . $pageSize . "&cpage=" . $randomPage);
		} else {
			$json 	= file_get_contents("https://en.yiwugo.com/ywg/productlist.html?account=Wien.suh@gmail.com&pageSize=" . $pageSize . "&cpage=" . $pageCounter);
		}

		$obj 	= json_decode($json, true);

		$result = array();

		foreach ($obj['prslist'] as $list) {

			//Use custom library for Image Formating
			$newPath = $this->incube->replaceLink($list['picture2']);

			$result[$counter]['ID'] 			= strval($list['id']);
			$result[$counter]['TITLE']			= $list['title'];
			$result[$counter]['PICTURE']		= $newPath . $list['picture2'];
			// $result[$counter]['ORIGINAL_PRICE']	= $list['sellPrice'];
			$result[$counter]['START_QUANTITY'] = strval($list['startNumber']);

			if ($list['priceType'] == '0') {
				$result[$counter]['PRICE']			= 'Price Negotiable';
			} else {
				$price = $this->incube->setPrice($convertRate, $marginParameter, $list['sellPrice']);
				// $result[$counter]['PRICE']			= number_format($price, 2, '.', ',');
				$result[$counter]['PRICE']			= strval($price);
			}

			$counter++;
		}

		$jsonResult = json_encode($result);

		$this->response([
			'status' 	=> 'ok',
			'pageSize' 	=> $pageSize,
			'code' 		=> REST_Controller::HTTP_ACCEPTED,
			'item'      => json_decode($jsonResult, true),
		], REST_Controller::HTTP_ACCEPTED);
	}

	//SEARCH CUSTOM PAGE
	public function search_get()
	{

		$counter    		= 0;
		$pageSize   		= $this->input->get('pageSize');
		$searchQuery 		= $this->input->get('query');
		$marginParameter 	= $this->product->getMarginPrice();
		$convertRate		= $this->product->getConvertRate();

		$json 	= file_get_contents("https://en.yiwugo.com/ywg/productlist.html?account=Wien.suh@gmail.com&q=" . $searchQuery . "&pageSize=" . $pageSize . "&cpage=" . $this->input->get('page'));
		$obj 	= json_decode($json, true);

		$result = array();

		if ($obj['prslist'] == null) {

			$this->response([
				'status' 		=> 'ok',
				'pageSize' 		=> $pageSize,
				'searchQuery'	=> $searchQuery,
				'code' 			=> REST_Controller::HTTP_ACCEPTED,
				'item'      	=> 'No Product Found',
			], REST_Controller::HTTP_ACCEPTED);
		} else {

			foreach ($obj['prslist'] as $list) {

				//Use custom library for Image Formating
				$newPath = $this->incube->replaceLink($list['picture2']);

				$result[$counter]['ID'] 			= $list['id'];
				$result[$counter]['TITLE']			= $list['title'];
				$result[$counter]['PICTURE']		= $newPath . $list['picture2'];
				// $result[$counter]['ORIGINAL_PRICE']	= $list['sellPrice'];
				$result[$counter]['START_QUANTITY'] = strval($list['startNumber']);

				if ($list['priceType'] == '0') {
					$result[$counter]['PRICE']			= 'Price Negotiable';
				} else {
					$price = $this->incube->setPrice($convertRate, $marginParameter, $list['sellPrice']);
					// $result[$counter]['PRICE']			= number_format($price, 2, '.', ',');
					$result[$counter]['PRICE']			= strval($price);
				}

				$counter++;
			}

			$jsonResult = json_encode($result);

			$this->response([
				'status' 		=> 'ok',
				'pageSize' 		=> $pageSize,
				'searchQuery'	=> $searchQuery,
				'code' 			=> REST_Controller::HTTP_ACCEPTED,
				'item'      	=> json_decode($jsonResult, true),
			], REST_Controller::HTTP_ACCEPTED);
		}
	}

	//Get Product From Category
	public function categoryProduct_get()
	{
		$pageSize    = $this->input->get('pageSize');
		$categoryID  = $this->input->get('category');
		$pageCounter = $this->input->get('page');
		$counter     = 0;
		$marginParameter 	= $this->product->getMarginPrice();
		$convertRate		= $this->product->getConvertRate();

		$json 	= file_get_contents("http://en.yiwugo.com/ywg/productlist.html?account=Wien.suh@gmail.com&s=" . $categoryID . "&pageSize=" . $pageSize . "&cpage=" . $pageCounter);
		$obj 	= json_decode($json, true);

		$result = array();

		if ($obj['prslist'] == null) {

			$this->response([
				'status' 		=> 'ok',
				'pageSize' 		=> $pageSize,
				'categoryID'	=> $categoryID,
				'code' 			=> REST_Controller::HTTP_ACCEPTED,
				'item'      	=> 'No Product Found',
			], REST_Controller::HTTP_ACCEPTED);
		} else {

			foreach ($obj['prslist'] as $list) {

				//Use custom library for Image Formating
				$newPath = $this->incube->replaceLink($list['picture2']);

				$result[$counter]['ID'] 			= $list['id'];
				$result[$counter]['TITLE']			= $list['title'];
				$result[$counter]['PICTURE']		= $newPath . $list['picture2'];
				// $result[$counter]['ORIGINAL_PRICE']	= $list['sellPrice'];
				$result[$counter]['START_QUANTITY'] = $list['startNumber'];

				if ($list['priceType'] == '0') {
					$result[$counter]['PRICE']			= 'Price Negotiable';
				} else {
					$price = $this->incube->setPrice($convertRate, $marginParameter, $list['sellPrice']);
					// $result[$counter]['PRICE']			= number_format($price, 2, '.', ',');
					$result[$counter]['PRICE']			= $price;
				}

				$counter++;
			}

			$jsonResult = json_encode($result);

			$this->response([
				'status' 		=> 'ok',
				'pageSize' 		=> $pageSize,
				'categoryID'	=> $categoryID,
				'code' 			=> REST_Controller::HTTP_ACCEPTED,
				'item'      	=> json_decode($jsonResult, true),
			], REST_Controller::HTTP_ACCEPTED);
		}
	}

	//Get Product Detail
	public function product_get()
	{

		$id			= $this->input->get('id');
		$counter 	= 0;
		$finalUrl   = 'http://en.yiwugo.com/ywg/productdetail.html?account=Wien.suh@gmail.com&productId=' . $id;
		$marginParameter 	= $this->product->getMarginPrice();
		$convertRate		= $this->product->getConvertRate();

		$json 	= file_get_contents($finalUrl);
		$obj 	= json_decode($json, true);

		if (isset($obj['tip'])) {

			$this->response([
				'status' 		=> 'error',
				'code' 			=> REST_Controller::HTTP_ACCEPTED,
				'item'      	=> 'not found',
			], REST_Controller::HTTP_ACCEPTED);
		} else {

			$productForApp['TITLE'] 	= ucwords($obj['detail']['productForApp']['title']);
			$productForApp['MATRIC']	= ucwords($this->incube->changeItemMatric($obj['detail']['productForApp']['matrisingular']));
			$productForApp['DETAILS']	= ucwords($obj['detail']['productForApp']['introduction']);
			$estWeight 					= $obj['detail']['productForApp']['weightetc'];

			//FIX FOR IMAGE
			if ($obj['detail']['sdiProductsPicList'] == null) {

				if ($obj['detail']['productForApp']['picture'] != null) {
					//Use custom library for Image Formating
					$newPath = $this->incube->replaceLink($obj['detail']['productForApp']['picture']);
					$productForApp['PICTURE_LIST'][]['PICTURE1'] = $newPath . $obj['detail']['productForApp']['picture'];
					$productForApp['MAIN_PICTURE'] = $newPath . $obj['detail']['productForApp']['picture'];
				}

				if ($obj['detail']['productForApp']['picture1'] != null) {
					//Use custom library for Image Formating
					$newPath = $this->incube->replaceLink($obj['detail']['productForApp']['picture1']);
					$productForApp['PICTURE_LIST'][]['PICTURE1'] = $newPath . $obj['detail']['productForApp']['picture1'];
					$productForApp['MAIN_PICTURE'] = $newPath . $obj['detail']['productForApp']['picture1'];
				}

				if ($obj['detail']['productForApp']['picture2'] != null) {
					//Use custom library for Image Formating
					$newPath = $this->incube->replaceLink($obj['detail']['productForApp']['picture2']);
					$productForApp['PICTURE_LIST'][]['PICTURE1'] = $newPath . $obj['detail']['productForApp']['picture2'];
					$productForApp['MAIN_PICTURE'] = $newPath . $obj['detail']['productForApp']['picture2'];
				}

				if ($obj['detail']['productForApp']['picture3'] != null) {
					//Use custom library for Image Formating
					$newPath = $this->incube->replaceLink($obj['detail']['productForApp']['picture3']);
					$productForApp['PICTURE_LIST'][]['PICTURE1'] = $newPath . $obj['detail']['productForApp']['picture3'];
					$productForApp['MAIN_PICTURE'] = $newPath . $obj['detail']['productForApp']['picture3'];
				}

				if ($obj['detail']['productForApp']['picture4'] != null) {
					//Use custom library for Image Formating
					$newPath = $this->incube->replaceLink($obj['detail']['productForApp']['picture4']);
					$productForApp['PICTURE_LIST'][]['PICTURE5'] = $newPath . $obj['detail']['productForApp']['picture4'];
					$productForApp['MAIN_PICTURE'] = $newPath . $obj['detail']['productForApp']['picture3'];
				}
			} else {

				foreach ($obj['detail']['sdiProductsPicList'] as $picture) {

					if ($picture['picture'] != null) {
						//Use custom library for Image Formating
						$newPath = $this->incube->replaceLink($picture['picture']);
						$productForApp['PICTURE_LIST'][]['PICTURE1'] = $newPath . $picture['picture'];
						$productForApp['MAIN_PICTURE'] = $newPath . $picture['picture'];
					}

					if ($picture['picture1'] != null) {
						//Use custom library for Image Formating
						$newPath = $this->incube->replaceLink($picture['picture1']);
						$productForApp['PICTURE_LIST'][]['PICTURE2'] = $newPath . $picture['picture1'];
						$productForApp['MAIN_PICTURE'] = $newPath . $picture['picture1'];
					}

					if ($picture['picture2'] != null) {
						//Use custom library for Image Formating
						$newPath = $this->incube->replaceLink($picture['picture2']);
						$productForApp['PICTURE_LIST'][]['PICTURE3'] = $newPath . $picture['picture2'];
						$productForApp['MAIN_PICTURE'] = $newPath . $picture['picture2'];
					}

					if ($picture['picture3'] != null) {
						//Use custom library for Image Formating
						$newPath = $this->incube->replaceLink($picture['picture3']);
						$productForApp['PICTURE_LIST'][]['PICTURE4'] = $newPath . $picture['picture3'];
						$productForApp['MAIN_PICTURE'] = $newPath . $picture['picture3'];
					}

					if ($picture['picture4'] != null) {
						//Use custom library for Image Formating
						$newPath = $this->incube->replaceLink($picture['picture4']);
						$productForApp['PICTURE_LIST'][]['PICTURE5'] = $newPath . $picture['picture4'];
						$productForApp['MAIN_PICTURE'] = $newPath . $picture['picture4'];
					}
				}
			}

			//CREATE PRICE LIST
			if ($obj['detail']['sdiProductsPriceList'] == null) {

				//Check if the price is appropriate
				if ($this->incube->priceEmpty($obj['detail']['productForApp']['sellPrice'])) {
					$productForApp['PRICE'][$counter]['PRICE'] = 'Price Negotiable';
					$productForApp['PRICE'][$counter]['STARTING_QUANTITY'] = '1';
					$productForApp['PRICE'][$counter]['ENDING_QUANTITY'] = '';
					$productForApp['PRICE'][$counter]['FLAG'] = 'No EXW Price';

					$startingPrice 		= '0';
					$startingQuantity 	= '1';
				} else {
					$price = $this->incube->setPrice($convertRate, $marginParameter, $obj['detail']['productForApp']['sellPrice']);
					// $productForApp['PRICE']['STARTING_PRICE'] = number_format($price, 2, '.', ',');
					$productForApp['PRICE'][$counter]['PRICE'] = strval($price);
					$productForApp['PRICE'][$counter]['STARTING_QUANTITY'] = '1';
					$productForApp['PRICE'][$counter]['FLAG'] = 'No EXW Price';
					$productForApp['PRICE'][$counter]['ENDING_QUANTITY'] = '';

					$startingPrice 		= $price;
					$startingQuantity 	= '1';
				}
			} else {

				//Loop through each price
				foreach ($obj['detail']['sdiProductsPriceList'] as $priceList) {

					$price = $this->incube->setPrice($convertRate, $marginParameter, $priceList['sellPrice']);

					$productForApp['PRICE'][$counter]['PRICE'] 				= strval($price);
					$productForApp['PRICE'][$counter]['STARTING_QUANTITY'] 	= strval($priceList['startNumber']);
					$productForApp['PRICE'][$counter]['FLAG'] = 'EXW Price Exist';

					if ($priceList['endNumber'] == 0) {
						$productForApp['PRICE'][$counter]['ENDING_QUANTITY'] 	= 'Above ' . $priceList['startNumber'];
					} else {
						$productForApp['PRICE'][$counter]['ENDING_QUANTITY'] 	= strval($priceList['endNumber']);
					}

					//$productForApp['PRICE'][$counter]['PRICE'] 					= number_format($price, 2, '.', ',');
					if ($priceList['endNumber'] == 0) {
						$startingQuantity = $priceList['startNumber'];
						$startingPrice = $price;
					} else if ($priceList['endNumber'] == 1) {
						$startingQuantity = $priceList['startNumber'];
						$startingPrice = $price;
					} else if ($priceList['startNumber'] < $priceList['endNumber']) {
						$startingQuantity = $priceList['startNumber'];
						$startingPrice = $price;
					}

					$counter++;
				}
			}

			$jsonResult = json_encode($productForApp);

			$this->response([
				'status' 			=> 'ok',
				'productID' 		=> $id,
				'minimumOrder'		=> strval($startingQuantity),
				'startingPrice'		=> strval($startingPrice),
				'matrics'			=> $productForApp['MATRIC'],
				'estimated_weight'  => $estWeight,
				'code' 				=> REST_Controller::HTTP_ACCEPTED,
				'item'      		=> json_decode($jsonResult, true),
			], REST_Controller::HTTP_ACCEPTED);
		}
	}

	//Get Correct Pricing
	public function pricing_get()
	{

		$productID = $this->input->get('id');
		$quantity  = $this->input->get('quantity');
		$marginParameter 	= $this->product->getMarginPrice();
		$convertRate		= $this->product->getConvertRate();

		$finalUrl = 'http://en.yiwugo.com/ywg/productdetail.html?account=Wien.suh@gmail.com&productId=' . $productID;

		$json 	= file_get_contents($finalUrl);
		$obj 	= json_decode($json, true);

		if (isset($obj['tip'])) {

			$this->response([
				'status' 		=> 'error',
				'code' 			=> REST_Controller::HTTP_BAD_REQUEST,
				'item'      	=> 'not found',
			], REST_Controller::HTTP_BAD_REQUEST);
		} else {

			if ($obj['detail']['sdiProductsPriceList'] == null) {

				//Check if the price is appropriate
				if ($this->incube->priceEmpty($obj['detail']['productForApp']['sellPrice'])) {
					$startingPrice 		= 'Price Negotiable';
				} else {
					$price = $this->incube->setPrice($convertRate, $marginParameter, $obj['detail']['productForApp']['sellPrice']);
					$startingPrice 		= $price;
				}

				$this->response([
					'status' => 'ok',
					'price'	 => strval($startingPrice),
					'code' 	=> REST_Controller::HTTP_OK,
				], REST_Controller::HTTP_OK);
			} else {
				//Get the Pricing from Library
				$newPricing = $this->incube->getCorrectPrice($convertRate, $marginParameter, $quantity, $obj['detail']['sdiProductsPriceList']);

				if (!$newPricing) {
					$this->response([
						'status' 	=> 'error',
						'message'	=> 'quantity error',
						'code' 		=> REST_Controller::HTTP_BAD_REQUEST,
					], REST_Controller::HTTP_BAD_REQUEST);
				} else {
					$this->response([
						'status' 			=> 'ok',
						'price'	 			=> strval($newPricing['price']),
						'startingQuantity'	=> strval($newPricing['start']),
						'endingQUantity'	=> strval($newPricing['end']),
						'code' 				=> REST_Controller::HTTP_OK,
					], REST_Controller::HTTP_OK);
				}
			}
		}
	}

	//GET MESSAGE
	public function message_get()
	{

		$orderID 	 = $this->input->get('id');
		$messageData = $this->profile->getOrderMessages($orderID);
		$counter 	 = 0;

		if ($messageData->num_rows() > 0) {

			foreach ($messageData->result() as $data) {

				$message[$counter] = array(
					'SENDER' 			=> $data->SENDER_ID,
					'MESSAGE'			=> $data->MESSAGE,
					'MESSAGE_TIME'		=> $data->MESSAGE_TIME,
					'USER_READ_FLAG'	=> $data->USER_READ_FLAG,
					'ADMIN_READ_FLAG'	=> $data->ADMIN_READ_FLAG
				);

				$counter++;
			}

			$encode = json_encode($message);

			$this->response([
				'status' 		=> 'ok',
				'message' 		=> $messageData->num_rows() . ' messages found',
				'code' 			=> REST_Controller::HTTP_ACCEPTED,
				'message_data' 	=> json_decode($encode)
			], REST_Controller::HTTP_ACCEPTED);
		} else {

			$this->response([
				'status' 		=> 'ok',
				'message' 		=> 'no message history',
				'code' 			=> REST_Controller::HTTP_ACCEPTED,
				'message_data' 	=> '0'
			], REST_Controller::HTTP_ACCEPTED);
		}

		// echo $this->db->last_query();

	}

	//SEND MESSAGE
	public function message_post()
	{

		$data = array(
			'SENDER_ID' 		=> 'CUSTOMER',
			'ORDER_ID' 			=> $this->input->post('orderID'),
			'MESSAGE' 			=> $this->input->post('message'),
			'MESSAGE_TIME' 		=> date('Y-m-d H:m:s'),
			'USER_READ_FLAG' 	=> '0',
			'ADMIN_READ_FLAG' 	=> '1'
		);

		$messageData = $this->profile->getOrderMessages($this->input->post('orderID'));

		if ($messageData->num_rows() > 0) {

			if ($this->profile->sendMessages($data)) {
				$this->response([
					'status' 		=> 'ok',
					'message' 		=> 'message send',
					'code' 			=> REST_Controller::HTTP_ACCEPTED
				], REST_Controller::HTTP_ACCEPTED);
			} else {
				$this->response([
					'status' 		=> 'error',
					'message' 		=> 'message not sending',
					'code' 			=> REST_Controller::HTTP_BAD_REQUEST
				], REST_Controller::HTTP_BAD_REQUEST);
			}
		} else {

			$this->response([
				'status' 		=> 'error',
				'message' 		=> 'invalid order ID',
				'code' 			=> REST_Controller::HTTP_BAD_REQUEST
			], REST_Controller::HTTP_BAD_REQUEST);
		}
	}

	public function profile_get()
	{

		$query = $this->user->getMemberData($this->input->get('email'));

		if ($query->num_rows() > 0) {

			foreach ($query->result() as $data) {

				$dataSess = array(
					'FIRST_NAME' 		=> $data->FIRST_NAME,
					'LAST_NAME' 		=> $data->LAST_NAME,
					'PHONE' 			=> $data->PHONE,
					'PROFILE_PICTURE'	=> $data->IMAGE,
					'EMAIL' 			=> $data->EMAIL,
					'ADDRESS' 			=> $data->ADDRESS,
					'COUNTRY' 			=> $data->COUNTRY,
					'PROVINCE' 			=> $data->PROVINCE,
					'USERID' 			=> $data->ID,
					'ZIP' 				=> $data->ZIP,
				);

				$decode = json_encode($dataSess);
			}

			$this->response([
				'status' 		=> 'ok',
				'message' 		=> 'member found',
				'code' 			=> REST_Controller::HTTP_ACCEPTED,
				'data'			=> json_decode($decode)
			], REST_Controller::HTTP_ACCEPTED);
		} else {
			$this->response([
				'status' 		=> 'error',
				'message' 		=> 'no member data',
				'code' 			=> REST_Controller::HTTP_BAD_REQUEST
			], REST_Controller::HTTP_BAD_REQUEST);
		}

		// echo $this->db->last_query();
	}

	//UPDATE MEMBER PHONE
	public function profilephone_post()
	{

		$idQuery = $this->user->getMemberbyID($this->input->post('userID'));

		if ($idQuery->num_rows() > 0) {

			$data = array(
				'PHONE' => $this->input->post('phone')
			);

			$this->profile->updatePhone($this->input->post('userID'), $data);

			$this->response([
				'status' 		=> 'ok',
				'message' 		=> 'phone updated',
				'code' 			=> REST_Controller::HTTP_ACCEPTED
			], REST_Controller::HTTP_ACCEPTED);
		} else {
			$this->response([
				'status' 		=> 'error',
				'message' 		=> 'update error',
				'code' 			=> REST_Controller::HTTP_BAD_REQUEST
			], REST_Controller::HTTP_BAD_REQUEST);
		}
	}

	//UPDATE MEMBER IMAGE
	public function profilephoto_post()
	{
	}

	//UPDATE MEMBER ADDRESS
	public function profileaddress_post()
	{

		$idQuery = $this->user->getMemberbyID($this->input->post('userID'));

		if ($idQuery->num_rows() > 0) {

			$data = array(
				'ADDRESS' 		=> $this->input->post('address1'),
				'ADDRESS_2'  	=> $this->input->post('address2'),
				'COUNTRY' 		=> $this->input->post('country'),
				'PROVINCE' 		=> $this->input->post('province'),
				'ZIP' 			=> $this->input->post('zip'),
			);

			$query = $this->profile->updateAddress($this->input->post('userID'), $data);

			$this->response([
				'status' 		=> 'ok',
				'message' 		=> 'address updated',
				'code' 			=> REST_Controller::HTTP_ACCEPTED
			], REST_Controller::HTTP_ACCEPTED);
		} else {
			$this->response([
				'status' 		=> 'error',
				'message' 		=> 'update error',
				'code' 			=> REST_Controller::HTTP_BAD_REQUEST
			], REST_Controller::HTTP_BAD_REQUEST);
		}
	}

	//UPDATE MEMBER PASSWORD
	public function reset_post()
	{

		$regularEmail = $this->input->post('email');

		$queryResult = $this->user->checkExistingEmail($regularEmail);

		if ($queryResult->num_rows() < 0) {
			$this->response([
				'status' 		=> 'error',
				'message' 		=> 'email not found',
				'code' 			=> REST_Controller::HTTP_BAD_REQUEST
			], REST_Controller::HTTP_BAD_REQUEST);
		} else {
			$id = date('y-m-d-h-m-s');

			$hashID = sha1($id);

			$resetData = array(
				'USER_EMAIL'   => $regularEmail,
				'RESET_ID'    => $hashID,
				'RESET_DATE'	=> $id,
				'RESET_STATUS' => 'ACTIVE'
			);

			$config['protocol']    = 'smtp';
			$config['smtp_host']   = 'mail.kikikuku.com';
			$config['smtp_user']   = 'admin@kikikuku.com';
			$config['smtp_pass']   = 'yMiBWEq=H+NN';
			$config['smtp_port']   = 587;
			$config['charset']     = 'utf-8';
			$config['newline']     = "\r\n";
			$config['wordwrap']    = TRUE;
			$config['mailtype']    = 'html';

			$this->email->initialize($config);

			$this->email->from('admin@kikikuku.com', 'Kikikuku Team');
			$this->email->to($regularEmail);
			$this->email->set_mailtype('html');

			$message = $this->load->view('email-template/reset-password', $resetData, true);

			$this->email->subject('Please confirm your email address');
			$this->email->message($message);

			if ($this->email->send()) {
				$this->user->sentResetPassword($resetData);

				$this->response([
					'status' 		=> 'ok',
					'message' 		=> 'reset email sent',
					'code' 			=> REST_Controller::HTTP_ACCEPTED
				], REST_Controller::HTTP_ACCEPTED);
			} else {
				$this->response([
					'status' 		=> 'error',
					'message' 		=> 'email cannot be send',
					'code' 			=> REST_Controller::HTTP_BAD_GATEWAY
				], REST_Controller::HTTP_BAD_GATEWAY);
			}
		}
	}



	//UPLOAD PAYMENT PROOF
	public function payment_post()
	{

		$messageData = $this->profile->getPaymentProcess($this->input->post('orderID'));

		if ($messageData->num_rows() > 0) {

			$config['upload_path'] 		= './img/order/';
			$config['allowed_types'] 	= 'gif|jpg|png|jpeg';
			$config['file_name'] 		= $this->input->post('orderID');

			$this->load->library('upload', $config);

			//GET THE FILE EXTENSION FOR SAVING THE DATA TO DATABASE
			$path = $_FILES['paymentImage']['name'];
			$ext = pathinfo($path, PATHINFO_EXTENSION);

			$defaultPath = '/img/order/' . $this->input->post('orderID') . '.' . $ext;

			if (!$this->upload->do_upload('paymentImage')) {

				$this->response([
					'status' 		=> 'error',
					'message' 		=> 'payment process error',
					'code' 			=> REST_Controller::HTTP_BAD_REQUEST
				], REST_Controller::HTTP_BAD_REQUEST);
			} else {

				$data = array(
					'ORDER_ID'        => $this->input->post('orderID'),
					'ACCOUNT_NAME'    => $this->input->post('accountName'),
					'ACCOUNT_NUMBER'  => $this->input->post('accountNumber'),
					'ACCOUNT_BANK'    => $this->input->post('bankName'),
					'PAYMENT_AMOUNT'  => $this->input->post('paymentAmount'),
					'PAYMENT_DATE'    => date('Y-m-d H:m:s'),
					'PAYMENT_IMAGE'   => $defaultPath,
					'FLAG'            => '1'
				);

				$query1 = $this->profile->insertImageData($data);
				$query2 = $this->profile->updatePaymentStatus($this->input->post('orderID'));

				if ($query1 && $query2) {
					$this->response([
						'status' 		=> 'ok',
						'message' 		=> 'payment process completed',
						'code' 			=> REST_Controller::HTTP_ACCEPTED,
					], REST_Controller::HTTP_ACCEPTED);
				} else {
					$this->response([
						'status' 		=> 'error',
						'message' 		=> 'payment process not completed',
						'code' 			=> REST_Controller::HTTP_BAD_GATEWAY,
					], REST_Controller::HTTP_BAD_GATEWAY);
				}

				// echo $this->db->last_query();

			}
		} else {
			$this->response([
				'status' 		=> 'error',
				'message' 		=> 'invalid order ID',
				'code' 			=> REST_Controller::HTTP_BAD_REQUEST
			], REST_Controller::HTTP_BAD_REQUEST);
		}
	}

	//TRANSACTION STATUS 
	public function transaction_get()
	{

		$email 		= $this->input->get('email');
		$status 	= $this->input->get('status_order');
		$counter 	= 0;

		if ($status != null) {
			$transData 	= $this->profile->getOrderMasterData($email, $status);
		} else {
			$transData 	= $this->profile->getAllOrderMasterData($email);
		}

		$transArr 	= array();
		$masterArr  = array();

		if ($transData->num_rows() > 0) {

			foreach ($transData->result() as $data) {

				$masterData = $this->profile->getOrderHistory($email);

				foreach ($masterData->result() as $master) {

					if ($master->PRODUCT_NAME != null) {
						$productName 	= $master->PRODUCT_NAME;
						$productImage 	= $master->PRODUCT_IMAGE;
					} else {
						$productName 	= null;
						$productImage 	= null;
					}

					$masterArr[$counter] = array(
						'order_no'			=> $master->ORDER_NO,
						'product_id' 		=> $master->PRODUCT_ID,
						'product_name'		=> $productName,
						'product_image'		=> $productImage,
						'product_quantity' 	=> $master->PRODUCT_QUANTITY,
						'product_price' 	=> $master->PRODUCT_PRICE,
						'product_notes' 	=> $master->PRODUCT_NOTES,
						'product_postage' 	=> $master->PRODUCT_POSTAGE,
						'buyer_email' 		=> $master->MEMBER_EMAIL,
						'buyer_phone' 		=> $master->MEMBER_PHONE,
						'buyer_name' 		=> $master->MEMBER_NAME,
						'buyer_id'			=> $master->ORDER_ID,
					);

					$masterDecode = json_encode($masterArr);
				}

				$transArr[$counter] = array(
					'first_name' 	=> $data->FIRST_NAME,
					'last_name'  	=> $data->LAST_NAME,
					'order_no' 	 	=> $data->ORDER_NO,
					'order_data' 	=> $data->ORDER_DATE,
					'total_order' 	=> $data->AMOUNT,
					'total_postage'	=> $data->TOTAL_POSTAGE,
					'status_order' 	=> $data->STATUS_ORDER,
					'order_updated' => $data->UPDATED,
					'order_details' => json_decode($masterDecode),
				);

				$counter++;
			}

			$transDecode = json_encode($transArr);

			$this->response([
				'status' 		=> 'ok',
				'message' 		=> $transData->num_rows() . ' transaction found',
				'code' 			=> REST_Controller::HTTP_ACCEPTED,
				'transaction' 	=> json_decode($transDecode)
			], REST_Controller::HTTP_ACCEPTED);
		} else {

			$this->response([
				'status' 		=> 'ok',
				'message' 		=> 'no transaction found',
				'code' 			=> REST_Controller::HTTP_ACCEPTED,
				'transaction' 	=> '0'
			], REST_Controller::HTTP_ACCEPTED);
		}
	}

	//DEBUG GET ALL MEMBER
	public function register_get()
	{

		$data = $this->api->getMembers()->result();
		$count = $this->api->getMembers()->num_rows();

		$this->response([
			'status' => 'success',
			'total' => $count,
			'message' => $data
		], REST_Controller::HTTP_ACCEPTED);
	}

	//REGISTER USER
	public function register_post()
	{

		/* POST API PARAMETER */
		$fName 		= $this->input->post('firstName');
		$lName 		= $this->input->post('lastName');
		$phone 		= $this->input->post('phone');
		$email 		= $this->input->post('email');
		$country 	= $this->input->post('country');
		$address1 	= $this->input->post('address1');
		$address2 	= $this->input->post('address2');
		$password   = $this->input->post('password');
		$zip 		= $this->input->post('zip');
		/* FORMAT BUAT TANGGAL HARUS STRING DD/MM/YYYY */
		$date 		= $this->input->post('birthdate');
		$state 		= $this->input->post('province');
		$type 		= $this->input->post('type');

		//CHECK IF THE EMAIL IS ALREADY REGISTERED OR NOT
		$checkEmail = $this->api->checkExistingEmail($email);

		if ($checkEmail->num_rows() > 0) {

			$this->response([
				'status' 		=> 'error',
				'message' 		=> 'user already exists',
				'code' 			=> REST_Controller::HTTP_BAD_REQUEST
			], REST_Controller::HTTP_BAD_REQUEST);
		} else if ($fName == null || $lName == null || $phone == null || $email == null || $country == null || $address1 == null || $address2 == null || $password == null || $state == null || $zip == null) {
			$this->response([
				'status' 		=> 'error',
				'message' 		=> 'cannot use empty value',
				'code' 			=> REST_Controller::HTTP_BAD_REQUEST
			], REST_Controller::HTTP_BAD_REQUEST);
		} else {

			$hashPassword = sha1($password);
			$hashEmail = sha1($email);

			$formatDate = strtotime($date);

			$data = array(
				'FIRST_NAME' 	=> $fName,
				'LAST_NAME' 	=> $lName,
				'JOIN_DATE' 	=> date("Y/m/d h:i:s"),
				'BIRTH_DATE' 	=> date('Y-m-d', $formatDate),
				'PHONE' 		=> $phone,
				'ADDRESS' 		=> $address1,
				'ADDRESS_2' 	=> $address2,
				'COUNTRY' 		=> $country,
				'PROVINCE' 		=> $state,
				'ZIP' 			=> $zip,
				'EMAIL' 		=> $email,
				'PASSWORD' 		=> $hashPassword,
				'STATUS' 		=> 'PENDING',
				'HASH' 			=> $hashEmail,
				'MEMBER_TYPE'	=> $type
			);

			$query = $this->api->insertMember($data);

			if (!$query) {

				$data['email'] = $email;
				$data['hash'] = sha1($email);

				//Disable this for debug only
				//$this->load->view('email-template/verification-email', $data);
				$config['smtp_user']   = 'admin@kikikuku.com';
				$config['smtp_pass']   = 'nOX-D8NlrF#Z';
				$config['smtp_port']   = 25;
				$config['charset']     = 'utf-8';
				$config['wordwrap']    = TRUE;
				$config['mailtype']    = 'html';

				$this->email->initialize($config);

				$this->email->from('admin@kikikuku.com', 'Kikikuku Team');
				$this->email->to($email);
				$this->email->set_mailtype('html');

				$message = $this->load->view('email-template/verification-email', $data, true);

				$this->email->subject('Please confirm your email address');
				$this->email->message($message);

				$this->email->send();

				$this->response([
					'status' 	=> 'success',
					'message' 	=> 'registration successful',
					'code' 		=> REST_Controller::HTTP_ACCEPTED
				], REST_Controller::HTTP_ACCEPTED);
			} else {
				$this->response([
					'status' 	=> 'error',
					'message' 	=> 'bad request',
					'code' 		=> REST_Controller::HTTP_BAD_REQUEST
				], REST_Controller::HTTP_BAD_REQUEST);
			}
		}
	}

	public function login_post()
	{

		//HASHING DI BACKEND
		$email = trim($this->input->post('email'));
		$password = $this->input->post('password');

		//CHECK IF EMAIL IS USED OR NOT
		$emailQuery = $this->api->checkExistingEmail($email);
		$emptyArray = array();

		//Check if email exists
		if ($emailQuery->num_rows() == 0) {

			$this->response([
				'status' 	=> 'error',
				'message' 	=> "this email doesn't exist",
				'code' 		=> REST_Controller::HTTP_BAD_REQUEST,
				'user_data' => new \stdClass(),
			], REST_Controller::HTTP_BAD_REQUEST);
		} else {

			//Check if password is correct
			$checkPassword = $this->user->checkPassword($email, $password);

			if ($checkPassword->num_rows() == 0) {
				//Wrong Password
				$this->response([
					'status' 	=> 'error',
					'message' 	=> "wrong password",
					'code' 		=> REST_Controller::HTTP_BAD_REQUEST,
					'user_data' => new \stdClass(),
				], REST_Controller::HTTP_BAD_REQUEST);
			} else {

				$checkVerified = $this->user->checkVerified($email);

				if ($checkVerified->num_rows() == 0) {

					$this->response([
						'status' 	=> 'error',
						'message' 	=> "this account is not verified",
						'code' 		=> REST_Controller::HTTP_BAD_REQUEST,
						'user_data' => new \stdClass(),
					], REST_Controller::HTTP_BAD_REQUEST);
				} else {
					foreach ($checkPassword->result() as $data) {

						$dataSess = array(
							'FIRST_NAME' => $data->FIRST_NAME,
							'LAST_NAME' => $data->LAST_NAME,
							'PHONE' => $data->PHONE,
							'EMAIL' => $data->EMAIL,
							'ADDRESS' => $data->ADDRESS,
							'ADDRESS_2'	=> $data->ADDRESS_2,
							'COUNTRY' => $data->COUNTRY,
							'PROVINCE' => $data->PROVINCE,
							'USERID' => $data->ID,
							'ZIP' => $data->ZIP,
							'LOGGED_IN' => TRUE,
							'TYPE'	=> $data->MEMBER_TYPE
						);
					}

					$this->response([
						'status' 	=> 'ok',
						'message' 	=> "user found",
						'code' 		=> REST_Controller::HTTP_ACCEPTED,
						'user_data' => $dataSess
					], REST_Controller::HTTP_ACCEPTED);
				}
			}
		}
	}

	//Add item to cart
	public function cart_post()
	{
		$finalUrl   = file_get_contents('http://en.yiwugo.com/ywg/productdetail.html?account=Wien.suh@gmail.com&productId=' . $this->input->post('product-id'));
		$obj 		= json_decode($finalUrl, true);

		$totalQty = 0;
		$hashID = sha1($this->input->post('product-buyer'));

		$itemArray = array(
			'CART_ID' 			=> $hashID,
			'PRODUCT_ID' 		=> $this->input->post('product-id'),
			'PRODUCT_QUANTITY' 	=> $this->input->post('quantity'),
			'PRODUCT_PRICE' 	=> $this->input->post('product-price'),
			'PRODUCT_NAME' 		=> $this->input->post('product-name'),
			'PRODUCT_NOTES' 	=> $this->input->post('customer-notes'),
			'PRODUCT_IMAGES' 	=> $this->input->post('product-images'),
			'PRODUCT_BUYER' 	=> $this->input->post('product-buyer')
		);

		if ($this->input->post('product-name') == null) {
			$this->response([
				'status' 	=> 'error',
				'message' 	=> 'product name cannot be empty',
				'code' 		=> REST_Controller::HTTP_BAD_REQUEST
			], REST_Controller::HTTP_BAD_REQUEST);
		} else if ($this->input->post('quantity') == null) {
			$this->response([
				'status' 	=> 'error',
				'message' 	=> 'quantity cannot be empty',
				'code' 		=> REST_Controller::HTTP_BAD_REQUEST
			], REST_Controller::HTTP_BAD_REQUEST);
		} else if ($this->input->post('product-id') == null) {
			$this->response([
				'status' 	=> 'error',
				'message' 	=> 'product id cannot be empty',
				'code' 		=> REST_Controller::HTTP_BAD_REQUEST
			], REST_Controller::HTTP_BAD_REQUEST);
		} else if ($this->input->post('quantity') <= 0) {
			$this->response([
				'status' 	=> 'error',
				'message' 	=> 'quantity cannot be 0 or lower',
				'code' 		=> REST_Controller::HTTP_BAD_REQUEST
			], REST_Controller::HTTP_BAD_REQUEST);
		} else if ($this->input->post('product-price') == null) {
			$this->response([
				'status' 	=> 'error',
				'message' 	=> 'price cannot be empty',
				'code' 		=> REST_Controller::HTTP_BAD_REQUEST
			], REST_Controller::HTTP_BAD_REQUEST);
		} else if ($this->input->post('product-price') <= 0) {
			$this->response([
				'status' 	=> 'error',
				'message' 	=> 'price cannot be 0 or lower',
				'code' 		=> REST_Controller::HTTP_BAD_REQUEST
			], REST_Controller::HTTP_BAD_REQUEST);
		} else if ($this->input->post('product-buyer') == null) {
			$this->response([
				'status' 	=> 'error',
				'message' 	=> 'product buyer cannot be empty',
				'code' 		=> REST_Controller::HTTP_BAD_REQUEST
			], REST_Controller::HTTP_BAD_REQUEST);
		} else if ($obj == null) {
			$this->response([
				'status' 	=> 'error',
				'message' 	=> 'invalid product id',
				'code' 		=> REST_Controller::HTTP_BAD_REQUEST
			], REST_Controller::HTTP_BAD_REQUEST);
		} else {

			//CHECK IF THE ITEM IS EXIST OR NOT
			$itemResult = $this->carts->getItemInfo($this->input->post('product-id'), $this->input->post('product-buyer'));

			if ($itemResult->num_rows() > 0) {

				//DO SOMETHING ELSE
				$currentQty = $itemResult->result()[0]->PRODUCT_QUANTITY;
				$totalQty = $currentQty + $this->input->post('quantity');

				$dataArray = array(
					'PRODUCT_QUANTITY' => $totalQty
				);

				// UPDATE QUANTITY
				$this->carts->updateCartQuantity($dataArray, $this->input->post('product-buyer'), $this->input->post('product-id'));

				$this->response([
					'status' 	=> 'ok',
					'message' 	=> 'cart quantity updated',
					'code' 		=> REST_Controller::HTTP_ACCEPTED
				], REST_Controller::HTTP_ACCEPTED);
			} else {
				$this->carts->insertCartData($itemArray);

				$this->response([
					'status' 	=> 'ok',
					'message' 	=> 'cart inserted',
					'code' 		=> REST_Controller::HTTP_ACCEPTED
				], REST_Controller::HTTP_ACCEPTED);
			}
		}
	}

	//Delete the items
	public function deletecart_post()
	{

		$hashTrans 	= sha1($this->input->post('product-buyer'));
		$productID	= $this->input->post('product-id');

		$queryDelete = $this->carts->deleteCartsAPI($hashTrans, $productID);

		if ($queryDelete) {
			$this->response([
				'status' 	=> 'ok',
				'message' 	=> 'item deleted',
				'code' 		=> REST_Controller::HTTP_OK,
			], REST_Controller::HTTP_OK);
		} else {
			$this->response([
				'status' 	=> 'error',
				'message' 	=> 'item cannot be deleted',
				'code' 		=> REST_Controller::HTTP_BAD_REQUEST,
			], REST_Controller::HTTP_BAD_REQUEST);
		}
	}

	//Update the cart content
	public function updatecart_post()
	{

		$productID 		= $this->input->post('product-id');
		$productBuyer 	= $this->input->post('product-buyer');
		$newQty			= $this->input->post('product-quantity');
		$newNotes		= $this->input->post('product-notes');

		$updateArray = array(
			'PRODUCT_QUANTITY'	=> $newQty,
			'PRODUCT_NOTES'		=> $newNotes
		);

		$updateQuery = $this->carts->updateCartContents($productID, $productBuyer, $updateArray);

		if ($updateQuery) {
			$this->response([
				'status' 	=> 'ok',
				'message' 	=> 'item updated',
				'code' 		=> REST_Controller::HTTP_ACCEPTED,
			], REST_Controller::HTTP_ACCEPTED);
		} else {
			$this->response([
				'status' 	=> 'error',
				'message' 	=> 'item not updated',
				'code' 		=> REST_Controller::HTTP_BAD_REQUEST,
			], REST_Controller::HTTP_BAD_REQUEST);
		}
	}

	//Get the cart content based from email and trans date
	public function cart_get()
	{

		$data = array();

		$hashTrans = sha1($this->input->get('email'));

		$result = $this->carts->displayCart($hashTrans);

		$count = $result->result_array();

		for ($i = 0; $i < count($count); $i++) {

			$finalUrl   = file_get_contents('http://en.yiwugo.com/ywg/productdetail.html?account=Wien.suh@gmail.com&productId=' . $count[$i]['PRODUCT_ID']);
			$obj 		= json_decode($finalUrl, true);

			$data[$i]['prod_id'] 		= $count[$i]['PRODUCT_ID'];
			$data[$i]['quantity'] 		= $count[$i]['PRODUCT_QUANTITY'];
			$data[$i]['prod_image'] 	= $count[$i]['PRODUCT_IMAGES'];
			$data[$i]['price'] 			= $count[$i]['PRODUCT_PRICE'];
			$data[$i]['prod_name'] 		= $count[$i]['PRODUCT_NAME'];
			$data[$i]['notes'] 			= $count[$i]['PRODUCT_NOTES'];
			$data[$i]['email'] 			= $count[$i]['PRODUCT_BUYER'];
			$data[$i]['weight']			= $obj['detail']['productForApp']['weightetc'];

			$flag = $count[$i]['CART_FLAG'];
		}

		$json = json_encode($data);

		if (count($count) > 0) {
			$this->response([
				'status' 	=> 'ok',
				'message' 	=> $result->num_rows() . ' cart item found',
				'cart_id'	=> $hashTrans,
				'cart_flag' => $flag,
				'code' 		=> REST_Controller::HTTP_ACCEPTED,
				'item'      => json_decode($json, true),
			], REST_Controller::HTTP_ACCEPTED);
		} else {
			$this->response([
				'status' 	=> 'ok',
				'message' 	=> 'no cart history found',
				'code' 		=> REST_Controller::HTTP_OK,
				'item'      => json_decode($json, true),
			], REST_Controller::HTTP_OK);
		}
	}

	//Get category
	public function category_get()
	{

		$childArray 	= array();
		$parentArray	= array();
		$parent 		= $this->category->getParentCategory();

		foreach ($parent->result() as $parents) {

			$parentArray[] = array(
				'NAME' 			=> $parents->NAME,
				'ID'			=> $parents->ID,
				'PARENT'		=> $parents->PARENT,
				'PICTURE'		=> $parents->PICTURE
			);
		}

		for ($i = 0; $i < count($parentArray); $i++) {

			$child 		= $this->category->getChildCategory($parentArray[$i]['ID']);

			foreach ($child->result() as $child) {

				$childArray[] = array(
					'CHILD_NAME'	=> $child->NAME,
					'CHILD_ID'	=> $child->LINK
				);
			}

			array_push($parentArray[$i], $childArray);
		}

		$json = json_encode($parentArray);

		$this->response([
			'status' 		=> 'ok',
			'message' 		=> 'category list',
			'code' 			=> REST_Controller::HTTP_OK,
			'category_list'	=> json_decode($json, true)
		], REST_Controller::HTTP_OK);
	}

	//Create Inquiry
	public function inquiry_post()
	{

		$json = file_get_contents('php://input');
		$parse = json_decode($json);

		$inquiryData = array();
		$itemData	 = array();
		$counter     = 1;
		$genID       = $this->carts->generateID();

		$emailQuery = $this->api->checkExistingEmail($parse->member_email);

		if ($emailQuery->num_rows() == 0) {
			$this->response([
				'status' 		=> 'ok',
				'message' 		=> "invalid user email",
				'code' 			=> REST_Controller::HTTP_BAD_REQUEST,
			], REST_Controller::HTTP_BAD_REQUEST);
		}

		if ($parse->member_id == null || $parse->total_price == null || $parse->member_name == null || $parse->member_email == null || $parse->member_phone == null || $parse->member_address_1 == null || $parse->member_address_2 == null || $parse->member_country == null || $parse->member_zip == null || $parse->member_state == null) {

			$this->response([
				'status' 		=> 'error',
				'message' 		=> "data cannot be empty",
				'code' 			=> REST_Controller::HTTP_BAD_REQUEST,
			], REST_Controller::HTTP_BAD_REQUEST);
		} else {

			$inquiryData['ORDER_NO'] 		 = $genID;
			$inquiryData['ORDER_DATE'] 		 = date('Y-m-d h:i:s');
			$inquiryData['MEMBER_ID'] 		 = $parse->member_id;
			$inquiryData['TOTAL_ORDER']  	 = $parse->total_price;
			$inquiryData['MEMBER_NAME'] 	 = $parse->member_name;
			$inquiryData['MEMBER_EMAIL'] 	 = $parse->member_email;
			$inquiryData['MEMBER_PHONE'] 	 = $parse->member_phone;
			$inquiryData['ADDRESS_1'] 		 = $parse->member_address_1;
			$inquiryData['ADDRESS_2'] 	 	 = $parse->member_address_2;
			$inquiryData['COUNTRY'] 	 	 = $parse->member_country;
			$inquiryData['ZIP'] 		 	 = $parse->member_zip;
			$inquiryData['STATE'] 	 		 = $parse->member_state;
			$inquiryData['STATUS'] 	 		 = 'NEW ORDER';
			$inquiryData['UPDATED'] 	 	 = date('Y-m-d h:i:s');
			$inquiryData['SAVE_FLAG'] 	 	 = 0;

			foreach ($parse->items as $item) {

				$itemData['FLAG']			= '1';
				$itemData['ORDER_NO']		= $genID;
				$itemData['PROD_NAME']		= $item->prod_name;
				$itemData['PROD_IMAGE']		= $item->prod_image;
				$itemData['PROD_ID']		= $item->prod_id;
				$itemData['QUANTITY']		= $item->quantity;
				$itemData['WEIGHT']			= $item->weight;
				$itemData['PRICE']			= $item->price;
				$itemData['FINAL_PRICE']	= $item->final_price;
				$itemData['POSTAGE']		= '0';
				$itemData['NOTES']			= $item->notes;

				$hashTrans = sha1($parse->member_email);
				$productID = $item->prod_id;

				$this->carts->updateCartFlag($productID, $hashTrans);
				$this->carts->insertOrderDetail($itemData);

				$counter++;
			}
		}

		$emailQuery = $this->api->checkExistingEmail($inquiryData['MEMBER_EMAIL']);

		if ($emailQuery->num_rows() > 0) {

			$json  = json_encode($inquiryData);
			$items = json_encode($itemData);

			$query1 = $this->carts->insertMasterData($inquiryData);

			if ($query1) {
				$this->response([
					'status' 		=> 'ok',
					'message' 		=> "data inserted",
					'generated_id'	=> $genID,
					'code' 			=> REST_Controller::HTTP_ACCEPTED,
				], REST_Controller::HTTP_ACCEPTED);
			} else {

				$this->response([
					'status' 		=> 'error',
					'message' 		=> "data not inserted",
					'code' 			=> REST_Controller::HTTP_BAD_REQUEST,
				], REST_Controller::HTTP_BAD_REQUEST);
			}
		} else {

			$this->response([
				'status' 		=> 'error',
				'message' 		=> "email does not exist",
				'code' 			=> REST_Controller::HTTP_BAD_REQUEST,
			], REST_Controller::HTTP_BAD_REQUEST);
		}
	}

	//Get Margin Parameter
	public function margin_get()
	{

		$margin 	= $this->api->getMarginParam();
		$convert 	= $this->api->getConvertRate();

		$this->response([
			'status' 		=> 'ok',
			'message' 		=> "current margin value",
			'code' 			=> REST_Controller::HTTP_ACCEPTED,
			'margin_param'	=> $margin,
			'convert_rate'  => $convert,
		], REST_Controller::HTTP_ACCEPTED);
	}

	//Get all user inquiry
	public function inquiry_get()
	{
	}

	//Verified Account
	public function verification_get()
	{

		$hash = $this->input->get('hash');
		$email = $this->input->get('email');

		$query = $this->api->verifyMember($hash);

		if ($query->num_rows() > 0) {

			$update = $this->api->updateStatus($email);

			if ($update) {
				//Account Verified
				$this->response([
					'status' 	=> 'success',
					'message' 	=> 'account verified',
					'code' 		=> REST_Controller::HTTP_ACCEPTED
				], REST_Controller::HTTP_ACCEPTED);
			} else {
				//Verification Error
				$this->response([
					'status' 	=> 'error',
					'message' 	=> 'bad request',
					'code' 		=> REST_Controller::HTTP_BAD_REQUEST
				], REST_Controller::HTTP_BAD_REQUEST);
			}
		} else {
			//Account already verified
			$this->response([
				'status' 	=> 'verified',
				'message' 	=> 'this account is already verified',
				'code' 		=> REST_Controller::HTTP_ACCEPTED
			], REST_Controller::HTTP_ACCEPTED);
		}
	}
}

<?php if(!defined("BASEPATH")) exit("Hack Attempt");
class Profile extends CI_Controller {

		public function __construct() {
			parent::__construct();
			// $this->output->enable_profiler(TRUE);
			$this->load->library('session');
			$this->load->helper('form');
			$this->load->model('M_profile', 'profile');
			date_default_timezone_set('Asia/Jakarta');
		}

		public function transaction() {

			$loginStatus = $this->session->userdata('LOGGED_IN');
			$userEmail   = $this->session->userdata('EMAIL');

			if($loginStatus == false) {
				redirect(base_url('login?error=4'));
			}

			if($this->input->get('transaction') == null) {
				$data['masterData'] = $this->profile->getAllOrderMasterData($userEmail);
			} else {
				$status = $this->input->get('transaction');
				$data['masterData'] = $this->profile->getOrderMasterData($userEmail, $status);
			}

			$data['userHistory'] = $this->profile->getOrderHistory($userEmail);
			$data['userEmail'] = $this->session->userdata('EMAIL');

			$this->load->view('templates/header');
			$this->load->view('templates/navbar');
	    	$this->load->view('pages/profile/transaction', $data);
	    	$this->load->view('templates/footer');

		}

		public function myprofile() {

			$loginStatus = $this->session->userdata('LOGGED_IN');
			$userEmail   = $this->session->userdata('EMAIL');

			if($loginStatus == false) {
				redirect(base_url('login?error=4'));
			}

			$data['memberDetails'] = $this->profile->getMemberDetails($userEmail);

			$this->load->view('templates/header');
			$this->load->view('templates/navbar');
	    	$this->load->view('pages/profile/profile', $data);
	    	$this->load->view('templates/footer');

		}

		public function getMessages() {

			$orderID = $this->input->get('id');

			$data['message'] = $this->profile->getOrderMessages($orderID);
			$data['transID'] = $orderID;

			$this->load->view('pages/modal/modal-message', $data);

		}

		public function changePassword() {

			$id = $this->input->get('id');
			
			$loginStatus = $this->session->userdata('LOGGED_IN');
			$userEmail   = $this->session->userdata('EMAIL');

			if($loginStatus == false) {
				redirect(base_url('login?error=4'));
			}

			$data['memberDetails'] = $this->profile->getMemberDetails($userEmail);

 			$this->load->view('pages/modal/modal-password', $data);

		}

		public function updatePassword() {

			$this->load->helper('form');
			$this->load->library('upload');

			$this->load->model('M_profile', 'cms');

			$id = $this->input->post('id');
			$password = $this->input->post('new_password');
			
			$hashPassword = sha1($password);

			$this->cms->updatePassword($id, $hashPassword);

			redirect('profile/myprofile');
			
		}

		public function changePhone() {

			$id = $this->input->get('id');
			
			$loginStatus = $this->session->userdata('LOGGED_IN');
			$userEmail   = $this->session->userdata('EMAIL');

			if($loginStatus == false) {
				redirect(base_url('login?error=4'));
			}

			$data['memberDetails'] = $this->profile->getMemberDetails($userEmail);

 			$this->load->view('pages/modal/modal-phone', $data);

		}

		public function updatePhone(){

			// $this->output->enable_profiler(TRUE);
			// echo "masuk";
			$this->load->model('M_profile', 'cms');

			$id = $this->input->post('id');
			$phone = $this->input->post('phone');

			$this->cms->updatePhone($id, $phone);

			redirect('profile/myprofile');
		}

		public function changePhoto() {

			$id = $this->input->get('id');
			
			$loginStatus = $this->session->userdata('LOGGED_IN');
			$userEmail   = $this->session->userdata('EMAIL');

			if($loginStatus == false) {
				redirect(base_url('login?error=4'));
			}

			$data['memberDetails'] = $this->profile->getMemberDetails($userEmail);

 			$this->load->view('pages/modal/modal-photo', $data);

		}

		public function updatePhoto() {

			// $this->output->enable_profiler(TRUE);

		   	$this->load->helper('form');
			$this->load->library('upload');

			$this->load->model('M_profile', 'cms');

			$defaultPath = '/assets/images/member-img/'.$_FILES['file_name']['name'];
			
			$id = $this->input->post('id');
			$file  = $defaultPath;

			$this->cms->updatePhoto($id, $defaultPath);

	    	$config['upload_path']   = './assets/images/member-img/';
	    	$config['allowed_types'] = 'jpeg|jpg|png';

			$this->upload->initialize($config);

			if ( !$this->upload->do_upload('file_name')) {
				echo $this->upload->display_errors();
			} else {
					$this->upload->data();
					redirect('profile/myprofile');
					// $this->set('showModal',true);
		   	}

		}

		public function changeAddress() {

			$id = $this->input->get('id');
			
			$loginStatus = $this->session->userdata('LOGGED_IN');
			$userEmail   = $this->session->userdata('EMAIL');

			if($loginStatus == false) {
				redirect(base_url('login?error=4'));
			}

			$data['memberDetails'] = $this->profile->getMemberDetails($userEmail);

 			$this->load->view('pages/modal/modal-address', $data);

		}

		public function updateAddress(){

			// $this->output->enable_profiler(TRUE);
			// echo "masuk";
			$this->load->model('M_profile', 'cms');

			$id = $this->input->post('id');
			$add1 = $this->input->post('add1');
			$add2 = $this->input->post('add2');
			$country = $this->input->post('country');
			$province = $this->input->post('province');
			$zip = $this->input->post('zip');

			$this->cms->updateAddress($id, $add1, $add2, $country, $province, $zip);

			redirect('profile/myprofile');
		}

		public function customerSendMessages() {

			$customerID 	= $this->input->get('sender');
			$transactionID 	= $this->input->get('id');
			$message 		= $this->input->get('message');

			$data = array(
        		'SENDER_ID' 		=> 'CUSTOMER',
        		'ORDER_ID' 			=> $transactionID,
				'MESSAGE' 			=> $message,
				'MESSAGE_TIME' 		=> date('Y-m-d H:m:s'),
				'USER_READ_FLAG' 	=> '0',
				'ADMIN_READ_FLAG' 	=> '1'
			);

			$this->profile->sendMessages($data);

		}

		public function orderPayment() {

			$data['orderID'] 		= $this->input->post('orderID');
			$data['orderTotal'] 	= $this->input->post('orderTotal');
			$data['transactionID'] 	= $this->input->post('transactionID');

			$this->load->view('templates/header');
			$this->load->view('templates/navbar');
		    $this->load->view('pages/profile/payment', $data);
		    $this->load->view('templates/footer');

		}

		public function manualVerification() {

			$data['orderID'] 		= $this->input->post('orderID');
			$data['orderTotal'] = $this->input->post('orderTotal');
			$data['transactionID'] = $this->input->post('transactionID');

			$this->load->view('templates/header');
			$this->load->view('templates/navbar');
	    $this->load->view('pages/profile/order_confirmation', $data);
	    $this->load->view('templates/footer');

		}

		public function finishOrder() {

			$orderID = $this->input->post('id');

			if($this->profile->finishOrder($orderID)) {
				echo 'order closed';
			} else {
				echo 'something is wrong';
			}

		}

		public function history() {

			//GET THE PARAMETER FOR QUERY
			$searchQuery = $this->input->get('query');
			$emailQuery  = $this->input->get('id');
			$userEmail	 = $this->session->userdata('EMAIL');

			//SET EACH PARAMETER TO MATCH THE DATABASE
			if($searchQuery == 'created') {
					$searchQuery = 'NEW ORDER';
			} else {
				strtolower($searchQuery);
			}

			$data['userHistory'] = $this->profile->getOrderHistoryFromQuery($emailQuery, $searchQuery);
			$data['masterData'] = $this->profile->getOrderMasterDataFromQuery($emailQuery, $searchQuery);
			$data['userEmail'] = $this->session->userdata('EMAIL');

			$this->load->view('templates/header');
			$this->load->view('templates/navbar');
			$this->load->view('pages/profile/transaction', $data);
			$this->load->view('templates/footer');

		}

}

<?php if(!defined("BASEPATH")) exit("Hack Attempt");
	
	class ContentLoad extends CI_Controller {

		public function __construct() {
			parent::__construct();

				//$this->output->enable_profiler(TRUE);
				// $this->load->model('M_pages', 'pages');
				// $this->load->model('M_home', 'home');
				// $this->load->model('M_product', 'product');
				date_default_timezone_set('Asia/Jakarta');
		}

		public function autoloadHome() {

			$output = '';
			$randomPage = mt_rand(1, 500);

			$json = file_get_contents("https://en.yiwugo.com/ywg/productlist.html?account=Wien.suh@gmail.com&pageSize=10&cpage=".$randomPage."");
			$obj = json_decode($json, true);

			//COUNTER TO DISPLAY MANUAL LOAD
			$currentCounter = $this->input->post('counter');

			foreach($obj['prslist'] as $list) {

				//BROKEN IMAGE LINK FIX
				if(substr($list['picture2'], 1, 1) != 'i' && substr($list['picture2'], 4, 1) != '/') {					
					$newPath = 'http://img1.yiwugou.com/i000';
				} else {
	    		  	$newPath = 'http://img1.yiwugou.com/';
				}

				//FILL THE TEMPLATE WITH OUTPUT DATA
				if($list['sellPrice'] == 0) {
						$output .= '
						<div class="custom-product-list" >
							<div class="card product-list" id="prod_'.$list['id'].'">
								<a href="'.base_url('product_detail?id='.$list['id']).'" style="text-decoration: none;">
									<div class="d-flex justify-content-center">
										<img alt="'.$list['title'].'" class="product-image" src="'.$newPath.$list['picture2'].'" />
									</div>
									<p class="product-title mt-2">'.ucwords(mb_strimwidth($list['title'], 0, 35, "...")).'</p>
									<label class="product-label">Estimated Price</label></br>
									<span class="product-price">Price Negotiable</span>	
								</a>
							</div>
						</div>';
				} else if($list['sellPrice'] > 9999999) {
						$output .= '
						<div class="custom-product-list" >
							<div class="card product-list" id="prod_'.$list['id'].'">
								<a href="'.base_url('product_detail?id='.$list['id']).'" style="text-decoration: none;">
									<div class="d-flex justify-content-center">
										<img alt="'.$list['title'].'" class="product-image" src="'.$newPath.$list['picture2'].'" />
									</div>
									<p class="product-title mt-2">'.ucwords(mb_strimwidth($list['title'], 0, 35, "...")).'</p>
									<label class="product-label">Estimated Price</label></br>
									<span class="product-price">Price Negotiable</span>	
								</a>
							</div>
						</div>';
				} else {
					$output .= '
					<div class="custom-product-list" >
						<div class="card product-list" id="prod_'.$list['id'].'">
							<a href="'.base_url('product_detail?id='.$list['id']).'" style="text-decoration: none;">
								<div class="d-flex justify-content-center">
									<img alt="'.$list['title'].'" class="product-image" src="'.$newPath.$list['picture2'].'" />
								</div>
								<p class="product-title mt-2">'.ucwords(mb_strimwidth($list['title'], 0, 35, "...")).'</p>
								<label class="product-label">Estimated Price</label></br>
								<span class="product-price">IDR '.number_format($list['sellPrice']).'</span>	
							</a>
						</div>
					</div>';
				}
			}

			if($currentCounter >= 4) {
				$output .= '<button onclick="manualLoad()" class="load-more-content" type="button" >Load More</button>';
			}
			
			//ECHO THE FINAL GROUP OF OUTPUT
			echo $output;

		}



	}
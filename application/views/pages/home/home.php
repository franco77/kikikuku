<div class="full-container" style="margin-top: 7.5em;">

	<!-- DESKTOP BANNER (NO CAROUSEL) -->
	<div class="banner-container header-desktop">
		<div class="row" style="width: 100%; margin-left: 0!important; margin-right: 0!important;">

			<!-- LEFT PART -->
			<div class="col-10 col-md-10 col-lg-10 col-xl-10">
				<img alt="Main Banner" src="<?php echo base_url('assets/images/banner-03.jpg');?>" style="width: 100%;" />
			</div>

			<!-- RIGHT PART -->
			<div class="col-2 col-md-2 col-lg-2 col-xl-2">

				<!-- UPPER IMAGE -->
				<div class="row" style="padding-top: 0!important; padding-bottom: 0!important;">
					<div class="col-12" style="padding-left: 0!important;">
						<img alt="Side Banner" src="<?php echo base_url('assets/images/banner-01.jpg');?>" style="width: 100%;" />
					</div>
				</div>
				<!-- LOWER IMAGE -->
				<div class="row" style="padding-top: 0.5em!important;">
					<div class="col-12" style="padding-left: 0!important;">
						<img alt="Side Banner" src="<?php echo base_url('assets/images/banner-01.jpg');?>" style="width: 100%;" />
					</div>
				</div>
			</div>

		</div>
	</div>


	<!-- MOBILE BANNER (CAROUSEL) -->
	<div class="banner-container header-mobile">
		<div class="row" style="width: 100%; margin-left: 0!important; margin-right: 0!important;">
			<div class="col-12 no-gutters no-padding-x">
				<div id="carouselBanner" class="carousel slide" data-ride="carousel" data-touch="true">
					<div class="carousel-inner">
						<div class="carousel-item active">
							<img alt="Side Banner" class="d-block w-100" src="<?php echo base_url('assets/images/banner-01.jpg');?>" style="width: 100%;" />
						</div>
						<div class="carousel-item">
							<img alt="Main Banner" class="d-block w-100" src="<?php echo base_url('assets/images/banner-03.jpg');?>" style="width: 100%;" />
						</div>
						<div class="carousel-item">
							<img alt="Main Banner" class="d-block w-100" src="<?php echo base_url('assets/images/banner-03.jpg');?>" style="width: 100%;" />
						</div>
					</div>
					<a class="carousel-control-prev" href="#carouselBanner" role="button" data-slide="prev">
						<span class="carousel-control-prev-icon" aria-hidden="true"></span>
					    <span class="sr-only">Previous</span>
					</a>
					<a class="carousel-control-next" href="#carouselBanner" role="button" data-slide="next">
						<span class="carousel-control-next-icon" aria-hidden="true"></span>
					    <span class="sr-only">Next</span>
					</a>
				</div>
			</div>
		</div>
	</div>

</div>

<div class="main-container">

	<div class="row" style="width: 100%; margin-left: 0!important; margin-right: 0!important;">

		<!-- COMPONENT LEFT PART -->
		<div class="custom-column-left header-desktop">
			<div class="accordion" id="main-accordion">
			<?php foreach($categories->result() as $data): ?>
			<div class="card">
				<div class="card-header" data-toggle="collapse" data-target="#collapse-<?php echo $data->ID; ?>" aria-expanded="false" aria-controls="collapse-<?php echo $data->ID; ?>" id="heading-<?php echo $data->ID; ?>" style="padding: 0!important;">
					<p class=" categoryCardContainer">
						<span class="categoryCardTitle">
							<label style="font-size: 11px;"><?php echo $data->DESCRIPTION; ?></label>
						</span>
						<span class="float-right caret-container">
							<i class="fas fa-chevron-down"></i>
						</span>
					</p>
				</div>
				<div id="collapse-<?php echo $data->ID; ?>" class="collapse" aria-labelledby="heading-<?php echo $data->ID; ?>" data-parent="#main-accordion">
					<div class="card-body">
						<ul>
							<?php $categoryQuery = $this->M_category->getChildCategory($data->ID); ?>

							<?php foreach($categoryQuery->result() as $list): ?>
								<li class="category-list">
									<a class="text-capitalize" href="<?php echo base_url('home?category='.$data->ID.'&id='.ucfirst(strtolower($list->LINK)) );?>">&nbsp;<?php echo ucfirst(strtolower($list->NAME));?></a>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
			</div>
			<?php endforeach; ?>

			<div class="card">
				<span id="banner-sidebar">
					<img alt="Side Banner" src="<?php echo base_url('assets/images/banner_bike.jpg');?>" style="width: 100%;" />
				</span>
			</div>

			</div>
		</div>

		<!-- COMPONENT RIGHT PART -->
		<div class="custom-column-right">

			<div class="row">
				<div class="col-12">
				<!-- Category Part -->
				<?php if($breadcrumb):?>
					<div class="mb-3 pt-2" style="text-align: left;">
						<span style="color: #333;">
							<a href="<?php echo base_url(); ?>" style="color: black;">
								<span class="fa fa-home"></span> Home
							</a>
						</span>
						<span style="color: #333;"> -
							<?php foreach($mainCategory->result() as $data): ?>
								<?php echo $data->NAME; ?>
							<?php endforeach; ?>
						</span> -
						<span style="color: #333;">
						<?php foreach($subCategory->result() as $data): ?>
							<?php echo $data->NAME; ?>
						<?php endforeach; ?>
						</span>
					</div>
				<?php endif; ?>
				<!-- End of Category Part -->
				</div>
			</div>

			<div class="row" id="product-main-list">

			</div>

			<div class="row" id="loader-icon">

				<div class="col-12 col-md-12 col-lg-12 col-xl-12">
					<div class="d-flex justify-content-center">
						<div class="lds-roller pt-4 pb-5">
							<div></div>
							<div></div>
							<div></div>
							<div></div>
							<div></div>
							<div></div>
							<div></div>
							<div></div>
						</div>
					</div>
				</div>

			</div>

		</div>

	</div>

</div>

<?php if($this->session->userdata('verification') == 'pending'): ?>
<script type="text/javascript">

	swal.fire({
		title:'Registration Successful',
		text: 'Please check your email to verify your account',
		type: 'success',
		showCancelButton: false,
	});

</script>

<?php elseif($this->session->userdata('verification') == 'error'): ?>
<script type="text/javascript">

	swal.fire({
		title:'Registration Unsuccessful',
		text: 'Something wrong with your registration process. Please try again later',
		type: 'warning',
		showCancelButton: false,
	});

</script>
<?php endif; ?>

<?php 
	//VERIFICATION AJAX FUNCTION
	if($this->input->get('key') != null && $this->input->get('email') != null):
?>
<script type="text/javascript">

	var email = "<?php echo $this->input->get('email'); ?>";
	var hash = "<?php echo $this->input->get('key'); ?>";

	$.ajax({
		url: '<?php echo base_url('Register/verification'); ?>',
		type: 'GET',
		data: { email: email, key: hash },
		success: function(data) {
			console.log(data);	
			if(data === 'success') {
				swal.fire({
					title:'Verification Successful',
					text: 'Account Verification Successful. Happy shopping!',
					type: 'success',
					showCancelButton: false,
				});
			} else if(data === 'error') {
				swal.fire({
					title:'Verification Unsuccessful',
					text: 'Something wrong with your registration process. Please try again later',
					type: 'error',
					showCancelButton: false,
				});
			} else if(data === 'verified') {
				swal.fire({
					title:'Verification Unsuccessful',
					text: 'This account is already verified',
					type: 'info',
					showCancelButton: false,
				});
			}
		}, error: function (xhr, ajaxOptions, thrownError) {
			var errorMsg = 'Ajax Failed: ' + xhr.responseText;
			console.log(errorMsg);
		}
	});
</script>

<?php endif; ?>

<?php if($this->session->userdata('inquiry') == 'created'): ?>
<script type="text/javascript">

	swal.fire({
		title:'Inquiry Created',
		text: 'Your inquiry has been created. Our team will process your order',
		type: 'success',
		showCancelButton: false,
	});

</script>
<?php elseif($this->session->userdata('inquiry') == 'failed'): ?>
<script type="text/javascript">

	swal.fire({
		title:'Inquiry Not Created',
		text: 'Your inquiry has not been created. Please try again later.',
		type: 'error',
		showCancelButton: false,
	});

</script>
<?php endif; ?>

<?php if($this->input->get('inquiry') == 'paid'): ?>
<script type="text/javascript">

	swal.fire({
		title:'Payment Complete',
		text: 'Your payment process is completed. Our team will process your payment confirmation',
		type: 'success',
		showCancelButton: false,
	});

</script>
<?php endif; ?>
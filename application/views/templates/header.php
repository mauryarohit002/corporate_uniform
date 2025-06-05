<?php
  $user_id  	= $_SESSION['user_id'];
  $role     	= $_SESSION['user_role'];
  $name     	= $_SESSION['user_fullname'];
  $uname    	= $_SESSION['user_name'];
  $branch   	= $_SESSION['user_branch'];
  $fin_year 	= $_SESSION['fin_year'];
  $start_year   = $_SESSION['start_year'];
  $end_year 	= $_SESSION['end_year'];
  $title		= $this->config->item('title');
  $company_name	= isset($_SESSION['company_name']) ? $_SESSION['company_name'] : $title[1].' '. $title[2];
  $menu_data 	= get_menu_data();
  $bg_color 	= get_bgcolor();
?>
<!DOCTYPE html> 
<html>
	<head>
		<meta charset="utf-8">
	  	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	  	<title><?php echo $company_name; ?></title>
	  	
	  	<!-- Tell the browser to be responsive to screen width -->
	  	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

	  	<!-- Bootstrap 4 -->
	  	<link rel="stylesheet" href="<?php echo assets('plugins/bootstrap/css/bootstrap.min.css')?>">

	  	<!-- Font Awesome -->
	  	<link rel="stylesheet" href="<?php echo assets('plugins/font-awesome/css/font-awesome.min.css')?>">

	  	<!--Toastr-->
	  	<link rel="stylesheet" href="<?php echo assets('plugins/toastr/css/toastr.min.css'); ?>" />

	  	<!-- Date Picker -->
  		<link rel="stylesheet" href="<?php echo assets('plugins/datepicker/css/bootstrap-datepicker.css')?>">

			<!-- Toggle Switch -->
  		<link rel="stylesheet" href="<?php echo assets('plugins/toggle-switch/css/toggle.min.css')?>">

  		<!-- Select2 -->
  		<link rel="stylesheet" href="<?php echo assets('plugins/select2/css/select2.min.css')?>">
  		
  		<!-- Pan -->
  		<link rel="stylesheet" href="<?php echo assets('plugins/pan/css/pan.min.css')?>">

  		<!-- SweetAlert2 -->
  		<link rel="stylesheet" href="<?php echo assets('plugins/sweetalert2/css/sweetalert2.min.css')?>">

		<!-- Google Font -->
		<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
		
		<!-- custom style sheet -->
		<link rel="stylesheet" href="<?php echo assets('dist/css/bootstrap.css?v=16')?>">
		<link rel="stylesheet" href="<?php echo assets('dist/css/common.css?v=8')?>">
		<link rel="stylesheet" href="<?php echo assets('dist/css/floating.css?v=1')?>">
		<link rel="stylesheet" href="<?php echo assets('dist/css/loader.css')?>">
		<link rel="stylesheet" href="<?php echo assets('dist/css/select2.css')?>">

		<style>
			:root { 
					--bg-color-primary : <?php echo (!empty($bg_color) && !empty($bg_color['primary']))?$bg_color['primary']:''; ?>; 
					--font-color-secondary : <?php echo (!empty($bg_color) && !empty($bg_color['secondary']))?$bg_color['secondary']:''; ?>; 
				}
				@font-face {
				  font-family: copperplate;
				  src: url(<?php echo assets('dist/css/copperplate/CopperplateCC-Heavy.ttf')?>);
				}

				body{ 
				  font-family: copperplate !important;
				}	
		</style>
	</head>
	<body class="wrapper blur">
		<!-- Modal  -->
		<?php $this->load->view('templates/modal/sm'); ?>
		<?php $this->load->view('templates/modal/lg'); ?>
		<?php $this->load->view('templates/modal/xl'); ?>
		<?php $this->load->view('templates/overlay/xl'); ?>
		<header class="sticky-top">
			<nav class="navbar navbar-expand-lg navbar-dark">
				<button 
					class="navbar-toggler hamburger_button" 
					type="button" 
					data-toggle="collapse" 
					data-target="#navbarSupportedContent" 
					aria-controls="navbarSupportedContent" 
					aria-expanded="false" 
					aria-label="Toggle navigation"
				><div class="hamburger_icon"><span></span><span></span><span></span></div></button>
				<div class="d-flex flex-column">
					<a class="navbar-brand d-flex flex-wrap flex-column" href="<?php echo base_url('/home'); ?>">
		    			<span class="border-bottom text-white font-weight-bold font-italic text-center" style="font-size: 10px;">
				  			<span><?php echo $company_name; ?></span>
			      		</span>
			      		<span class="text-white font-italic text-center" style="font-size: 12px;">
			  					<span class="text-white text-center font-italic" style="font-size: 12px;"><?php echo $fin_year ?></span>
				  				<input type="hidden" id="start_year" value="<?php echo $start_year ?>">
				  				<input type="hidden" id="end_year" value="<?php echo $end_year ?>">
			      		</span>	
		  			</a>
		    	</div>
				<div class="d-block d-sm-block d-md-block d-lg-none">
						<a class="p-2 rounded neu_flat_secondary text-secondary" href="<?php echo base_url('login/logout')?>" data-toggle="tooltip" data-placement="bottom" title="Logout">
						<i class="fa fa-sign-out"></i>
					</a>
					</div>
				</div>
				<div class="collapse navbar-collapse scroll" id="navbarSupportedContent">
					<ul class="navbar-nav navbar-nav-mobile" >
						<?php if(!empty($menu_data)): ?>
							<?php foreach ($menu_data as $key => $value): ?>
								<?php if(!empty($value['trans_data'])): ?>
									<li class="nav-item dropdown position-static" id="<?php echo $value['menu_js'] ?>">
										<a 
											class="nav-link dropdown-toggle" 
											href="#" 
											id="navbarDropdown" 
											role="button" 
											data-toggle="dropdown" 
											aria-haspopup="true" 
											aria-expanded="false"
										><?php echo strtoupper($value['menu_name']); ?></a>
										<div class="dropdown-menu w-100" aria-labelledby="navbarDropdown">
											<div class="d-flex flex-wrap">
												<?php if(!empty($value['trans_data'])): ?>
													<?php foreach ($value['trans_data'] as $k => $v): ?>
														<div class="col-12 col-sm-12 col-md-3 col-lg-3">
															<a 
																class="dropdown-item my-2" 
																id="<?php echo $v['mt_js'] ?>" 
																href="<?php echo base_url($value['menu_js'].'/'.$v['mt_url']); ?>"
															><?php echo strtoupper($v['mt_name']); ?></a>
														</div>
													<?php endforeach; ?>
												<?php endif; ?>
											</div>
										</div>
									</li>
								<?php else: ?>
								<?php endif; ?>
							<?php endforeach; ?>
						<?php endif; ?>
						<li class="d-block d-sm-block d-md-block d-lg-none nav-item">
							<form class="form-inline my-2 my-lg-0 search_wrapper">
								<select name="search_sub_menu" class="search_sub_menu_sm"></select>
							</form>
						</li>
						<li class="d-block d-sm-block d-md-block d-lg-none nav-item" id="profile">
							<div class="d-flex flex-column">
								<span class="text-white font-weight-bold font-italic text-center border-bottom" style="font-size: 12px;">
									<?php echo strtoupper($uname); ?>
								</span>
								<span class="text-white font-italic text-center" style="font-size: 10px;">
									<?php echo strtoupper($branch); ?>
								</span>	
							</div>
						</li>
					</ul>
				</div>
				<div class="d-none d-sm-none d-md-none d-lg-block mx-2">
					<div class="d-flex">
						<input 
							type="color"
							class="m-2 neu_flat_secondary"
							id="user_bgcolor"
							value="<?php echo (!empty($bg_color) && !empty($bg_color['primary']))?$bg_color['primary']:''; ?>"
							style="width: 2rem;"
							onchange="set_user_bg_color()"
						/>
						<form class="form-inline my-2 my-lg-0 search_wrapper">
							<select name="search_sub_menu" class="search_sub_menu"></select>
						</form>
					</div>
				</div>
				<div class="d-none d-sm-none d-md-none d-lg-block">
					<div class="d-flex">
						<div class="d-flex flex-column ml-3 mr-2">
							<span class=" text-white font-weight-bold font-italic text-center border-bottom" style="font-size: 12px;">
								<?php echo strtoupper($uname); ?>
							</span>
							<span class=" text-white font-italic text-center" style="font-size: 10px;">
								<?php echo strtoupper($branch); ?>
							</span>	
						</div>
						<a class="mx-2 p-2 rounded neu_flat_secondary text-secondary" href="<?php echo base_url('login/logout')?>" data-toggle="tooltip" data-placement="bottom" title="Logout">
							<i class="fa fa-sign-out"></i>
						</a>
					</div>
				</div>
			</nav>
		</header>
		<main>
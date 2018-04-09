	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">    
<title>
	<?php       
        $this->db->where('domain',$_SERVER['HTTP_HOST']);
        $this->db->select('*');
        $this->db->order_by('accountid', 'desc');
        $this->db->limit(1);
        $invoiceconf = $this->db->get('invoice_conf');
        $invoiceconf = (array)$invoiceconf->first_row();
	if(isset($invoiceconf['website_title']) && $invoiceconf['website_title']!='') {
	?>
	Forgot Password | <?php echo $invoiceconf['website_title']; ?>
	<?php
		}else{ 
	?>
	Forgot Password | ASTPP - Open Source Voip Billing Solution
	<?php
	}
	?>
</title>
<link rel="icon" href="<?php echo base_url(); ?>/assets/images/favicon.ico">

<link href="<?php echo base_url(); ?>/assets/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= base_url() ?>assets/fonts/font-awesome-4.5.0/css/font-awesome.css" rel="stylesheet">
<link href="<?php echo base_url(); ?>/assets/css/global-style.css" rel="stylesheet" type="text/css">


<!-- IE -->
<script type="text/javascript" src="<?php echo base_url(); ?>/assets/js/respond.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>/assets/js/respond.src.js"></script>
<!-- -->    

<script type="text/javascript" src="<?php echo base_url(); ?>/assets/js/module_js/generate_grid.js"></script>
<noscript>
<div id="noscript-warning">
ASTPP work best with JavaScript enabled
</div>
</noscript>

</script>

</head>
<? extend('master.php') ?>
<? startblock('extra_head') ?>
<body style="overflow:hidden; background: #343434;">


<section class="slice">
	<div class="w-section inverse">
    	<div class="container">
            <div class="row">
                <div class="col-md-4 col-md-offset-4">&nbsp;
					<span class="login_error">
                        <?php if (isset($astpp_notification)){ ?>
                        Login unsuccessful. Please make sure you entered the correct username and password, and that your account is active.
						<?php }else{
						 echo "&nbsp;";
						} ?>
                    </span>
                </div> 
					<br/>
                    <br/>
                    <br/>
                    <br/>
            	<div class="col-lg-4 col-md-offset-4" >
                    <div class="w-section inverse no-padding margin-t-20 forget_center">                       
                        <div class="w-box dark sign-in-wr box_shadow margin-b-10">
								<div class="">	
											<!-- Header Start-->
												<div class="col-md-12">
													  <?php
														if(isset($this->session->userdata['user_logo']) && $this->session->userdata['user_logo'] != ""){
															$logo = $this->session->userdata['user_logo'];
														}else{
															$logo = 'logo.png';
														}
														
													if ($this->session->userdata('userlevel_logintype') != '0') {?>
															<a class="col-md-8" style="padding:0px 0px 10px 0px" href="<?php echo base_url(); ?>">
																<img style="height: 44px; width:216px;" id="logo" alt="dashboard" src="<?php echo base_url(); ?>upload/<?php echo$logo;?>">
														<? } else {?> 
																<a class="col-md-8" style="padding:0px 0px 20px 0px" href="<?php echo base_url(); ?>">
																<img style="height: 44px; width:216px;" id="logo" title='ASTPP - Open Source Voip Billing Solution' alt='ASTPP - Open Source Voip Billing Solution' src="<?php echo base_url(); ?>upload/<?php echo$logo;?>">
														<? }?>
													
															</a>
												
												<div class="col-md-3 no-padding">
													<a href="<?php echo base_url(); ?>">
														<input type="submit" value="Login" name="Login" style="border-radius:4px;" class="btn btn-success col-md-12 margin-t-10 forget_login">
													</a>
												</div>
												</div>
											<!-- Header close-->
											<br>

									<form action="<?php echo base_url(); ?>confirmpassword/" class="form-light col-md-12 no-padding" onsubmit="return validateemail();" method="post" accept-charset="utf-8" id="customer_form" name="customer_form">
										<div class="input-group col-md-12 margin-t-15 padding-r-32 padding-l-32">
												<span class="input-group-addon"><i class="fa fa-envelope"></i></span>

												<input type="text" class="form-control" id="email" name="email" placeholder="Username OR Email" value = "<?php if (isset($value['email'])) echo  $value['email']; else ''; ?>" style="height:40px;">
										</div> 

												<?php if (isset($error['email'])) echo $error['email']; ?>              
											<div style="width: 97.67%; float: left;text-align: left; margin: 2% 22%;">
													<span id="e_name" style="color:red;"> </span>
											</div>
										<div class="input-group col-md-12 margin-t-15 padding-r-32 padding-l-32 margin-b-20">
											<button type="submit" class="btn-login" >Forgot Password</button>                      
										</div>
								   </form>
								</div>
							</div>    
						</div>
					
				</div> 
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
		function validateemail()  
		{
			var ename = document.customer_form.email; 
			if(emailLetter(ename))  
			{ 
				return true;
			}
			return false;
		}
									
		function emailLetter(ename)  
		{   
			if((email.value) == "")
			{
				document.getElementById("e_name").innerHTML = "The Username or Email field is required! ";				
				return false;   
			}else{
				return true;
			}
		
		}  
</script>	

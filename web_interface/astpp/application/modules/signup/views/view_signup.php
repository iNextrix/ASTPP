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
	Signup | <?php echo $invoiceconf['website_title']; ?>
	<?php
		}else{ 
	?>
	Signup | ASTPP - Open Source Voip Billing Solution
	<?php
	}
	?>
</title>
<link rel="icon" href="<?php echo base_url(); ?>/assets/images/favicon.ico">

<link href="<?php echo base_url(); ?>/assets/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= base_url() ?>assets/fonts/font-awesome-4.5.0/css/font-awesome.css" rel="stylesheet">
<link href="<?php echo base_url(); ?>/assets/css/global-style.css" rel="stylesheet" type="text/css">

<style>
.col-md-5
{
	width:100% !important;	
}
</style>
<!-- IE -->
<script type="text/javascript" src="<?php echo base_url(); ?>/assets/js/respond.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>/assets/js/respond.src.js"></script>
<!-- -->    



<script type="text/javascript" src="<?php echo base_url(); ?>/assets/js/module_js/generate_grid.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>/assets/js/jquery-1.7.1.js"></script>

<script type="text/javascript" src="<?php echo base_url(); ?>/assets/js/validate.js"></script>
<script type="text/javascript">
         $(window).on('resize', function () {

                if ($(window).width() <500){

                        $('img').css('width','153');
                }else{
                        $('img').css('width','253');    
                }
        });
$('document').ready(function()
{ 
                        if ($(window).width() <500){
			      $('img').css('width','153');
		        }else{
		              $('img').css('width','253');    
		        }
			$('.form-control').focus(function(){ 
					$('#email_error').value = '';
				
			})
            $("#customer_form").validate({
                rules: {
                    userCaptcha: "required",
                    email: {
						required: true,
						email: true,
					},
					first_name: {
						required: true						
					},
					telephone_1: {
                        phn_number: true,
                     },
                },
                messages: {
                    userCaptcha: "<div id= 'capcha_error' style='color: red; margin-right: 113px; margin-top: -8px; text-transform: none;'>Captcha is required</div>",
                    email: {
						required: '<div id= "email_error" style="color: red; margin-right: 132px; margin-top: -8px; text-transform: none;">Email is required</div>',
						email: '<div id= "email_error" style="color: red; margin-right: 32px; margin-top: -8px; text-transform: none;">Please enter a valid email address</div>',
					},
					first_name: {
						required: "<div id= 'first_name_error' style='color: red; margin-right: 100px; margin-top: -8px; text-transform: none;'>First name is required</div>",
					},
					telephone_1: {
						phn_number: "<div id='telephone_1_error' style='color: red; margin-right: 70px; margin-top: -8px; text-transform: none;'>Phone number is not valid</div>",
					},
                },
                submitHandler: function(form) {
                    form.submit();
                }
            });  
});
</script>


<noscript>
<div id="noscript-warning">
ASTPP work best with JavaScript enabled
</div>
</noscript>

</script>

</head>
<? extend('master.php') ?>
<? startblock('extra_head') ?>
<body style="overflow-x:hidden; background: #343434;">


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
                   
                   
            	<div class="col-md-8 col-md-offset-2">
                    <div class="w-section inverse no-padding margin-t-20">                       
                        <div class="w-box dark sign-in-wr box_shadow">
							
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
															<a class="col-md-10" style="padding:0px 0px 10px 0px" href="<?php echo base_url(); ?>">
																<img style="height: 53px; width:216px;" id="logo" alt="dashboard" src="<?php echo base_url(); ?>upload/<?php echo$logo;?>">
														<? } else {?> 
																<a class="col-md-10" style="padding:0px 0px 20px 0px" href="<?php echo base_url(); ?>">
																<img style="height: 44px; width:216px;" id="logo" title='ASTPP - Open Source Voip Billing Solution' alt='ASTPP - Open Source Voip Billing Solution' src="<?php echo base_url(); ?>upload/<?php echo$logo;?>">
														<? }?>
															</a>
												<div class="col-md-2">
													<a href="<?php echo base_url(); ?>">
														<input type="submit" value="Login" name="Login" style="border-radius: 2px" class="btn btn-success col-md-12 margin-t-10">
													</a>
									  				</div>
												</div>
		
		<form class="col-md-12 no-padding" action="<?php echo base_url(); ?>signup/signup_save/" onsubmit="return validatepass();" method="post" accept-charset="utf-8" id="customer_form" name="customer_form">
		
		
		<center>
	
		<div class="margin-t-15 padding-r-32 padding-l-32">

		<input type="hidden" name="key_unique"  value="<?php if (isset($key_unique)) {
	echo $key_unique;
} else {
	'';
}
?>"
		id="key_unique" size="15" maxlength="250" class="col-md-5 form-control"/>
	<div class="col-md-12 no-padding">

			<li class="col-md-6 no-padding">
				<label class="col-md-3 no-padding" style="text-align: left;">First Name *</label>
				<div class='col-md-9'>
				<input type="text" name="first_name" value="<?php if (isset($value['first_name'])) {
	echo $value['first_name'];
} else {
	'';
}
?>" id="first_name" size="15" maxlength="40" class="form-control"/>
				<div style="width: 97.67%; float: left;text-align: left;">
					<span id="f_name" style="color:red;"> </span>
				</div>
				</div>
			</li>
			<li class="col-md-6  no-padding">
				<label class="col-md-3 no-padding" style="text-align: left;">Last Name</label>
				<div class='col-md-9'>
				<input type="text" name="last_name" id="last_name" value="<?php if (isset($value['last_name'])) {
	echo $value['last_name'];
} else {
	'';
}
?>"  size="15" maxlength="40" class="form-control"/>
				<div style="width: 97.67%; float: left;text-align: left;">
					<span id="l_name" style="color:red;"> </span>
				</div>
				</div>
			</li>
			
		</div>
		<div class="col-md-12 no-padding">


			<li class="col-md-6 no-padding">
				<label class="col-md-3 no-padding" style="text-align: left;">Company&nbsp;Name&nbsp;</label>
				<div class='col-md-9'>
				<input type="text" id="company_name" name="company_name" value="<?php if (isset($value['company_name'])) {
	echo $value['company_name'];
} else {
	'';
}
?>" maxlength="40" size="15" class="form-control"/>
				</div>
			</li>
			<li class="col-md-6 no-padding">
				<label class="col-md-3 no-padding" style="text-align: left;">Telephone</label>
				<div class='col-md-9'>
				<input type="text" id="telephone_1" name="telephone_1" value="<?php if (isset($value['telephone_1'])) {
	echo $value['telephone_1'];
} else {
	'';
}
?>" size="15" maxlength="20" class="form-control"/>
				<div style="width: 100%; float: left;text-align: left;">
					<span id="phonenumber" style="color:red;"> </span>
				</div>
				</div>
			</li>
		
		</div>
		<div class="col-md-12 no-padding">


			<li class="col-md-6 no-padding">	
				<label class="col-md-3 no-padding" style="text-align: left;">Email *</label>
				<div class='col-md-9'>
				<input type="text" name="email" id="email" value="<?php if (isset($value['email'])) {
	echo $value['email'];
} else {
	'';
}
?>" size="50" maxlength="80" class="form-control"/>
				<span id="email_error" style="color:red;"> 
				<div style="width: 100%; float: left;text-align: left;"><?php if (isset($error['email'])) {
	echo $error['email'];
}
?></div></span>
				</div>
			</li>
			<li class="col-md-6  no-padding">
				<label for="Country" class="col-md-3 no-padding" style="text-align: left;">Country</label>
				<div class='col-md-9'>
				<?
				$js = 'id="country_id"';
				$country = form_dropdown(array('id'=>'country_id', 'name'=>'country_id','class'=>'country_id'), $this->db_model->build_dropdown("id,country", "countrycode", "", ""), '', 'id="country_id"');
				echo $country;
				?>
				</div>
			</li>

			</div>	
			<div class="col-md-12 no-padding">


			<li class="col-md-6  no-padding">
				<label for="Timezone" class="col-md-3 no-padding add_settings" style="text-align: left;">Timezone</label>
				<div class='col-md-9'>
				<?
				$timezone = form_dropdown(array('id'=>'timezone_id', 'name'=>'timezone_id','class'=>'timezone_id'), $this->db_model->build_dropdown("id,gmtzone", "timezone", "", ""), '', 'id="timezone_id"');
				echo $timezone;
				?>
				</div>
			</li>
			<li class="col-md-6 no-padding">
				<label for="Currency" class="col-md-3  no-padding add_settings" style="text-align: left;">Currency</label>
				<div class='col-md-9'>
				<?
				$currency = form_dropdown(array('id'=>'currency_id', 'name'=>'currency_id','class'=>'currency_id'), $this->db_model->build_dropdown("id,currencyname", "currency", "", ""), '', 'id="currency_id"');
				echo $currency;
				?>
				</div>
			</li>
			</div>
			<div class="col-md-12 no-padding">
		  	<li class="col-md-6  no-padding">
				<label class="col-md-3  no-padding" style="text-align: left;">Address</label>
				<div class='col-md-9'>
				<textarea id="address_1" name="address_1" value="" size="15" maxlength="200" class="form-control"> <?php if (isset($value['address_1'])) {
	echo $value['address_1'];
} else {
	'';
}
?> </textarea>
				</div>
			</li>
			<li class="col-md-6 no-padding">
				<label for="captcha" class="col-md-3 no-padding" style="text-align: left;">Captcha *</label>
				<div class='col-md-9'>
				<div class="col-md-12 no-padding" style="text-align:left;">
					<?php echo $captcha['image']; ?>
				</div>
				</div>
				
				<div class="col-md-3 no-padding"></div>
				<div class='col-md-9 margin-t-10'>															
				<input class='form-control posttoexternal' id="userCaptcha" name="userCaptcha" type="text" autocomplete="off" placeholder="Enter above text"/>
				<div style="width: 100%; float: left;text-align: left;"><?php if (isset($error['userCaptcha'])) {
	echo $error['userCaptcha'];
}
?></div>
				</div>
			</li>
		</div>
		<center>
		<div class="col-md-12 margin-t-20 margin-b-10">
		<button name="action" type="submit" value="Signup" class="btn btn-line-parrot" >Signup</button>
		<button class="btn btn-line-sky" id="reset" type="button">Reset</button>
		</div>
		</center>
		

		</div>
			
		</center>
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
		$("#country_id").val(<?= $country_id ?>);
		$("#timezone_id").val(<?= $timezone_id ?>);
		$("#currency_id").val(<?= $currency_id ?>);
</script>
<script type="text/javascript">
	var country = $("#country_id").val();
	var timezone = $("#timezone_id").val();
	var currency = $("#currency_id").val();
	$("#reset").click(function () {
		$("#first_name").val("");
		$("#last_name").val("");
		$("#company_name").val("");
		$("#telephone_1").val("");
		$("#email").val("");
		$("#country_id").val(country);
		$("#timezone_id").val(timezone);
		$("#currency_id").val(currency);
		$("#address_1").val("");
		$("#userCaptcha").val("");
		$("#email_error").hide("");
		$("#capcha_error").hide("");
		$("#first_name_error").hide("");
		$("#telephone_1_error").hide("");
	});
</script>


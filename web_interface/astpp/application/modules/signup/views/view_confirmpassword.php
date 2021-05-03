<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
			<title>
	<?php
$this->db->where('domain', $_SERVER['HTTP_HOST']);
$this->db->select('*');
$this->db->order_by('accountid', 'desc');
$this->db->limit(1);
$invoiceconf = $this->db->get('invoice_conf');
$invoiceconf = (array) $invoiceconf->first_row();
if (isset($invoiceconf['website_title']) && $invoiceconf['website_title'] != '') {    
	echo gettext("Forgot Password")." |"; echo $invoiceconf['website_title']; 
} else {
	echo gettext("Forgot Password")." | ".gettext("ASTPP - A Smart TelePhony Platform");
}
?>
</title>
<?php

{
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") {
        $domain = "https://" . $_SERVER["HTTP_HOST"] . "/";
    } else {
        $domain = "http://" . $_SERVER["HTTP_HOST"] . "/";
    }
    $http_host = $_SERVER["HTTP_HOST"];
    $this->db->select('favicon');
    $this->db->where("domain LIKE '%$domain%'");
    $this->db->or_where("domain LIKE '%$http_host%'");
    $user_favicon = (array) $this->db->get_where("invoice_conf")->first_row();
}
?>
<?php if(!empty($user_favicon['favicon'])) {  ?>
        <link rel="icon"
				href="<? echo base_url(); ?>upload/<? echo $user_favicon['favicon'] ?>" />
<?php } else { ?>
        <link rel="icon"
				href="<? echo base_url(); ?>assets/images/favicon.ico" />
<?php } ?>

<link href="<?php echo base_url(); ?>/assets/css/bootstrap.min.css"
				rel="stylesheet">
				<link
					href="<?php echo base_url(); ?>/assets/fonts/font-awesome-4.2.0/css/font-awesome.css"
					rel="stylesheet">
					<link href="<?php echo base_url(); ?>/assets/css/global-style.css"
						rel="stylesheet" type="text/css">

						<!-- IE -->
						<script type="text/javascript"
							src="<?php echo base_url(); ?>/assets/js/respond.js"></script>
						<script type="text/javascript"
							src="<?php echo base_url(); ?>/assets/js/respond.src.js"></script>
						<!-- -->

						<script type="text/javascript"
							src="<?php echo base_url(); ?>/assets/js/module_js/generate_grid.js"></script>
						<noscript>
							<div id="noscript-warning"><?php echo gettext("ASTPP work best with JavaScript enabled"); ?></div>
						</noscript>

						</script>

</head>
<? extend('master.php') ?>
<? startblock('extra_head') ?>

<body
	style="overflow-x: hidden; background: #343434 none repeat scroll 0% 0%;">
	<section class="slice">
	<div class="w-section inverse">
		<div class="container">
			<div class="row">
				<div class="col-md-4 col-md-offset-4">&nbsp;</div>
				<br /> <br /> <br /> <br /> <br />


				<div class="col-lg-4 col-md-offset-4">
					<div class="w-section inverse no-padding margin-t-20">
						<div class="w-box dark sign-in-wr box_shadow margin-b-10">
							<div class="col-md-12 no-padding">
			  <?php
    if (isset($this->session->userdata['user_logo']) && $this->session->userdata['user_logo'] != "") {
        $logo = $this->session->userdata['user_logo'];
    } else {
        $logo = 'logo.png';
    }

    if ($this->session->userdata('userlevel_logintype') != '0') {
        ?>
						<a class="col-md-9" style="padding: 0px 0px 10px 0px"
									href="<?php echo base_url(); ?>"> <img
									style="height: 44px; width: 216px;" id="logo" alt="dashboard"
									src="<?php echo base_url(); ?>upload/<?php echo$logo;?>">
					<? } else {?> 
							<a class="col-md-9" style="padding: 0px 0px 20px 0px"
										href="<?php echo base_url(); ?>"> <img
											style="height: 44px; width: 216px;" id="logo"
											title='ASTPP - Open Source Voip Billing Solution'
											alt='ASTPP - Open Source Voip Billing Solution'
											src="<?php echo base_url(); ?>upload/<?php echo$logo;?>">
					<? }?>	
			
				</a>

										<div class="col-md-3">
											<a href="<?php echo base_url(); ?>"><input type="submit"
												value="Login" name="Login"
												class="btn btn-success col-md-12 margin-t-10"></a>
										</div>
							
							</div>



							<form class="form-light col-md-12 no-padding"
								action="<?php echo base_url(); ?>confirmpass" method="post"
								accept-charset="utf-8" id="customer_form" name="customer_form">

								<input type="hidden" name="email" value=<?php echo $email; ?>>

									<div class="col-md-12 margin-t-20 padding-r-32 padding-l-32">
										<h3
											style="color: #173D77 !important; padding: 0 0 10px; border-bottom: 1px solid #ddd;"><?php echo gettext('Enter Your Password')?></h3>
									</div>

									<div class="col-md-12 margin-t-20 padding-r-32 padding-l-32">
										<label class="col-md-5 no-padding" style="text-align: left;"><?php echo gettext('Password')?></label>
										<div class="col-md-7 no-padding">
											<input type="password" name="password" required
												value="<?php if (isset($value['password'])) echo  $value['password']; else ''; ?>"
												id="password" size="15" maxlength="40" class="form-control" />
											<div class='error-style col-md-12 no-padding'
												style='color: red; font-size: 13px;' id="une"><?php if (isset($error['password'])) echo $error['password']; ?></div>
										</div>
									</div>

									<div class="col-md-12 margin-t-10 padding-r-32 padding-l-32">
										<label class="col-md-5 no-padding" style="text-align: left;"><?php echo gettext('Confirm Password')?></label>
										<div class="col-md-7 no-padding">
											<input type="password" name="confirmpassword" required
												value="<?php if (isset($value['confirm_password'])) echo  $value['confirm_password']; else ''; ?>"
												id="confirm_password" size="15" maxlength="40"
												class="form-control" />
											<div class='error-style col-md-12'
												style='color: red; font-size: 13px;' id="conpas"><?php if (isset($error['confirm_password'])) echo $error['confirm_password']; ?></div>
										</div>
									</div>

									<div
										class="col-md-12 margin-t-20 margin-b-20 padding-r-32 padding-l-32">
										<button name="action" type="submit" value="Submit"
											class="btn btn-line-parrot col-md-12"
											onclick="return check_function();"><?php echo gettext('Submit')?></button>




									</div>
							
							</form>


						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	</section>
</body>

</html>
<script type="text/javascript">
 $(document).ready(function(){
	$('#password').change(function(){
       $('#une').innerHTML('');
        return false;
	});
	$('#confirm_password').change(function(){
    $('#conpas').innerHTML('');
    return false;
  });
});
</script>
<script type="text/javascript">
$("#country_id").val(<?=$country_id?>);
$("#timezone_id").val(<?=$timezone_id?>);
$("#currency_id").val(<?=$currency_id?>);
function check_function()
{
	var pass = document.getElementById("password").value;
	var conpas = document.getElementById("confirm_password").value;
	var myregexp = /^.*(?=.{8,})(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z]).*$/;
	var match = myregexp.exec(pass);
	if (pass == "")
	{
		document.getElementById('une').innerHTML = "<?php echo gettext('Please Enter Password'); ?>";
		customer_form.password.focus();
		return(false);
	}else{
	document.getElementById('une').innerHTML = "";
	
	}
	if (match != null) {
		document.getElementById('une').innerHTML = "";
	} else {
	   document.getElementById('une').innerHTML = "<?php echo gettext('Password must be at least 8 characters and must contain at least one lower case letter, one upper case letter and one digit'); ?>";
		customer_form.password.focus();
		return(false);
	}
	 if (conpas == "")
	{
		document.getElementById('conpas').innerHTML = "<?php echo gettext('Please Enter Confirm Password'); ?>";
		customer_form.confirm_password.focus();
		return(false);
	}else{
	document.getElementById('conpas').innerHTML = "";
	}
	 if (pass != conpas)
	{
		document.getElementById('conpas').innerHTML = "<?php echo gettext('Confirm Password is not match'); ?>";
		customer_form.confirm_password.focus();
		return(false);
	}
	else
	{
		customer_form.submit();
	}
}

   


</script>
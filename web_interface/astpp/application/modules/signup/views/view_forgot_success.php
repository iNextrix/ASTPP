<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML+RDFa 1.1//EN" "-//W3C//DTD XHTML 1.0 Transitional//EN">
<html xml:lang="en" xmlns:fb="http://ogp.me/ns/fb#"
	xmlns="http://www.w3.org/1999/xhtml" lang="en">
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
<link rel="icon" href="<? echo base_url(); ?>assets/images/favicon.ico">
<link href="<?= base_url() ?>assets/css/bootstrap.min.css"
	rel="stylesheet">
<link
	href="<?= base_url() ?>assets/fonts/font-awesome-4.2.0/css/font-awesome.css"
	rel="stylesheet">
<link href="<?= base_url() ?>assets/css/global-style.css"
	rel="stylesheet" type="text/css">

<!-- IE -->
<script type="text/javascript"
	src="<?php echo base_url(); ?>assets/js/respond.js"></script>
<script type="text/javascript"
	src="<?php echo base_url(); ?>assets/js/respond.src.js"></script>
<noscript>
	<div id="noscript-warning"><?php echo gettext("ASTPP work best with JavaScript enabled."); ?></div>
</noscript>
<style>
.form-control {
	height: 40px;
}

.input-group .form-control {
	border-radius: 0px !important;
}
</style>
</head>


<body
	style="overflow: hidden !important; background: #343434 none repeat scroll 0% 0%;">
	<section class="slice">
	<div class="w-section inverse">
		<div class="container">

			<div class="row">

				<div class="col-md-4 col-md-offset-4">
					&nbsp;<span class="login_error">
                        <?php if (isset($astpp_notification)){ ?>
                        <?php echo gettext('Login unsuccessful. Please make sure you entered the correct username and password, and that your account is active.')?>
                    <?php

} else {
                            echo "&nbsp;";
                        }
                        $astpp_err_msg = $this->session->flashdata('astpp_signupmsg');
                        if ($astpp_err_msg) {
                            echo $astpp_err_msg;
                        }
                        ?>
                    </span>
				</div>
				<br /> <br />
				<br />
				<br />
				<br />
				<div class="col-md-4 col-md-offset-4">
					<div class="w-section inverse no-padding margin-t-20">
						<div class="w-box dark sign-in-wr box_shadow margin-b-10">
							<div class="padding-l-32 padding-r-32">
								<h2 class="text-center">
                          
                            	<?php
                            if (isset($this->session->userdata['user_logo']) && $this->session->userdata['user_logo'] != "") {
                                $logo = $this->session->userdata['user_logo'];
                            } else {
                                $logo = 'logo.png';
                            }

                            if ($this->session->userdata('userlevel_logintype') != '0') {
                                ?>
							<a class="col-md-12" style="padding: 0px 0px 10px 0px"
										href="<?php echo base_url(); ?>"> <img
										style="height: 44px; width: 216px;" id="logo" alt="dashboard"
										src="<?php echo base_url(); ?>upload/<?php echo$logo;?>">
							<? } else {?> 
							<a class="col-md-12" style="padding: 0px 0px 20px 0px"
										href="<?php echo base_url(); ?>"> <img
											style="height: 44px; width: 216px;" id="logo"
											title='ASTPP - Open Source Voip Billing Solution'
											alt='ASTPP - Open Source Voip Billing Solution'
											src="<?php echo base_url(); ?>upload/<?php echo$logo;?>">
							<? }?>	
                            	</a>
										<div class="col-md-12 no-padding"></div>
								
								</h2>

							</div>
							<div class="margin-t-15 padding-r-32 padding-l-32"
								style="color: #232222; text-align: left;"><?php echo gettext("We sent update password link.")." <br>".gettext("Please check your Email!")."<br>"; ?></div>

							<div class="margin-b-20 padding-r-32 padding-l-32">
								<div class="col-md-12 no-padding">
									<a href="<?php echo base_url(); ?>"> <input type="submit"
										value="Go to Login Page" name="Login"
										style="border-radius: 3px"
										class="btn btn-success col-md-12 margin-t-10">
									</a>
								</div>

							</div>

							<br>
							<br>
						</div>
					</div>
				</div>
			</div>
		</div>
	
	</section>
</body>
</html>
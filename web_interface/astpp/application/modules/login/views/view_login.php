<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML+RDFa 1.1//EN" "-//W3C//DTD XHTML 1.0 Transitional//EN">
<html xml:lang="en" xmlns:fb="http://ogp.me/ns/fb#" xmlns="http://www.w3.org/1999/xhtml" lang="en">
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
	Log In | <?php echo $invoiceconf['website_title']; ?>
	<?php
		}else{ 
	?>
	Log In | ASTPP - Open Source Voip Billing Solution
	<?php
	}
	?>
	</title>
    <?php  $user_favicon = $this->session->userdata('user_favicon'); ?>
    <?php if($user_favicon) {  ?>
	<link rel="icon" href="<? echo base_url(); ?>upload/<? echo $user_favicon ?>"/>
    <?php } else { ?>
	<link rel="icon" href="<? echo base_url(); ?>assets/images/<? echo $user_favicon ?>"/>
	<?php } ?>
    <link href="<?= base_url() ?>assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url() ?>assets/fonts/font-awesome-4.5.0/css/font-awesome.css" rel="stylesheet">
    <link href="<?= base_url() ?>assets/css/global-style.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery-1.7.1.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>/assets/js/validate.js"></script>
     <!-- IE -->
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/respond.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/respond.src.js"></script>
    <noscript>
	 <div id="noscript-warning">
	  ASTPP work best with JavaScript enabled
	</div>
    </noscript>
    <style>
        body{
            background:#343434 repeat scroll 0 0;
        }
         .login_validate {
            padding: 13px 21px 10px 14px;
            position: fixed;
            z-index: 9;
        }
        .error_login {
            margin-left: 36px;
            width: 280px !important;
        }
    .form-control{
     height:40px;
    }
  
    </style>
    <script type="text/javascript">
        $('document').ready(function() { 
	        $("#login_form").validate({
		        rules: {
			
			        username: {
				        required: true,
			        },
			        password: {
				        required: true,
			        },
	           },
		        messages: {
			        username: { 
				        required: '<span style="color: red; margin-top: -8px;margin-left:40px; text-transform: none;">Username is Required.</span>',
			        },
			        password: {
				        required: '<span style="color: red;margin-top: -8px;margin-left:40px; text-transform: none;">Password is Required.</span>',
			        },
		        },
		        submitHandler: function(form) {
			        form.submit();
		        }
	        });  
        });
</script>
</head>
<body style="overflow-y:hidden !important">
<section class="slice">
	<div class="w-section inverse">
    	<div class="container slice123">
        
            <div class="row">
                   
                        <div class="col-md-4 col-md-offset-4">&nbsp;<span class="login_error" style="color:white !important;">
                        <?php if (isset($astpp_notification)){
							echo $astpp_notification;
						}else{
						 echo "&nbsp;";
					} ?>
                    </span></div> <br/>
                    <br/>
                    <br/>
                    <br/>
            	<div class="col-lg-4 col-md-offset-4">
                    <div class="w-section inverse no-padding margin-t-20">                       
                        <div class="w-box dark sign-in-wr box_shadow margin-b-10">
                          <div class="padding-l-32 padding-r-32">
                        	<h2 class="text-center">
                          
							<?php
							if(isset($this->session->userdata['user_logo']) && $this->session->userdata['user_logo'] != ""){
								$logo = $this->session->userdata['user_logo'];
							}else{
								$logo = 'logo.png';
							}

							if ($this->session->userdata('userlevel_logintype') != '0') {?>
									<img style="height:53px;width:216px;" id="logo" alt="login" src="<?php echo base_url(); ?>upload/<?php echo $logo;?>">
							<? } else {?> 
									<img style="height:53px;width:216px;" id="logo"  alt='login' src="<?php echo base_url(); ?>upload/<?php echo $logo;?>">
							<? }?>
                            	<div class="clear"></div>
                            
                            </h2>
                           </div> 
                            <form role="form"  id="login_form" class="form-light" name="login_form"  action="<?php echo base_url(); ?>login/login" method="POST">
                                <div class="input-group col-md-12 padding-t-10  padding-r-32 padding-l-32">
				    <span class="input-group-addon login_validate "><i class="fa fa-user"></i></span>
				    <input type="text" class="form-control error_login" id="username" name="username" placeholder="Username OR Email" value = "" style="height:40px;" autocomplete="off">
                                </div>
                              
                                <div class="input-group col-md-12 margin-t-15 padding-r-32 padding-l-32">
				   <span class="input-group-addon login_validate"><i class="fa fa-lock"></i></span>
                                   <input type="password" class="form-control error_login" id="password" name="password" placeholder="Password" value = "" style="height:40px;" autocomplete="off">
                                </div>
                  
                               
                                    <div class="col-md-12 margin-t-15 padding-r-32 padding-l-32">
                                         <button type="submit" id="save_button"  name="save_button"  class="btn-login" >Log in</button>
                    
                                    </div>
<!--
ASTPP  3.0 
For Enable signup module
-->

		<div class="gray_lohin col-md-12 padding-r-32 padding-l-32 padding-b-10 no-padding margin-t-15">
			<p style='text-align:left;text-transform: none;' class="col-md-6 no-padding margin-t-10">
			<?php 
			if (Common_model::$global_config['system_config']['enable_signup'] == 0)
			{?>
			<a href="/signup/">Signup now!</a> 
			<?php
			}
			?>
			</p>
			
			<p style='text-align:right;' class="col-md-6 margin-t-10 no-padding"><a href="forgotpassword">Forgot Password?</a></p>
		</div>
<!--/*********************************/-->

                              <br/><br/>
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
 

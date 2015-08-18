<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML+RDFa 1.1//EN" "-//W3C//DTD XHTML 1.0 Transitional//EN">
<html xml:lang="en" xmlns:fb="http://ogp.me/ns/fb#" xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>	ASTPP - Open Source Voip Billing Solution</title>
    <link rel="icon" href="<? echo base_url(); ?>assets/images/favicon.ico">
    <link href="<?= base_url() ?>assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url() ?>assets/fonts/font-awesome-4.2.0/css/font-awesome.css" rel="stylesheet">
    <link href="<?= base_url() ?>assets/css/global-style.css" rel="stylesheet" type="text/css">
    
     <!-- IE -->
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/respond.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/respond.src.js"></script>
    <noscript>
	 <div id="noscript-warning">
	  ASTPP work best with JavaScript enabled
	</div>
    </noscript>
    <style>
    .form-control{
     height:40px;
    }
    .input-group .form-control{
    border-radius: 0px !important;
    }
    </style>
</head>
<body style="overflow-y:hidden !important">
<section class="slice">
	<div class="w-section inverse">
    	<div class="container">
        
            <div class="row">
                   
                        <div class="col-md-4 col-md-offset-4">&nbsp;<span class="login_error">
                        <?php if (isset($astpp_notification)){ ?>
                        Login unsuccessful. Please make sure you entered the correct username and password, and that your account is active.
                    <?php }else{
                         echo "&nbsp;";
                    } ?>
                    </span></div> <br/>
                    <br/>
                    <br/>
                    <br/>
            	<div class="col-md-4 col-md-offset-4">
                    <div class="w-section inverse no-padding margin-t-20">                       
                        <div class="w-box dark sign-in-wr box_shadow margin-b-10">
                          <div class="padding-r-20 padding-l-32 padding-r-32">
                        	<h2 class="text-center">
                          
                            	<img alt="login" src="<?= base_url() ?>assets/images/logo.png">
                             <br/> <br/>   
                            </h2>
                           </div> 
                            <form role="form" class="form-light padding-l-32 padding-r-32"  action="<?php echo base_url(); ?>login/login" method="POST">
                                <div class="input-group col-md-12 padding-b-10 padding-r-32 padding-l-32">
				    <span class="input-group-addon"><i class="fa fa-user"></i></span>
				    <input type="text" class="form-control" id="username" name="username" placeholder="User Name OR Email" value = "" style="height:40px;">
                                </div>
                              
                                <div class="input-group col-md-12 padding-t-10 padding-r-32 padding-l-32">
				   <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                   <input type="password" class="form-control" id="password" name="password" placeholder="Password" value = "" style="height:40px;">
                                </div>
                  
                               
                                    <div class="col-md-12 padding-t-20 padding-r-32 padding-l-32 padding-b-20">
                                        <button type="submit" class="btn-login" >Log in</button>                      
                                    </div>
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

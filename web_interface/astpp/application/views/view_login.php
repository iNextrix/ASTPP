<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML+RDFa 1.1//EN" "-//W3C//DTD XHTML 1.0 Transitional//EN">
<html xml:lang="en" xmlns:fb="http://ogp.me/ns/fb#" xmlns="http://www.w3.org/1999/xhtml" lang="en"><head>
    <link href="<?=base_url()?>assets/css/common.css" media="screen" rel="stylesheet" type="text/css">
  <link href="<?=base_url()?>assets/css/dlgbox.css" media="screen" rel="stylesheet" type="text/css">  
</head>
<body>

	<div class="p40">
		 <div class="dialog center"> 
         <?php if(isset($astpp_errormsg)):?>
         <div class="error" id="dlg_flash_msg">Login unsuccessful. Please make sure you entered the correct username and password, and that your account is active.</div>
        <?php endif;?>		
          <form action="<?php echo base_url();?>login/login" method="POST">
         	<div class="dialog_content login">
            		<div style="margin: 25px 0pt; text-align: center; clear: both;">
    				<a href="#"><img src="<?php echo base_url();?>assets/css/themes/apple_pie/images/logo.png" width="187" height="71" border="0"></a>
  					</div>                    
                    <div id="logins">               
                        
                        <div class="content" style="height: 110px;">
                          <p>
                            <label for="login" class="large" style="color: rgb(16, 51, 77); line-height: 24px; font-size: 15px;"> Username</label>
                            <input id="username" name="username" style="width: 200px;" tabindex="1" type="text">
                          </p>
                    
                          <p>
                            <label for="password" class="large" style="color: rgb(16, 51, 77); line-height: 24px; font-size: 15px;">Password</label>
                            <input id="password" name="password" style="width: 200px;" tabindex="2" value="" type="password">
                          </p>
                    
                          <p style="margin-bottom: 5px;">&nbsp;
                            
                            
                    
                            
                          </p>
                    
                          <p class="indented large">&nbsp;
                          
                          </p>
                          <div style="width: 0pt; height: 0pt; overflow: hidden;">
                            <button type="submit">&nbsp;</button>
                          </div>
                        </div>
                     
					</div>

                    <div class="dialog_buttons">
                      <input class="button default" id="btn_signin" name="commit" style="font-size: 17px; float: left;" tabindex="3" value="Sign In" type="submit">
                    </div>                  

            </div>
            </form>
         	
         </div>
	</div>

</body>
</html>

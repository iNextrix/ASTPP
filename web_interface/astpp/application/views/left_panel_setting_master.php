<? include('header.php'); ?>
<!--
ASTPP  3.0 
Add classes 
-->
<section class="slice color-one">
 <div class="w-section inverse no-padding border_box">
   <div class="container">
     <div class="">
<!--******************************************-->   

<!--
ASTPP  3.0 
Remove hr 
-->
<!--******************************************-->  
	  </div>
     </div>
    </div>
  </div>    
</section>
<section class="page-wrap">
<div id="toast-container" class="toast-top-right col-md-6" style="display:none;" >
 <div class="toast fa-check toast-success1">
        <button class="toast-close-button">
            <i class="fa fa-close"></i>
        </button>
        <div class="toast-message">
                    Success message
        </div>
  </div>
</div>

<div id="toast-container_error" class="toast-top-right col-md-6" style="display:none;z-index:999"> <!--  style="display:none;" -->
<div class="toast fa fa-times toast-danger1">
        <button class="toast-close-button">
            <i class="fa fa-close"></i>
        </button>
        <div class="toast-message">
                    Error message light
        </div>
  </div>
</div>
<?php
	$astpp_msg = false;
	$msg_type = "";
	$astpp_err_msg = $this->session->flashdata('astpp_errormsg');
	if ($astpp_err_msg) {
		$astpp_msg = $astpp_err_msg;
		$msg_type = "error";
	}
    
   $astpp_notify_msg = $this->session->flashdata('astpp_notification');
   if ($astpp_notify_msg) {  
		$astpp_msg = $astpp_notify_msg;
		$msg_type = "notification";
   }
   if ($astpp_msg) {
?>
<script> 
    var validate_ERR = '<?= $astpp_msg; ?>';
    var ERR_type = '<?= $msg_type; ?>';
    display_astpp_message(validate_ERR,ERR_type);
</script>
<?php } ?>

<?php
$class = "active";
?>
 <button type="button" class="navbar-toggle nav_sidetoggle collapsed white_bar" data-toggle="collapse" data-target=".sidebar">    
      	      <span class="sr-only">Toggle navigation</span>       
	      <span class="icon-bar"></span> 
	      <span class="icon-bar"></span> 
	      <span class="icon-bar"></span>
	</button> 
<div class="sidebar collapse">
	<ul class='sidemenu'>
	<li class="<?php if ($group_title == 'global') { echo $class; } ?>"><a href ='<?php echo base_url(); ?>systems/configuration/global'>Global</a></li>
	<li class="<?php if ($group_title == 'email') { echo $class; } ?>"><a href ='<?php echo base_url(); ?>systems/configuration/email'>Email</a></li>
	<li class="<?php if ($group_title == 'callingcard') { echo $class; } ?>"><a href ='<?php echo base_url(); ?>systems/configuration/callingcard'>Callingcard</a></li>
	<li class="<?php if ($group_title == 'opensips') { echo $class; } ?>"><a href ='<?php echo base_url(); ?>systems/configuration/opensips'>Opensips</a></li>
	<li class="<?php if ($group_title == 'paypal') { echo $class; } ?>"><a href ='<?php echo base_url(); ?>systems/configuration/paypal'>Paypal</a></li>
	<li class="<?php if ($group_title == 'signup') { echo $class; } ?>"><a href ='<?php echo base_url(); ?>systems/configuration/signup'>Signup</a></li>
  <li class="<?php if ($group_title == 'homer') { echo $class; } ?>"><a href ='<?php echo base_url(); ?>systems/configuration/homer'>Homer</a></li>
               </ul>
               
</div>		

<? start_block_marker('content') ?><? end_block_marker() ?>	


<?php include('footer.php'); ?>


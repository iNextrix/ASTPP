<? include('header.php'); ?>
<section class="slice color-one">
 <div class="w-section inverse p-0 border_box">
   <div class="container">
     <div class="">
  
	  </div>
     </div>
    </div>
  </div>    
</section>
<section class="page-wrap">
    <div class="container-fluid">
        <div class="row">
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
$category_id='';
?>
 <button type="button" class="navbar-toggler bg-secondary text-light d-lg-none d-block btn-block rounded-0 py-2 collapsed btn" data-toggle="collapse" data-target=".sidebar" aria-controls="sidebar" aria-expanded="false" aria-label="Toggle Side Navigation">    
    <i class="fa fa-bars fa-2x"></i>
 </button> 
 <?php 
	$uri_string= uri_string();
	$uri_arr=explode("/",$uri_string);
	if(isset($uri_arr[2]) && !empty($uri_arr[2])){
		$category_id=$uri_arr[2];
	}
	
 ?>
<div class="sidebar collapse">
	<ul class='sidemenu'>
		<?php 
		if(isset($menu)){
		foreach($menu as $key=>$val){ 
			if($val=='payment_methods'){
				$payment_methods=str_replace("_"," ",$val);
				$payment_methods_replace_string=ucwords($payment_methods);	?>
				<li class="<?php if ($group_title == $val) { echo $class; } ?>"><a href ='<?php echo base_url(); ?>systems/configuration/<?php echo $val;?>' ><?php echo $payment_methods_replace_string;?></a></li>	
					<?php }
				  else if($val=='ported_number'){
					$ported_number=str_replace("_"," ",$val);
					$ported_number_replace_string=ucwords($ported_number);  ?>
					<li class="<?php if ($group_title == $val) { echo $class; } ?>"><a href ='<?php echo base_url(); ?>systems/configuration/<?php echo $val;?>' ><?php echo $ported_number_replace_string;?></a></li>  
				  <?php }
				  else{?>
						<li class="<?php if ($group_title == $val) { echo $class; } ?>"><a href ='<?php echo base_url(); ?>systems/configuration/<?php echo $val;?>' ><?php echo ucfirst($val);?></a></li>	
					<?php }?>
			<?php }
		}
		if(isset($product_category)){?>
			<?php foreach($product_category as $category => $category_value){ ?>
				<li class="<?php if ($category_id == $category_value['id']) { echo $class; } ?>"><a href="<?php echo base_url();?>pages/services/<?php echo $category_value['id']; ?>"><?php echo $category_value['name']; ?> </a></li>
			<?php } ?>
		<?php }
		?>
     </ul>
</div>
<? start_block_marker('content') ?><? end_block_marker() ?>	
<?php include('footer.php'); ?>

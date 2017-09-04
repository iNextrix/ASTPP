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

<?php
$url = "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";

if (strpos($url, 'customer_cdrs') != true) {
   
?>
        <div class="col-md-12 no-padding">
	        <div class="col-md-8 no-padding">
                    <h2><? start_block_marker('page-title') ?><? end_block_marker() ?>	</h2>
	        
	        
                <span id="error_msg" class=" success"></span></div>
                 <div class="pull-right">
				</div>
<!--
ASTPP  3.0
Email host test
-->
	        <?php if (isset($test_email_flag) && $test_email_flag) { ?>
	                <div id="show_search" class="pull-right btn btn-warning btn margin-t-51"><a onclick="PopupCenter('<?=base_url()?>newmail/',resizable=1,width=580,height=700) "><font color="#fff"><i class= " fa fa-envelope-o"></i> &nbsp;Test Mail</font></a></div>
                <?php } ?>

<!--/*******************************/ -->
	        <div class="col-sm-4 no-padding">
		 <?php if (isset($batch_update_flag) && $batch_update_flag) { ?>
                <div id="updatebar" class="pull-right btn btn-update btn margin-t-51 margin-l-10"><i class="fa fa-retweet fa-lg"></i> <?php echo gettext('Batch Update'); ?></div>
                <?php } ?>
	        <?php if (isset($search_flag) && $search_flag) { ?>
	                <div id="show_search" class="pull-right btn btn-warning btn margin-t-51"><i class="fa fa-search"></i> <?php echo gettext('Search'); ?></div>
                <?php } ?>
                <?php if (isset($back_flag) && $back_flag) {?>
					<ul class="breadcrumb">
                <li class="active pull-right">
				<a href="<?= $_SERVER['HTTP_REFERER']?>"> <i class="fa fa-fast-backward" aria-hidden="true"></i> Back</a>
				</li></ul>
				<!----------------------------------------------------------------------------------------------------->
                <?php } ?>
                
                </div>
        	<div class="col-md-12 no-padding"></div>
<!--******************************************-->        	
	  </div>
<?php } ?>
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
		$astpp_msg = ucfirst($astpp_err_msg);
		$msg_type = "error";
	}
    
   $astpp_notify_msg = $this->session->flashdata('astpp_notification');
   if ($astpp_notify_msg) {  
		$astpp_msg = ucfirst($astpp_notify_msg);
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
<? start_block_marker('content') ?><? end_block_marker() ?>
 
<?php include('footer.php'); ?>


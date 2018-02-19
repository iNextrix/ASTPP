<? extend('left_panel_master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript">
function form_submit(){
    var email = document.forms["reseller_form"]["notify_email"].value;
    if (email == null || email == "") {
	  $("#email_err").html('Please enter your email');    
        return false;
    }else{
  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/; 
	var check = regex.test(email);
	if(check == true ){
	  $("#email_err").html('');    
	}else{
	  $("#email_err").html('Please enter proper email');    
          return false;
	}
    }
}
</script>
<?php endblock() ?>
<? startblock('page-title') ?>
<?= $page_title ?>
<? endblock() ?>
<?php startblock('content') ?>

<div id="main-wrapper" class="tabcontents">
    <div id="content">   
        <div class="row"> 
            <div class="col-md-12 no-padding color-three border_box"> 
                <div class="pull-left">
                     <ul class="breadcrumb">
                                         <?php $accountinfo=$this->session->userdata('accountinfo');
						  if($accountinfo['type']==1){ ?>
                          <li><a href="<?= base_url() . "user/user_myprofile/"; ?>">My Profile</a></li>
                          <?php } else{ ?>
			    <li><a href="#"><?php echo gettext('Configuration')?></a></li>
                          <?php } ?>
			  <li class='active'>
                             <a href="<?= base_url() . "user/user_alert_threshold/"; ?>"><?php echo gettext('Alert Threshold')?></a>
                          </li>
                    </ul>
                </div>
                <?php if($accountinfo['type']==1) { ?>
                   <div class="pull-right">
                    <ul class="breadcrumb">
		      <li class="active pull-right">
		      <a href="<?= base_url() . "user/user_myprofile/"; ?>"> <i class="fa fa-fast-backward" aria-hidden="true"></i> Back</a></li>
                    </ul>
                   </div>
                <?php }?>      

	     </div>
            <div class="padding-15 col-md-12">
                <div class="slice color-three pull-left content_border">
                    <?php echo $form; ?>
                    <?php if (isset($validation_errors) && $validation_errors != '') { ?>
                        <script>
                            var ERR_STR = '<?php echo $validation_errors; ?>';
                            print_error(ERR_STR);
                        </script>
                    <? } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<? endblock() ?>
<? startblock('sidebar') ?>
<? endblock() ?>
<? end_extend() ?>

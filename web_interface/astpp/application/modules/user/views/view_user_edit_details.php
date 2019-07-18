<? extend('left_panel_master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript">
$(document).ready(function() {
var country_id = "<?php echo $account_data[0]['country_id']; ?>"
var timezone_id = "<?php echo $account_data[0]['timezone_id']; ?>"
$("#country_id").val(country_id);
$("#timezone_id").val(timezone_id);
});
function form_submit(){
    var password = document.forms["reseller_form"]["password"].value;
    if (password == null || password == "") {
	  $("#password_err").html('<?php echo gettext("Please enter your password"); ?>');    
        return false;
    }else{
	  $("#password_err").html('');    
    }
    var fname = document.forms["reseller_form"]["first_name"].value;
    if (fname == null || fname == "") {
	  $("#fname_err").html('<?php echo gettext("Please enter your first name"); ?>');    
        return false;
    }else{
	  $("#fname_err").html('');    
    }
    var email = document.forms["reseller_form"]["email"].value;
    if (email == null || email == "") {
	  $("#email_err").html('<?php echo gettext("Please enter your email"); ?>');    
        return false;
    }else{
  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/; 
	var check = regex.test(email);
	if(check == true ){
	  $("#email_err").html('');    
	}else{
	  $("#email_err").html('<?php echo gettext("Please enter proper email"); ?>');    
          return false;
	}
    }
}
</script>
<?php endblock() ?>
<?php startblock('page-title') ?>
<?= $page_title ?>
<?php endblock() ?>
<?php startblock('content') ?>
<div id="main-wrapper" class="tabcontents">
    <div id="content">   
        <div class="row"> 
            <div class="col-md-12 no-padding color-three border_box"> 
                <div class="pull-left">
                    <ul class="breadcrumb">
                        <li><a href="<?= base_url()."accounts/".strtolower($accounttype)."_list/"; ?>"><?= ucfirst($accounttype); ?>s</a></li>
                        <li class="active"><a href="<?= base_url()."accounts/".strtolower($accounttype)."_edit/".$edit_id."/"; ?>"> <?= ucfirst($accounttype); ?> <?php echo gettext('Profile');?> </a></li>
                    </ul>
                </div>
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
<? end_extend() ?>

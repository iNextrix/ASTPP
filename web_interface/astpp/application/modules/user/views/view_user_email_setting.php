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
	var smtp_port = document.forms["reseller_form"]["smtp_port"].value;
	var smtp_host = document.forms["reseller_form"]["smtp_host"].value;
	var smtp_user = document.forms["reseller_form"]["smtp_user"].value;
	var smtp_password = document.forms["reseller_form"]["smtp_password"].value;
	if(smtp_port == ''){
		$("#smtp_port_err").html('<?php echo gettext("This field is require"); ?>');    
		return false;
	}else{
		$("#smtp_port_err").html('');    
	}
	if(smtp_host == ''){
		$("#smtp_host_err").html('<?php echo gettext("This field is require"); ?>');    
		return false;
	}else{
		$("#smtp_host_err").html('');    
	}
	if(smtp_user == ''){
		$("#smtp_user_err").html('<?php echo gettext("This field is require"); ?>');    
		return false;
	}else{
		$("#smtp_user_err").html('');    
	}
	if(smtp_password == ''){
		$("#smtp_password_err").html('<?php echo gettext("This field is require"); ?>');    
		return false;
	}else{
		$("#smtp_password_err").html('');    
	}
}
</script>
<?php endblock() ?>
<?php startblock('page-title') ?>
<?= $page_title ?>
<?php endblock() ?>
<?php startblock('content') ?>
	<div class="w-section inverse no-padding">
    	<div class="container">
   	    <div class="row">
   	    	  <div class="col-md-12 no-padding">
		    <div class="col-md-12">
			<div class="col-md-12 w-box">
				<div class="breadcrumb">
					<a href="<?= base_url(); ?>user/user_myprofile/"><?php echo gettext('My Profile');?></a>
					<a href="<?= base_url(); ?>user/user_myprofile/"><?php echo gettext('Email Setting');?></a>
				</div>
	  	  	   <form method='POST' id="reseller_form" name="reseller_form" action="<?= base_url()?>user/user_email_setting/" onSubmit="return form_submit();">

			    <div class='col-md-12 no-padding'>
				<input type="hidden" name='id' id='id' value="<?= $account_data[0]['id'] ?>">
				<div class='col-md-6'>
					<div class='col-md-4 '><label><?php echo gettext('Email');?>:</label></div>
					<div class='col-md-6'>
					   <select class=" form-control" name="email"  id="email">
						<option value="0" ><?php echo gettext('Enable')?></option>
						<option value="1" ><?php echo gettext('Disable')?></option>
					</select>
					</div>
				</div>
				<div class='col-md-6'>
					<div class='col-md-4'><label><?php echo gettext('SMTP port');?>:</label></div>
					<div class='col-md-6'><input type='text' name="smtp_port" id="smtp_port" value="<?= $account_data[0]['password'] ?>" class="col-md-12 form-control"><span id="smtp_port_err" style="color:red;"></span></div>
				</div>
			    </div>
			    <div class='col-md-12 no-padding'>
				<input type="hidden" name='id' id='id' value="<?= $account_data[0]['id'] ?>">
				<div class='col-md-6'>
					<div class='col-md-4 '><label><?php echo gettext('SMTP');?>:</label></div>
					<div class='col-md-6'>
					   <select class=" form-control" name="smtp"  id="smtp">
						<option value="0" ><?php echo gettext('Enable');?></option>
						<option value="1" ><?php echo gettext('Disable');?></option>
					</select>
					</div>
				</div>
				<div class='col-md-6'>
					<div class='col-md-4'><label><?php echo gettext('SMTP user');?>:</label></div>
					<div class='col-md-6'><input type='text' name="smtp_user" id="smtp_user" value="<?= $account_data[0]['password'] ?>" class="col-md-12 form-control"><span id="smtp_user_err" style="color:red;"></span></div>
				</div>
			    </div>
			    <div class='col-md-12 no-padding'>
				<input type="hidden" name='id' id='id' value="<?= $account_data[0]['id'] ?>">
				<div class='col-md-6'>
					<div class='col-md-4 '><label><?php echo gettext('SMTP host');?>:</label></div>
					<div class='col-md-6'><input type='text' id='smtp_host' name="smtp_host" value="<?= $account_data[0]['number'] ?>" class="col-md-12 form-control"><span id="smtp_host_err" style="color:red;"></span></div>
				</div>
				<div class='col-md-6'>
					<div class='col-md-4'><label><?php echo gettext('SMTP password')?>:</label></div>
					<div class='col-md-6'><input type='text' name="smtp_pass" id="smtp_password" value="<?= $account_data[0]['password'] ?>" class="col-md-12 form-control"><span id="smtp_password_err" style="color:red;"></span></div>
				</div>
			    </div>
			   <center>
			    <div class="col-md-12 margin-t-20 margin-b-20">
				<input type="submit" name='submit' value="save" class="btn btn-line-parrot">
				<button name="action" type="button" value="cancel" class="btn btn-line-sky margin-x-10" onclick="return redirect_page('/dashboard/')"><?php echo gettext('Cancel')?></button>
			    </div>
			   </center>
			   </form>
			</div>
		    </div>   
	        </div>
            </div>
        </div>
    </div>

<? endblock() ?>
<? startblock('sidebar') ?>
<? endblock() ?>
<? end_extend() ?>

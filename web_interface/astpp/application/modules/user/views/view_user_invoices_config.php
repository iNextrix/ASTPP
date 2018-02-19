<? extend('left_panel_master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript">
$(document).ready(function() {

});

</script>
<script type="text/javascript">
$( "#imagePreview" ).hide();

$(function() {
	$( "#logo_delete" ).live('click', function() {	
	var id =  "<?= $account_data['accountid'] ?>";
	var url='<?php echo base_url()."user/user_invoice_logo_delete/"; ?>'+id;
	var confirm_string = 'Are you sure want to remove logo?';
	var answer = confirm(confirm_string);
	if(answer){
		$.ajax({
			type:"POST",
			url:url,
			success: function(response){
				window.location = '<?php echo base_url()."user/user_invoice_config/"; ?>';
			}
		});
	}else{
		return false;
	}
	});
	$( "#imagePreview" ).live('click', function() {	
		$( "#imagePreview" ).fadeOut();
		$( "#uploadFile" ).val('');
	});
	$("#uploadFile").on("change", function()
	{
		var files = !!this.files ? this.files : [];
		if (!files.length || !window.FileReader) return; 
		if (/^image/.test( files[0].type)){ 
			var reader = new FileReader(); 
			reader.readAsDataURL(files[0]);
	
			reader.onloadend = function(){ 
				$("#imagePreview").show();
				$("#imagePreview").css("background", "url("+this.result+") no-repeat left top");
				$("#imagePreview").css("background-size", "90% 60%");
			}
		}
	});
});
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
                        <li><a href="<?= base_url()."user/user_myprofile/"; ?>">My Profile</a></li>
                        <li class='active'>
                            <a href="<?= base_url()."user/user_invoice_config/"; ?>">Company Profile</a>
                        </li>
                    </ul>
                </div>
                <div class="pull-right">
                    <ul class="breadcrumb">
		      <li class="active pull-right">
		      <a href="<?= base_url()."user/user_myprofile/"; ?>"> <i class="fa fa-fast-backward" aria-hidden="true"></i> Back</a></li>
                    </ul>
                </div>
            </div>
            <div class="padding-15 col-md-12">
		<form method='POST' id="reseller_form" name="reseller_form" action="<?= base_url()?>user/user_invoice_config/"   enctype="multipart/form-data">
                 <input type="hidden" name='id' id='id' value="<?= $account_data['id'] ?>">
                 <input type="hidden" name='accountid' id='accountid' value="<?= $account_data['accountid'] ?>">
		 <div class='col-md-12 no-padding'>
                    <div class='col-md-6'>
                            <div class='col-md-4 '><label>Company Name:</label></div>
                            <div class='col-md-6'><input type='text' name="company_name" value="<?= $account_data['company_name'] ?>" class="col-md-12 form-control"></div>
                    </div>
                    <div class='col-md-6'>
                            <div class='col-md-4 no-padding '><label>Invoice Prefix:</label></div>
                            <div class='col-md-6'><input type='text' name="invoice_prefix" value="<?= $account_data['invoice_prefix'] ?>" class="col-md-12 form-control"></div>
                    </div>
		</div>	
		<div class='col-md-12 no-padding'>
			<div class='col-md-6'>
				<div class='col-md-4 '><label>Address:</label></div>
				<div class='col-md-6'><input type='text' name="address" value="<?= $account_data['address'] ?>" class="col-md-12 form-control"></div>
			</div>
			<div class='col-md-6'>
				<div class='col-md-4 no-padding'><label>Invoice Due Notification:</label></div>
				<div class='col-md-6'>
				<select name="invoice_due_notification" value="<?= $account_data['invoice_due_notification'] ?>" class="col-md-12 form-control">
                                    <option value=0 <?=$account_data['invoice_due_notification'] == 0 ? 'selected' : ''; ?>>Enable</option>
                                    <option value=1 <?=$account_data['invoice_due_notification'] == 1 ? 'selected' : ''; ?>>Disable</option>
				</select>
			</div>	
		</div>
		</div>	
		<div class='col-md-12 no-padding'>
			<div class='col-md-6'>
				<div class='col-md-4 '><label>City:</label></div>
				<div class='col-md-6'><input type='text' name="city" value="<?= $account_data['city'] ?>" class="col-md-12 form-control"></div>
			</div>
			<div class='col-md-6'>
				<div class='col-md-4 no-padding '><label>Invoice Notification:</label></div>
				<div class='col-md-6'>
				<select name="invoice_notification" value="<?= $account_data['invoice_notification'] ?>" class="col-md-12 form-control">
					<option value=0 <?=$account_data['invoice_notification'] == 0 ? 'selected' : ''; ?> >Enable</option>
					<option value=1 <?=$account_data['invoice_notification'] == 1 ? 'selected' : ''; ?> >Disable</option>
				</select>
				</div>
			</div>
		</div>	
		<div class='col-md-12 no-padding'>
			<div class='col-md-6'>
				<div class='col-md-4 '><label>Province:</label></div>
				<div class='col-md-6'><input type='text' name="province" value="<?= $account_data['province'] ?>" class="col-md-12 form-control"></div>
			</div>
			<div class='col-md-6'>
				<div class='col-md-4 no-padding '><label>Invoice Date Interval:</label></div>
				<div class='col-md-6'><input type='text' name="interval" value="<?= $account_data['interval'] ?>" class="col-md-12 form-control"></div>
			</div>
		</div>	
		<div class='col-md-12 no-padding'>
			<div class='col-md-6'>
				<div class='col-md-4 '><label>Country:</label></div>
				<div class='col-md-6'><input type='text' name="country" value="<?= $account_data['country'] ?>" class="col-md-12 form-control"></div>
			</div>
			<div class='col-md-6'>
				<div class='col-md-4 no-padding '><label>Notify Before Days:</label></div>
				<div class='col-md-6'><input type='text' name="notify_before_day" value="<?= $account_data['notify_before_day'] ?>" class="col-md-12 form-control"></div>
			</div>
		</div>	
		<div class='col-md-12 no-padding'>
			<div class='col-md-6'>
				<div class='col-md-4 '><label>Zipcode:</label></div>
				<div class='col-md-6'><input type='text' name="zipcode" value="<?= $account_data['zipcode'] ?>" class="col-md-12 form-control"></div>
			</div>
			<div class='col-md-6'>
				<div class='col-md-4  no-padding'><label>Invoice Start Form:</label></div>
				<div class='col-md-6'><input type='text' name="invoice_start_from" value="<?= $account_data['invoice_start_from'] ?>" class="col-md-12 form-control"></div>
			</div>
		</div>	
		<div class='col-md-12 no-padding'>
			<div class='col-md-6'>
				<div class='col-md-4 '><label>Telephone:</label></div>
				<div class='col-md-6'><input type='text' name="telephone" value="<?= $account_data['telephone'] ?>" class="col-md-12 form-control"></div>
			</div>
			<div class='col-md-6'>
				<div class='col-md-4  no-padding'><label>Email Address:</label></div>
				<div class='col-md-6'><input type='text' name="emailaddress" value="<?= $account_data['emailaddress'] ?>" class="col-md-12 form-control"></div>
                        </div>
		</div>
		<div class='col-md-12 no-padding'>
			<div class='col-md-6'>
				<div class='col-md-4 '><label>Fax:</label></div>
				<div class='col-md-6'><input type='text' name="fax" value="<?= $account_data['fax'] ?>" class="col-md-12 form-control"></div>
			</div>
			<div class='col-md-6'>
				<div class='col-md-4  no-padding'><label>Website:</label></div>
				<div class='col-md-6'><input type='text' name="website" value="<?= $account_data['website'] ?>" class="col-md-12 form-control"></div>
			</div>
		</div>	
		<div class='col-md-12 no-padding'>
			<div class='col-md-6'>
				<div class='col-md-4 '><label>Website Domain:</label></div>
				<div class='col-md-6'><input type='text' name="fax" value="<?= $account_data['domain'] ?>" class="col-md-12 form-control"></div>
			</div>
			<div class='col-md-6'>
				<div class='col-md-4  no-padding'><label>Website Header:</label></div>
				<div class='col-md-6'><input type='text' name="website" value="<?= $account_data['website_title'] ?>" class="col-md-12 form-control"></div>
			</div>
		</div>	
		<div class='col-md-12 no-padding'>
			<div class='col-md-6'>
				<div class='col-md-4 '><label>Website Footer:</label></div>
				<div class='col-md-6'><input type='text' name="fax" value="<?= $account_data['website_footer'] ?>" class="col-md-12 form-control"></div>
			</div>
			<div class='col-md-6'>
				<div class='col-md-4  no-padding'><label>Compnay Logo:</label></div>
				<div class="col-md-6">
					<div class="fileinput fileinput-new input-group" data-provides="fileinput">
						<div class="form-control" data-trigger="fileinput">
							<span class="fileinput-filename"></span>
						</div>
						<span class="input-group-addon btn btn-primary btn-file" style="display: table-cell;">
						<span class="fileinput-new">Select file</span>
							<input style="height:33px;"type="file" name="file" size="20" maxlength="100" class="" id="uploadFile" style="" ">
						</span>
					</div>
					</div>
				</div>
		</div>	
<?php
	if($account_data['logo']  != ''){
		 $logo=$account_data['file'];
	}else{
		 $logo=$account_data['logo'];
	}
		  if($logo != ''){        
			 $file_name= base_url()."upload/$logo";
?>

		<div class='col-md-12 no-padding'>
			<div class='col-md-6'>
			<div class='col-md-4'><label>Delete logo</label></div>

			<div class='col-md-5'>
				<input type="button" name="button" value="Delete" style="margin-top:20px;" id="logo_delete" size="20" class="btn btn-line-parrot" margin-top:20px; />
			</div>
			</div>
			<div class='col-md-6'>
			<div class='col-md-4 no-padding'><label>Existing Image</label></div>
			<div class='col-md-5 '>
				<image type="image" name="image" value="" style="width:100%;margin-top:20px;" src="<?= $file_name ?>" width:100%;margin-top:20px; />
			</div>
			</div>
		</div>	
		<?php } ?>
		<center>
		<div class="col-md-12 margin-t-20 margin-b-20">
			<input type="submit" value="save" name='submit' class="btn btn-line-parrot">
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

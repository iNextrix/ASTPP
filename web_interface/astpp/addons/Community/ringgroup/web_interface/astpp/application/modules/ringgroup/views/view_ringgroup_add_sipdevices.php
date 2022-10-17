<? extend('master.php') ?>
<?php error_reporting(E_ERROR); ?>
<? startblock('extra_head') ?>
<?php endblock() ?>
<?php startblock('page-title') ?>
<?= $page_title ?>
<?php endblock() ?>
<?php startblock('content') ?>
<script type="text/javascript">
	function changeTest(val){
		var val_count = "<?= $count ?>";
		if(val == '0'){
			document.getElementById( 'sipdevice_set').style.display = 'block';
			document.getElementById( 'account_set').style.display = 'block';
		}else{
			document.getElementById( 'sipdevice_set').style.display = 'none';
			document.getElementById( 'account_set').style.display = 'none';

		}

	}
	function isNumberKey(evt){
		var charCode = (evt.which) ? evt.which : event.keyCode
		if (charCode > 31 && (charCode < 48 || charCode > 57))
			return false;
		return true;
	}
</script>
<div class="row">
	<div class="col-md-12 main-box">
		
		<div style="color:red;margin-left: 60px;">
			<?php if (isset($validation_errors)) echo $validation_errors; ?> 
		</div>
		<form action="<?php echo base_url(); ?>ringgroup/ringgroup_add/" accept-charset="utf-8" id="ringgroup_form" method="POST" name="ringgroup_form">
			<div style="width:100%;float:left;"><div style="width:100%;float:left;">
				<ul class="padding-15">
					<div class="col-md-12 no-padding">
						<fieldset>
							<legend><?php echo gettext("Ring Group Information"); ?></legend>
							<li class="col-md-12">
								<center>
									<div class="col-md-6" style="aling:center;"> 
										<label class="col-md-8 no-padding"><font size=3> <?php echo gettext("Do you want create sip devices?"); ?></font>  </label>
										<select name="strategy" class='col-md-2  selectpicker' data-live-search="true"   onChange="changeTest(this.value)">
											<option value="1" ><?php echo gettext("No"); ?></option>
											<option value="0"><?php echo gettext("Yes"); ?></option> 
										</select>
									</div>
									<div class="col-md-3">&nbsp;</div>
								</center>
							</li>
							<?php
							$accountinfo = $this->session->userdata('accountinfo');
							if($accountinfo['type'] == -1){
								?>
								<li class="col-md-6" style="display:none;" id="account_set">
									<label class="col-md-3 no-padding"><?php echo gettext("Account"); ?></label>
									<? $accounts = form_dropdown('accountid', $this->db_model->build_concat_dropdown("id,first_name,last_name,number,company_name", "accounts","where_arr", array("reseller_id" => "0", "status" => "0", "deleted" => "0", "type" => "0")), '');
									echo $accounts;
									?>
								</li>
								<?php 
							}else{
								?>
								<li class="col-md-6" style="display:none;" id="account_set">
									<label class="col-md-3 no-padding"><?php echo gettext("Account"); ?></label>
									<? $accounts = form_dropdown('accountid', $this->db_model->build_concat_dropdown("id,first_name,last_name,number,company_name", "accounts","where_arr", array("id" => $accountinfo['id'], "status" => "0", "deleted" => "0", "type" => "0")), '');
									echo $accounts;
									?>
								</li>


								<?php
							}
							?> 
							<li class="col-md-6" style="display:none;"  id="sipdevice_set">
								<label class="col-md-3 no-padding"><?php echo gettext("Sip device Count"); ?></label>
								<input name="sip_device_count" value="" id='sip_device_count' size="20" maxlength="30" class="col-md-5 form-control"  type="text" onkeypress="return isNumberKey(event)"/>
							</li>
						</ul>
					</div>
				</div>
				<center>
					<div class="col-md-12 margin-t-20 margin-b-20">
						<button name="action" type="submit" value="save" id="submit" class="btn btn-success" ><?php echo gettext("Next"); ?></button>
						<button name="action" type="button" value="cancel" class="btn btn-primary margin-x-10" onclick="return redirect_page('/ringgroup/ringgroup_list/')" ><?php echo gettext("Back"); ?></button></center></div>
					</fieldset>
				</form>
			</div>      

			
			<? endblock() ?>
			<? startblock('sidebar') ?>
			<? endblock() ?>
			<? end_extend() ?>

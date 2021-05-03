<?php include(FCPATH.'application/views/popup_header.php'); ?>
<!-- <section class="slice m-0">
 <div class="w-section inverse p-0">
        <div class="col-md-12">
	        <h3 class="text-dark fw4 pl-4 pr-4 pt-4 rounded-top"><? echo $page_title; ?></h3 class="bg-secondary text-light p-3 rounded-top">
		</div>
  </div>    
</section> -->
<script type="text/javascript">
/*function validateForm(){
	var formflag = true;
	
	var setup_fees= document.getElementById("setup_fees").value;
	var setup_fees_exp= /^[0-9]+$/;
	var price_exp= /^[0-9]+$/;
	var price= document.getElementById("prices").value;
	
	if((!setup_fees.match(setup_fees_exp))){
		document.getElementById('setup_fee_error').innerHTML = "<i style='color:#D95C5C; padding-right: 6px; padding-top: 10px;' class='fa fa-exclamation-triangle'></i><span class='popup_error error  p-0'>Plase enter valid setpup fees.</span>";
		document.getElementById('setup_fee').focus();
		jQuery('#setup_fees').addClass('borderred');
		formflag=false;
	}
	else if((!price.match(price_exp))){
		document.getElementById('price_error').innerHTML = "<i style='color:#D95C5C; padding-right: 6px; padding-top: 10px;' class='fa fa-exclamation-triangle'></i><span class='popup_error error  p-0'>Plase enter valid Prices.</span>";
		document.getElementById('prices').focus();
		jQuery('#prices').addClass('borderred');
		formflag=false;
	}else{
		document.getElementById('setup_fee_error').innerHTML = "";
		jQuery('#setup_fees').removeClass('borderred');
		document.getElementById('price_error').innerHTML = "";
		jQuery('#prices').removeClass('borderred');
		formflag=true;
	}
	
	 if(formflag){
		 $('#product_edit_form').submit();
	 }	 	
	
}*/
</script>
<section class="slice color-three">
	<div id="floating-label" class="w-section inverse p-0">
		<div class="col-md-12 p-0 card-header">
			<h3 class="fw4 p-4 m-0"><?php echo gettext("Edit Info"); ?></h3>
		</div>
		<form method="post" name="product_add_form" id="product_edit_form"
			action="<?= base_url()."did/did_resellerdid_save/";?>">
			<div class="col-12 p-4">
				<div class="col-12 card pb-4 form-inline">
					<h3 class="bg-secondary text-light p-3 rounded-top"><?php echo gettext("DID Information"); ?></h3>
					<input class="col-md-12 form-control form-control-lg m-0"
						name="product_id" value="<?php echo $did_info['product_id']?>"
						type="hidden" />
					<div class='col-md-12 p-4'>
						<div class="col-md-3 float-left text-center"></div>
						<div class="col-md-4 float-left text-center font-weight-bold"><?php echo gettext("BUY COST"); ?></div>
						<div class="col-md-5 float-left text-center font-weight-bold"><?php echo gettext("SELL COST"); ?></div>
					</div>

					<div class='col-md-12 form-group'>
						<label class="col-md-3 p-0"><?php echo gettext("DID"); ?></label>
						<div class="col-md-4 p-0">
							<input class="col-md-12 form-control form-control-lg" disabled
								value="<?php echo (isset($did_info['name']))?$did_info['name']:'' ?>"
								type="text" />
						</div>
						<div class="col-md-5 pr-0">
							<input class="col-md-12 form-control form-control-lg w-100"
								disabled
								value="<?php echo (isset($did_info['name']))?$did_info['name']:'' ?>"
								type="text" />
						</div>
					</div>
					<div class='col-md-12 form-group'>
						<label class="col-md-3 p-0"><?php echo gettext("Buy Cost"); ?> (<?php echo ($currency)?>)</label>
						<div class="col-md-4 p-0">
							<input class="col-md-12 form-control form-control-lg"
								value="<?php echo (isset($did_info['buy_cost']))?$this->common->convert_to_currency ( '', '', $did_info['price'] ):'' ?>"
								type="text" disabled />
						</div>
						<div class="col-md-5 pr-0">
							<input class="col-md-12 form-control form-control-lg w-100"
								value="<?php echo (isset($did_info['buy_cost']))?$this->common->convert_to_currency ( '', '', $did_info['price'] ):'' ?>"
								name="product_buy_cost" type="text" readonly />
						</div>
					</div>

					<!-- <div class='col-md-12 form-group'> 
            <label class="col-md-3 p-0">Commission (%)</label>
           <input class="col-md-12 form-control form-control-lg w-100" name="commission" value="<?php echo (isset($did_info['commission']))?$this->common->convert_to_currency ( '', '', $did_info['commission'] ):'' ?>" type="text"/>
         </div>-->
  <?php if($this->session->userdata ( 'logintype' ) == 1  && $accountinfo['is_distributor'] == 1) {?>
   <div class='col-md-12 form-group'>
						<label class="col-md-3 p-0"><?php echo gettext("Setup Fee"); ?> (<?php echo ($currency)?>)</label>
						<div class="col-md-4 p-0">
							<input class="col-md-12 form-control form-control-lg"
								value="<?php echo  $this->common->convert_to_currency ( '', '', $did_info['setup_fee'] )?>"
								type="text" disabled />
						</div>
						<div class="col-md-5 pr-0">
							<input class="col-md-12 form-control form-control-lg w-100"
								name="setup_fee"
								value="<?php echo  $this->common->convert_to_currency ( '', '', $did_info['setup_fee'] )?>"
								type="text" readonly />

						</div>
					</div>
					<div class='col-md-12 form-group'>
						<label class="col-md-3 p-0"><?php echo gettext("Price"); ?> (<?php echo ($currency)?>)</label>
						<div class="col-md-4 p-0">
							<input class="col-md-12 form-control form-control-lg"
								value="<?php echo (isset($did_info['price']))?$this->common->convert_to_currency ( '', '', $did_info['price'] ):'' ?>"
								type="text" disabled />
						</div>
						<div class="col-md-5 pr-0">
							<input class="col-md-12 form-control form-control-lg w-100"
								name="price"
								value="<?php echo (isset($did_info['price']))?$this->common->convert_to_currency ( '', '', $did_info['price'] ):'' ?>"
								type="text" readonly />

						</div>
					</div>
  <?php } else { ?>
 <div class='col-md-12 form-group'>
						<label class="col-md-3 p-0"><?php echo gettext("Setup Fee"); ?> (<?php echo ($currency)?>)</label>
						<div class="col-md-4 p-0">
							<input class="col-md-12 form-control form-control-lg"
								value="<?php echo  $this->common->convert_to_currency ( '', '', $did_info['setup_fee'] )?>"
								type="text" disabled />
						</div>
						<div class="col-md-5 pr-0">
							<input class="col-md-12 form-control form-control-lg w-100"
								name="setup_fee" id="setup_fees"
								value="<?php echo  $this->common->convert_to_currency ( '', '', $did_info['setup_fee'] )?>"
								type="text" />
							<div id="setup_fee_error"
								class="tooltips error_div float-left p-0"
								style="display: block;"></div>
						</div>
					</div>
					<div class='col-md-12 form-group'>
						<label class="col-md-3 p-0"><?php echo gettext("Price"); ?> (<?php echo ($currency)?>)</label>
						<div class="col-md-4 p-0">
							<input class="col-md-12 form-control form-control-lg"
								value="<?php echo (isset($did_info['price']))?$this->common->convert_to_currency ( '', '', $did_info['price'] ):'' ?>"
								type="text" disabled />
						</div>
						<div class="col-md-5 pr-0">
							<input class="col-md-12 form-control form-control-lg w-100"
								name="price" id="prices"
								value="<?php echo (isset($did_info['price']))?$this->common->convert_to_currency ( '', '', $did_info['price'] ):'' ?>"
								type="text" />
							<div id="price_error" class="tooltips error_div float-left p-0"
								style="display: block;"></div>
						</div>
					</div>







<?php } ?>
  <div class='col-md-12 form-group'>
						<label class="col-md-3 p-0"><?php echo gettext("Billing Type"); ?></label>
						<div class="col-md-4 p-0">
							<select class="form-control selectpicker form-control-lg" disable
								data-live-search='true' datadata-live-search-style='begins'
								disabled>
								<option value="1" <?php if($did_info['billing_type'] == '0'){ ?>
									selected="selected" <?php } ?>><?php echo gettext("One Time"); ?></option>
								<option value="0" <?php if($did_info['billing_type'] == '1'){ ?>
									selected="selected" <?php } ?>><?php echo gettext("Recurring"); ?></option>
							</select>
						</div>
						<div class="col-md-5 pr-0">
							<select name="billing_type"
								class="form-control selectpicker form-control-lg" disable
								data-live-search='true' datadata-live-search-style='begins'
								disabled>
								<option value="1" <?php if($did_info['billing_type'] == '0'){ ?>
									selected="selected" <?php } ?>><?php echo gettext("One Time"); ?></option>
								<option value="0" <?php if($did_info['billing_type'] == '1'){ ?>
									selected="selected" <?php } ?>><?php echo gettext("Recurring"); ?></option>
							</select>
						</div>
					</div>
					<div class='col-md-12 form-group'>
						<label class="col-md-3 p-0"><?php echo gettext("Billing Days"); ?></label>
						<div class="col-md-4 p-0">
							<input class="col-md-12 form-control form-control-lg"
								value="<?php echo (isset($did_info['billing_days']))?$did_info['billing_days']:'' ?>"
								type="text" disabled />
						</div>
						<div class="col-md-5 pr-0">
							<input class="col-md-12 form-control form-control-lg w-100"
								name="billing_days"
								value="<?php echo (isset($did_info['billing_days']))?$did_info['billing_days']:'' ?>"
								type="text" disabled />
						</div>
					</div>

					<!--<?php if($did_info['product_category'] == 1){ ?>
		<div class='col-md-12 form-group'> 
                      <label class="col-md-3 p-0">Free Minutes</label>
                     <input class="col-md-12 form-control form-control-lg m-0" name="free_minutes" value="<?php echo (isset($did_info['free_minutes']))?$did_info['free_minutes']:'' ?>" type="text"/>
                </div>
		<?php } ?>-->
				</div>
			</div>
			<div class="col-12 my-4">
				<div class="col-md-6 float-left">
					<button class="btn btn-success btn-block" name="action"
						value="Save" type="submit"><?php echo gettext("Save"); ?> </button>
				</div>
				<div class="col-md-6 float-left">
					<button class="btn btn-secondary mx-2 btn-block" name="cancel"
						onclick="return redirect_page('/did/did_available_list/')"
						value="Cancel" type="button">  <?php echo gettext("Cancel"); ?> </button>
				</div>
			</div>


		</form>
	</div>
</section>
<?php exit; ?>

<? extend('master.php') ?>
<? startblock('extra_head') ?>

<? endblock() ?>

<? startblock('page-title') ?>
<?= $page_title ?>
<? endblock() ?>

<? startblock('content') ?>

<section class="slice color-three pb-4">
	<div id="floating-label" class="w-section inverse p-0">
		<form action="#" accept-charset="utf-8" id="ordres_assign_form"
			method="POST" name="ordres_assign_form">
			<div class="col-md-4 float-left pl-0">
				<div class="card p-4">
					<div class="card pb-4 px-0">
						<h3 class="bg-secondary text-light p-2 rounded-top"><?php echo gettext('Basic Information');?></h3>
						<div class="row px-4">
							<input class="col-md-12 form-control form-control-lg m-0"
								name="accountid" value="<?php echo $account_arr['id']?>"
								size="16" type="hidden" /> <input
								class="col-md-12 form-control form-control-lg m-0"
								name="product_id" value="<?php echo $product_info['id']?>"
								size="16" type="hidden" />
							<div class='col-md-12 form-group'>
								<label class="col-md-12 p-0 control-label"><?php echo gettext('Product');?></label>

								<input class="col-md-12 form-control form-control-lg m-0"
									disabled value="<?php echo $product_info['name']?>" size="16"
									type="text" />

							</div>

							<div class='col-md-12 form-group'>
								<label class="col-md-12 p-0 control-label"><?php echo gettext('Account');?></label>

								<input class="col-md-12 form-control form-control-lg m-0"
									name="accountant_name" disabled
									value="<?php echo $account_arr['first_name'] . ' (' .$account_arr['number'] . ')';?>"
									size="16" type="text" />
								<div class="tooltips error_div pull-left no-padding"
									id="accountant_name_error_div" style="display: none;">
									<i class="fa fa-exclamation-triangle error_triangle"></i><span
										class="popup_error error  no-padding"
										id="accountant_name_error"> </span>
								</div>

							</div>

							<div class='col-md-12 form-group'>
								<label class="col-md-12 p-0 control-label"><?php echo gettext('Category');?></label>

								<input class="col-md-12 form-control form-control-lg m-0"
									name="destination" disabled
									value="<?php echo $this->common->get_field_name("name","category",array("id"=>$product_info['product_category']));?>"
									size="16" type="text" />

							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="product_view" class="card float-right col-md-8 py-4 mb-4"
				style="display: none">
				<div class="card pb-4 px-0">
					<h3 class="bg-secondary text-light p-2 rounded-top"><?php echo gettext('Product Details');?></h3>
					<div class="row px-4">
						<div class='col-md-6 form-group'>
							<label class="col-md-12 no-padding control-label"><?php echo gettext('Name');?></label>
							<input class="col-md-12 form-control form-control-lg m-0"
								value="<?php echo (isset($product_info['name']))?$product_info['name']:'' ?>"
								size="16" type="text" disabled />
						</div>
						<div class='col-md-6 form-group'>
							<label class="col-md-12 no-padding control-label"><?php echo gettext('Billing Type');?></label>
							<select name="billing_type"
								class="col-md-12 form-control selectpicker form-control-lg"
								data-live-search='true' datadata-live-search-style='begins'
								disabled>
                 		<?php if(isset($product_info)){ ?>
                 			<option value="0"
									<?php if($product_info['billing_type'] == '0'){ ?>
									selected="selected" <?php } ?>><?php echo gettext('One Time');?></option>
								<option value="1"
									<?php if($product_info['billing_type'] == '1'){ ?>
									selected="selected" <?php } ?>><?php echo gettext('Recurring');?></option>
                 		<?php }else{ ?>
                 			<option value="0"><?php echo gettext('One Time');?></option>
								<option value="1"><?php echo gettext('Recurring');?></option>

                 		<?php } ?>
                 	</select>
						</div>
						<div class='col-md-6 form-group'>
							<label class="col-md-12 no-padding control-label"><?php echo gettext('Billing Days');?></label>
							<input class="col-md-12 form-control form-control-lg m-0"
								name="billing_days"
								value="<?php echo (isset($product_data['billing_days']))?$product_data['billing_days']:$product_info['billing_days'] ?>"
								size="16" type="text" />
							<div class="tooltips error_div pull-left no-padding"
								id="billing_days_error_div" style="display: none;">
								<i
									style="color: #D95C5C; padding-right: 6px; padding-top: 10px;"
									class="fa fa-exclamation-triangle"></i><span
									class="popup_error error  no-padding" id="billing_days_error"></span>
							</div>
						</div>
						<div class='col-md-6 form-group'>
							<label class="col-md-12 no-padding control-label"><?php echo gettext('Price');?></label>
							<input class="col-md-12 form-control form-control-lg m-0"
								name="price"
								value="<?php echo (isset($product_data['price']))?$product_data['price']:$product_info['price'] ?>"
								size="16" type="text" />
							<div class="tooltips error_div pull-left no-padding"
								id="price_error_div" style="display: none;">
								<i
									style="color: #D95C5C; padding-right: 6px; padding-top: 10px;"
									class="fa fa-exclamation-triangle"></i><span
									class="popup_error error  no-padding" id="price_error"> </span>
							</div>
						</div>
						<div class='col-md-6 form-group'>
							<label class="col-md-12 no-padding control-label"><?php echo gettext('Setup Fee');?></label>
							<input class="col-md-12 form-control form-control-lg m-0"
								name="setup_fee"
								value="<?php echo (isset($product_data['setup_fee']))?$product_data['setup_fee']:$product_info['setup_fee'] ?>"
								size="16" type="text" />
							<div class="tooltips error_div pull-left no-padding"
								id="setup_fee_error_div" style="display: none;">
								<i
									style="color: #D95C5C; padding-right: 6px; padding-top: 10px;"
									class="fa fa-exclamation-triangle"></i><span
									class="popup_error error  no-padding" id="setup_fee_error"> </span>
							</div>
						</div>
                 <?php if($product_info['product_category'] == 3){?> 
                 	<div class='col-md-6 form-group'>
							<label class="col-md-12 no-padding control-label"><?php echo gettext('Free Minutes');?></label>
							<input class="col-md-12 form-control form-control-lg m-0"
								name="free_minutes"
								value="<?php echo (isset($product_data['free_minutes']))?$product_data['free_minutes']:$product_info['free_minutes'] ?>"
								size="16" type="text" />
						</div>
                 <?php } ?>
                 <?php if($product_info['product_category'] == 2){?>
                 	<div class='col-md-6 form-group'>
							<label class="col-md-12 no-padding pr-form-control control-label"><?php echo gettext('Quantity');?></label>
							<input class="col-md-12 form-control form-control-lg m-0"
								name="quantity"
								value="<?php echo (isset($product_data['quantity']))?$product_data['quantity']:'' ?>"
								size="16" type="text" />
							<div class="tooltips error_div pull-left no-padding"
								id="quantity_error_div" style="display: none;">
								<i
									style="color: #D95C5C; padding-right: 6px; padding-top: 10px;"
									class="fa fa-exclamation-triangle"></i><span
									class="popup_error error  no-padding" id="quantity_error"> </span>
							</div>
						</div>
                 <?php } ?>
                 <div class='col-md-12'>
							<label> <input class="align-middle" type="checkbox"
								name="email_notify" value="1"
								<?php if(isset($product_data['email_notify']) && $product_data['email_notify']==1){ ?>
								checked <?php } ?>> <span class="align-middle"><?php echo gettext('Email Notification');?></span>
							</label>
						</div>
						<div class="col-md-12 my-4">
							<div class="col-md-6 float-left">
								<button class="btn btn-success btn-block" name="order_now"
									id="order_now" value="Order Now" type="button"> <?php echo gettext('Order Now');?> </button>
							</div>
							<div class="col-md-6 float-left">
								<button class="btn btn-secondary mx-2 btn-block" name="cancel"
									onclick="return redirect_page('/accounts/customer_product/<?php echo  $account_arr['id'] ?>')"
									value="Cancel" type="button"> <?php echo gettext('Cancel');?> </button>
							</div>
						</div>
					</div>
				</div>
		
		</form>
     <?php
    if (isset($validation_errors) && $validation_errors != '') {
        ?>
     	<script>
     		var ERR_STR = '<?php echo $validation_errors; ?>';
     		print_error(ERR_STR);
     	</script>
     <? } ?>

 </div>
</section>
<script>



	jQuery(document).ready(function() {  
	$("#product_view").css("display", "block");
	$("#order_now").click(function(){  
		$('#ordres_assign_form').attr('action', "<?php echo base_url();?>accounts/customer_orders_save/");
		$('#ordres_assign_form').submit();

	});
	
});
</script>


<? endblock() ?>  
<? end_extend() ?> 

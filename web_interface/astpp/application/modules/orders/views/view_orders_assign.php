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
						<h3 class="bg-secondary text-light p-2 rounded-top"><?php echo gettext("Basic Information"); ?></h3>
						<div class="row px-4">
							<div class='col-md-12 form-group'>
								<label class="col-md-12 p-0 control-label"><?php echo gettext("Category"); ?></label>
			
			<?php

$category = array(
    "id" => "category",
    "name" => "category",
    "class" => "category"
);
$categoryinfo = $this->db_model->getSelect("GROUP_CONCAT(id) as id", "category", "code NOT IN ('REFILL','DID')");

if ($categoryinfo->num_rows > 0) {
    $categoryinfo = $categoryinfo->result_array()[0]['id'];
    $where_arr['where'] = $this->db->where("id IN (" . $categoryinfo . ")", NULL, false);
    $category_list = $this->db_model->build_dropdown("id,name,code", "category", "", $where_arr);
    echo form_dropdown($category, $category_list, isset($add_array['category']) ? $add_array['category'] : '', '');
}
?>
      
                  </div>
							<div class='col-md-12 form-group'>
								<label class="col-md-12 p-0 control-label"><?php echo gettext("Reseller"); ?></label>
			  <?php
    if ($this->session->userdata('logintype') == 1) {
        $reseller_list = $this->db_model->build_concat_select_dropdown("id,first_name,number", "accounts", "", array(
            "type" => 1,
            "status" => 0,
            "deleted" => 0,
            "reseller_id" => $accountinfo['id']
        ), "");
    } else {
        $reseller_list = $this->db_model->build_concat_select_dropdown("id,first_name,number", "accounts", "", array(
            "type" => 1,
            "status" => 0,
            "deleted" => 0
        ), "");
    }
    $reseller_id = array(
        "id" => "reseller_id",
        "name" => "reseller_id",
        "class" => "reseller_id"
    );
    echo form_dropdown($reseller_id, $reseller_list, isset($add_array['reseller_id']) ? $add_array['reseller_id'] : '', '');
    ?>
			
                                
                  </div>

							<div class='col-md-12 form-group'>
								<label class="col-md-12 p-0 control-label"><?php echo gettext("Accounts"); ?></label>
								<div
									class="col-md-12 form-control selectpicker form-control-lg p-0">
			  <?php

    $account_add = array(
        "id" => "accountid",
        "name" => "accountid",
        "class" => "accountid"
    );
    echo form_dropdown($account_add, $accounts_list, isset($add_array['accountid']) ? $add_array['accountid'] : '', "");
    ?>				
			</div>
								<div class="tooltips error_div pull-left no-padding"
									id="accountid_error_div" style="display: none;">
									<i class="fa fa-exclamation-triangle error_triangle"></i><span
										class="popup_error error  no-padding" id="accountid_error"> </span>
								</div>

							</div>
							<div class='col-md-12 form-group'>
								<label class="col-md-12 p-0 control-label"><?php echo gettext("Product"); ?></label>
								<div
									class="col-md-12 form-control selectpicker form-control-lg p-0">
			  <?php
    if (isset($add_array['accountid']) && $add_array['accountid'] != '0') {
        $product_item = array(
            "id" => "product_id",
            "name" => "product_id",
            "class" => "product_id"
        );
        echo form_dropdown_all($product_item, $product_item_list, isset($add_array['product_id']) ? $add_array['product_id'] : '', '');
        ?>
			<?php

} else {
        $product_item = array(
            "id" => "product_id",
            "name" => "product_id",
            "class" => "product_id"
        );
        echo form_dropdown_all($product_item, $product_item_list, "", '');
        ?>

			<?php } ?>
			
			</div>
								<div class="tooltips error_div pull-left no-padding"
									id="product_id_error_div" style="display: none;">
									<i class="fa fa-exclamation-triangle error_triangle"></i><span
										class="popup_error error  no-padding" id="product_id_error"> </span>
								</div>
							</div>
							<div class='col-md-12 form-group'>
								<label class="col-md-12 p-0 control-label"><?php echo gettext("Payment Method"); ?></label>

								<select name="payment_by"
									class="col-md-12 form-control selectpicker ">
									<option value="0"><?php echo gettext("Account Balance"); ?></option>
								</select>

							</div>
						</div>
					</div>
				</div>
			</div>
			<div></div>

			<div id="product_view" class="card float-right col-md-8 py-4 mb-4"
				style="display: none">
				<div class="card pb-4 px-0">
					<h3 class="bg-secondary text-light p-2 rounded-top"><?php echo gettext("Product Details"); ?></h3>
					<div class="row px-4">
						<div class='col-md-6 form-group'>
							<label class="col-md-12 no-padding control-label"><?php echo gettext("Name"); ?></label>
							<input
								class="col-md-12 form-control pr-form-control form-control-lg m-0"
								value="<?php echo (isset($product_info['name']))?$product_info['name']:$add_array['product_name']; ?>"
								name="product_name" readonly size="16" type="text" />
						</div>
		<?php if($this->session->userdata ( 'logintype' ) == '-1' || $this->session->userdata ( 'logintype' ) == '2' || ($this->session->userdata ( 'logintype' ) == 1  && $accountinfo['is_distributor'] == 1)){ ?>
		<div class='col-md-6 form-group'>
							<label class="col-md-12 no-padding control-label"><?php echo gettext("Commission")." (%)"; ?></label>
							<input
								class="col-md-12 form-control pr-form-control form-control-lg m-0"
								name="commission"
								value="<?php echo (isset($product_info['commission']))?$product_info['commission']:$add_array['commission']; ?>"
								size="16" type="text" readonly />
						</div>
		<?php } ?>
		<?php if($this->session->userdata ( 'logintype' ) == '-1' || $this->session->userdata ( 'logintype' ) == '2'){?>
		 <div class='col-md-6 form-group'>
							<label class="col-md-12 no-padding  control-label"><?php echo gettext("Billing Type"); ?></label>
							<select name="billing_type"
								class="col-md-12 form-control pr-form-control selectpicker form-control-lg"
								data-live-search='true' datadata-live-search-style='begins'
								disabled>
			<?php if(isset($product_info)){ ?>
                       <option value="0"
									<?php if($product_info['billing_type'] == '0'){ ?>
									selected="selected" <?php } ?>><?php echo gettext("One Time"); ?></option>
								<option value="1"
									<?php if($product_info['billing_type'] == '1'){ ?>
									selected="selected" <?php } ?>><?php echo gettext("Recurring"); ?></option>
			<?php }else{ ?>
				<option value="0"><?php echo gettext("One Time"); ?></option>
								<option value="1"><?php echo gettext("Recurring"); ?></option>

			<?php } ?>
                      </select>
						</div>
		<?php }else{ ?>
		 <div class='col-md-6 form-group'>
							<label class="col-md-12 no-padding control-label"><?php echo gettext("Billing Type"); ?></label>
							<select
								class="col-md-12 form-control selectpicker pr-form-control  form-control-lg"
								disabled data-live-search='true'
								datadata-live-search-style='begins'>
			<?php if(isset($product_info)){ ?>
                       <option value="0"
									<?php if($product_info['billing_type'] == '0'){ ?>
									selected="selected" <?php } ?>><?php echo gettext("One Time"); ?></option>
								<option value="1"
									<?php if($product_info['billing_type'] == '1'){ ?>
									selected="selected" <?php } ?>><?php echo gettext("Recurring"); ?></option>
			<?php }else{ ?>
				<option value="0"><?php echo gettext("One Time"); ?></option>
								<option value="1"><?php echo gettext("Recurring"); ?></option>

			<?php } ?>
                      </select>
						</div>

		<?php }?>
		 <div class='col-md-6 form-group'>
							<label class="col-md-12 no-padding control-label"><?php echo gettext("Billing Days"); ?></label>
							<input
								class="col-md-12 form-control pr-form-control form-control-lg m-0"
								name="billing_days"
								value="<?php echo (isset($product_info['billing_days']) )?$product_info['billing_days']:$add_array['billing_days']; ?>"
								size="16" type="text" />
						</div>
		 <?php if($this->session->userdata ( 'logintype' ) ==1 && $accountinfo['is_distributor'] == 1 ){ ?>
		 <div class='col-md-6 form-group'>
							<label class="col-md-12 no-padding control-label"><?php echo gettext("Setup Fee"); ?> <?php echo '('.$currency.')'; ?> </label>
							<input
								class="col-md-12 form-control pr-form-control form-control-lg m-0"
								name="setup_fee" readonly
								value="<?php echo (isset($product_info['setup_fee']) )?$this->common->convert_to_currency ( '', '', $product_info['setup_fee'] ):$add_array['setup_fee']; ?>"
								size="16" type="text" />
							<div class="tooltips error_div pull-left no-padding"
								id="setup_fee_error_div" style="display: none;">
								<i class="fa fa-exclamation-triangle error_triangle"></i><span
									class="popup_error error  no-padding" id="setup_fee_error"> </span>
							</div>
						</div>
						<div class='col-md-6 form-group'>
							<label class="col-md-12 no-padding control-label"><?php echo gettext("Price"); ?> <?php echo '('.$currency.')'; ?></label>
							<input
								class="col-md-12 form-control pr-form-control form-control-lg m-0"
								readonly name="price"
								value="<?php echo (isset($product_info['price']))?$this->common->convert_to_currency ( '', '', $product_info['price'] ):$add_array['price'] ?>"
								size="16" type="text" />
							<div class="tooltips error_div pull-left no-padding"
								id="price_error_div" style="display: none;">
								<i class="fa fa-exclamation-triangle error_triangle"></i><span
									class="popup_error error  no-padding" id="price_error"> </span>
							</div>
						</div>
		<?php }else { ?>
		 <div class='col-md-6 form-group'>
							<label class="col-md-12 no-padding control-label"><?php echo gettext("Setup Fee"); ?> <?php echo '('.$currency.')'; ?> </label>
							<input
								class="col-md-12 form-control pr-form-control form-control-lg m-0"
								name="setup_fee"
								value="<?php echo (isset($product_info['setup_fee']) )?$this->common->convert_to_currency ( '', '', $product_info['setup_fee'] ):$add_array['setup_fee']; ?>"
								size="16" type="text" />
							<div class="tooltips error_div pull-left no-padding"
								id="setup_fee_error_div" style="display: none;">
								<i class="fa fa-exclamation-triangle error_triangle"></i><span
									class="popup_error error  no-padding" id="setup_fee_error"> </span>
							</div>
						</div>
						<div class='col-md-6 form-group'>
							<label class="col-md-12 no-padding control-label"><?php echo gettext("Price"); ?> <?php echo '('.$currency.')'; ?></label>
							<input
								class="col-md-12 form-control pr-form-control form-control-lg m-0"
								name="price"
								value="<?php echo (isset($product_info['price']))?$this->common->convert_to_currency ( '', '', $product_info['price'] ):$add_array['price'] ?>"
								size="16" type="text" />
							<div class="tooltips error_div pull-left no-padding"
								id="price_error_div" style="display: none;">
								<i class="fa fa-exclamation-triangle error_triangle"></i><span
									class="popup_error error  no-padding" id="price_error"> </span>
							</div>
						</div>



		<?php } ?>
		   <?php if($category_list == "Package"){?> 
			<div class='col-md-6 form-group'>
							<label class="col-md-12 no-padding control-label"><?php echo gettext("Free Minutes"); ?></label>
							<input
								class="col-md-12 form-control pr-form-control form-control-lg m-0"
								name="free_minutes"
								value="<?php echo (isset($product_info['free_minutes']))?$product_info['free_minutes']:'' ?>"
								size="16" type="text" />
						</div>
		  <?php } ?>
		<?php if($add_array['category'] == 2){?>
		<div class='col-md-6 form-group'>
							<label class="col-md-12 no-padding pr-form-control control-label"><?php echo gettext("Quantity"); ?></label>
							<input class="col-md-12 form-control form-control-lg m-0"
								name="quantity" value="" size="16" type="text" />
						</div>
		<?php } ?>
		<div class='col-md-12'>
							<label> <input class="align-middle" type="checkbox"
								name="email_notify" value=1> <span class="align-middle"><?php echo gettext("Email Notification"); ?></span>
							</label>
						</div>
						<div class="col-md-12 my-4">
							<div class="col-md-6 float-left">
								<button class="btn btn-success btn-block" name="order_now"
									id="order_now" value="Order Now" type="button"> <?php echo gettext("Order Now"); ?> </button>
							</div>
							<div class="col-md-6 float-left">
								<button class="btn btn-secondary mx-2 btn-block" name="cancel"
									onclick="return redirect_page('/orders/orders_list/')"
									value="Cancel" type="button"> <?php echo gettext("Cancel"); ?> </button>
							</div>
						</div>



					</div>
					<div></div>
		
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
function build_customer_dropdown(resellerid,accountid){
	 url = "<?= base_url().'orders/orders_reseller_accounts_dependency_dropdown/'?>";
	 $.ajax({
		type:'POST',
		url: url,
		data:{reseller_id:resellerid,accountid:accountid}, 
		success: function(response) {  
			$("#accountid").html(response); 
			$('.selectpicker').selectpicker('refresh'); 
		}
	});

}
function build_product_dropdown(category_id,resellerid,accountid,product_id){ 
	var url = "<?= base_url().'orders/orders_get_availbale_product_lists/'?>";
	$.ajax({
		type:'POST',
		url: url,
		data:{reseller_id:resellerid,accountid:accountid,category_id:category_id,productid:product_id}, 
		success: function(response) {  
			$("#product_id").html(response); 
			$('.selectpicker').selectpicker('refresh'); 
		}
	});
}
	var product_id = "<?php echo  $add_array['product_id'] ?>";
	var category_id = "<?php echo  $add_array['category'] ?>";
	var reseller_id = "<?php echo  $add_array['reseller_id'] ?>";
	var account_id = "<?php echo  $add_array['accountid'] ?>";

 jQuery(document).ready(function() {  
	build_customer_dropdown(reseller_id,account_id);
	build_product_dropdown(category_id,reseller_id,account_id,product_id);
	$("#product_view").css("display", "block");
	$("#product_id").change(function(){ //alert(1);
		$('#ordres_assign_form').attr('action', "<?php echo base_url();?>orders/orders_add/");
		$('#ordres_assign_form').submit();
	});
	$("#category").change(function(){ 
		document.getElementById("product_id").value = "";
		$('#ordres_assign_form').attr('action', "<?php echo base_url();?>orders/orders_add/");
		$('.pr-form-control').val('');        
		$('#ordres_assign_form').submit();
	});
	$("#reseller_id").change(function(){ 
		var resellerid = $("#reseller_id").val();
			
		build_customer_dropdown(resellerid,'');
		build_product_dropdown(category_id,resellerid,account_id,product_id);
	});
	$("#accountid").change(function(){ 
		var accountid = $("#accountid").val();
		var resellerid = $("#reseller_id").val();
		var category_id = $("#category").val();

		build_product_dropdown(category_id,resellerid,accountid,'');
      });
	$("#order_now").click(function(){ 
		$('#ordres_assign_form').attr('action', "<?php echo base_url();?>orders/orders_save/");
		$('#ordres_assign_form').submit();

	});

});
</script>


<? endblock() ?>  
<? end_extend() ?> 

<? extend('master.php') ?>
<? startblock('extra_head') ?>

<? endblock() ?>

<? startblock('page-title') ?>
<?= $page_title ?>
<? endblock() ?>

<? startblock('content') ?>

<section class="slice color-three pb-4">
	<div id="floating-label" class="w-section inverse p-0">
		<form action="<?php echo base_url().'orders/orders_save/'; ?>"
			accept-charset="utf-8" id="ordres_assign_form" method="POST"
			name="ordres_assign_form">
			<div class="col-md-4 float-left pl-0">
				<div class="card p-4">
					<div class="card pb-4 px-0">
						<h3 class="bg-secondary text-light p-2 rounded-top"><?php echo gettext("Basic Information"); ?></h3>
						<div class="row px-4">
							<div class='col-md-12 form-group'>
								<label class="col-md-12 p-0 control-label"><?php echo gettext("Select Product"); ?></label>
								<div
									class="col-md-12 form-control selectpicker form-control-lg p-0">
			  <?php
    $product_item = array(
        "id" => "product_id",
        "name" => "product_id",
        "class" => "product_id"
    );

    echo form_dropdown_all($product_item, $product_item_list, $add_array['product_id'], '');
    ?>
			</div>

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
    echo form_dropdown($account_add, $accounts_list, $add_array['accounts']);
    ?>
								
			</div>

							</div>

							<div class='col-md-12 form-group'>
								<label class="col-md-12 p-0 control-label"><?php echo gettext("Category"); ?></label>
			  <?php
    $category = array(
        "id" => "category_id",
        "name" => "category_id",
        "class" => "category_id"
    );
    echo form_dropdown($category, $category_list, $add_array['category']);
    ?>
			</div>
							--> <input class="col-md-12 form-control form-control-lg m-0"
								name="category" readonly
								value="<?php echo (isset($category_list))?$category_list:'' ?>"
								size="16" type="text" />
						</div>
						<div class='col-md-12 form-group'>
							<label class="col-md-12 p-0 control-label"><?php echo gettext("Payment By"); ?></label>
							<select name="payment_by"
								class="col-md-12 form-control selectpicker  form-control-lg"
								data-live-search='true' datadata-live-search-style='begins'>
								<option value="0"><?php echo gettext("Account Balance"); ?></option>
								<option value="1"><?php echo gettext("Paypal"); ?></option>
								<option value="2"><?php echo gettext("Card Payment");?></option>
							</select>
						</div>
					</div>
				</div>
			</div>
	
	</div>
	<div id="package_view" class="card float-right col-md-8 py-4 mb-4">
		<div class="card pb-4 px-0">
			<h3 class="bg-secondary text-light p-2 rounded-top"><?php echo gettext("Product Details"); ?></h3>
			<div class="row px-4">


				<div class='col-md-6 form-group'>
					<label class="col-md-12 no-padding control-label"><?php echo gettext("Name");?></label>
					<input class="col-md-12 form-control form-control-lg m-0"
						value="<?php echo (isset($product_info['name']))?$product_info['name']:'' ?>"
						name="product_name" size="16" type="text" />
				</div>
                                  <?php
                                $product_add = array(
                                    "id" => "product_category",
                                    "name" => "product_category",
                                    "class" => "product_category"
                                );
                                echo form_dropdown($product_add, $category_list, $product_info['product_category'], '');
                                ?>
			</div>
		</div>
		-->
		<div class='col-md-6 form-group'>
			<label class="col-md-12 no-padding control-label"><?php echo gettext("Commission")."(%)"; ?></label>
			<input class="col-md-12 form-control form-control-lg m-0"
				name="commission"
				value="<?php echo (isset($product_info['commission']))?$product_info['commission']:'' ?>"
				size="16" type="text" />
		</div>
		<div class='col-md-6 form-group'>
			<label class="col-md-12 no-padding control-label"><?php echo gettext("Duration"); ?></label>
			<input class="col-md-12 form-control form-control-lg m-0"
				name="duration"
				value="<?php echo (isset($product_info['duration']))?$product_info['duration']:'' ?>"
				size="16" type="text" />
		</div>
		<div class='col-md-6 form-group'>
			<label class="col-md-12 no-padding control-label"><?php echo gettext("Cost"); ?></label>
			<input class="col-md-12 form-control form-control-lg m-0" name="cost"
				value="<?php echo (isset($product_info['cost']))?$product_info['cost']:'' ?>"
				size="16" type="text" />
		</div>
		   <?php if($category_list == "Package"){?> 
			<div class='col-md-6 form-group'>
			<label class="col-md-12 no-padding control-label"><?php echo gettext("Free Minutes"); ?></label>
			<input class="col-md-12 form-control form-control-lg m-0"
				name="free_minutes"
				value="<?php echo (isset($product_info['free_minutes']))?$product_info['free_minutes']:'' ?>"
				size="16" type="text" />
		</div>
		  <?php } ?>
		 <div class="col-md-12 my-4">
			<button class="btn btn-info btn-block" name="order_now"
				value="Order Now" type="submit">
				<i class="fa fa-plus-square-o"></i> <?php echo gettext("Order Now"); ?> </button>
		</div>



	</div>
	<div></div>
	</form>


	</div>
</section>
<script>


jQuery(document).ready(function() {  
	$("#product_id").change(function(){
		$('#ordres_assign_form').attr('action', "<?php echo base_url();?>orders/orders_add/");
		$('#ordres_assign_form').submit();
	});
	/*$("#category").change(function(){ 
		$('#ordres_assign_form').attr('action', "<?php echo base_url();?>orders/orders_add/");
		$('#ordres_assign_form').submit();
	});
	$("#accounts").change(function(){ 
		$('#ordres_assign_form').attr('action', "<?php echo base_url();?>orders/orders_add/");
		$('#ordres_assign_form').submit();
	});*/
});
</script>


<? endblock() ?>  
<? end_extend() ?> 

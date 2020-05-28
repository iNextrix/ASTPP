<? extend('master.php') ?>
<? startblock('extra_head') ?>
<? endblock() ?>
<? startblock('page-title') ?>
<?= $page_title ?>
<? endblock() ?>
<? startblock('content') ?>        
<style>
.bootstrap-select.show-tick .dropdown-menu .selected span.check-mark {
    position: relative;
    float: right;
}
</style>

<section class="slice color-three">
  <div class="w-section inverse p-0">
      <div class="container">
        <div class="row">
        <span id="error_msg" class=" success"></span>
              <div class="portlet-content"  id="update_bar" style="cursor:pointer; display:none">
                      <?php echo $form_batch_update; ?>
              </div>
            </div>
        </div>
    </div>
</section>
<section class="slice color-three pb-4">
  <div id="floating-label" class="w-section inverse p-0">
	<form method="post" name="product_add_form" id="product_add_form" action="<?= base_url()."products/products_save/";?>">
	 <div class="col-md-4 float-left pl-0 pr-2">      
	  <div class="card float-left p-4">      
          <div class="card pb-4 px-0">
              <h3 class="bg-secondary text-light p-2 rounded-top"><?php echo gettext('Basic Information'); ?></h3>
		<div class="row px-4">
         <div class='col-md-12 form-group'>
                      <label class="col-md-12 p-0 control-label"><?php echo gettext('Product Category')?></label>
                      <div class="col-md-12 form-control selectpicker form-control-lg p-0" >
                       <?php $product_add = array("id" => "product_category", "name" => "product_category", "class" => "product_category");
                    echo form_dropdown($product_add, $product_category, isset($add_array['product_category'])?$add_array['product_category']:'','');?>
            </div>
                  </div>
         <div class='col-md-12 form-group'> 
            <label class="col-md-12 p-0 control-label"><?php echo gettext('Name'); ?> *</label>
            <input class="col-md-12 form-control form-control-lg m-0" name="product_name" value="<?php echo (isset($add_array['product_name']))?$add_array['product_name']:'' ?>" size="16" type="text"/>
			<div class="tooltips error_div pull-left no-padding" id="product_name_error_div" style="display: none;">
                <i style="color:#D95C5C; padding-right: 6px; padding-top: 10px;" class="fa fa-exclamation-triangle"></i>
                <span class="popup_error error  no-padding" id="product_name_error"></span>
            </div>	
         </div>
                  <div class='col-md-12 form-group'> 
                      <label class="col-md-12 p-0 control-label"><?php echo gettext('Description'); ?></label>
                      <input class="col-md-12 form-control form-control-lg m-0" name="product_description" value= "<?php echo (isset($add_array['product_description']))?$add_array['product_description']:'' ?>" size="16" type="textarea"/>
			
                  </div>
                  <div class='col-md-12 form-group'> 
                      <label class="col-md-12 p-0 control-label"><?php echo gettext('Buy Cost');?> (<?php echo ($currency)?>)</label>
                      <input class="col-md-12 form-control form-control-lg m-0" name="product_buy_cost" value= "<?php echo (isset($add_array['product_buy_cost']))?$add_array['product_buy_cost']:'' ?>"  size="16" type="text"/>
                  </div>
                  <div class='col-md-12 form-group'> 
                    <label class="col-md-12 p-0 control-label"><?php echo gettext('Reports'); ?></label>
                    <select name="resources[]" multiple='multiple' class='select field multiselectable col-md-12 form-control form-control-lg selectpicker'>
                        <?php foreach($resources as $item){
                            $checked = isset($add_array) && isset($add_array['resources']) && in_array($item['id'], $add_array['resources']) ? 'selected':'';
                        ?>
                        <option value="<?=$item['id']?>" <?=$checked?> ><?php echo gettext($item['description']); ?></option>
                    <?php }  ?>
                    </select>
                    <div class="tooltips error_div pull-left no-padding display_none" id="resources_error_div" >
                        <i style="color:#D95C5C; padding-left: 3px; padding-top: 10px;" class="fa fa-exclamation-triangle"></i>
                        <span class="popup_error error  no-padding" id="resources_error"></span>
                    </div>
                  </div>
                  <div class='col-md-12 form-group'>
                      <label class="col-md-12 p-0 control-label"><?php echo gettext('Can be purchased?'); ?></label>
                      <select  name="can_purchase" class="col-md-12 form-control selectpicker form-control-lg" data-live-search='true' datadata-live-search-style='begins'>
			<?php if(isset($add_array['can_purchase'])){ ?>
                         <option value="0" <?php if($add_array['can_purchase'] == '0'){ ?> selected="selected" <?php } ?>><?php echo gettext('Yes');?></option>
			<option value="1" <?php if($add_array['can_purchase'] == '1'){ ?> selected="selected" <?php } ?>><?php echo gettext('No');?></option>
			<?php } else { ?>
			 <option value="0"><?php echo gettext('Yes');?></option>
                         <option value="1"><?php echo gettext('No');?></option>
		 	 <?php } ?>
                      </select>
                  </div>
            	 <div class='col-md-12 form-group'>
                      <label class="col-md-12 p-0 control-label"><?php echo gettext('Status'); ?></label>
                      <select  name="status" class="col-md-12 form-control selectpicker form-control-lg" data-live-search='true' datadata-live-search-style='begins'>
			<?php if(isset($add_array['status'])){ ?>
                         <option value="0" <?php if($add_array['status'] == '0'){ ?> selected="selected" <?php } ?>><?php echo gettext('Active'); ?></option>
			<option value="1" <?php if($add_array['status'] == '1'){ ?> selected="selected" <?php } ?>><?php echo gettext('Inactive'); ?></option>
			<?php } else { ?>
			 <option value="0"><?php echo gettext('Active'); ?></option>
                         <option value="1"><?php echo gettext('Inactive'); ?></option>
		 	 <?php } ?>
                      </select>
                  </div>
              </div>
             </div>
           </div>
          </div>
		<div id="package_view" class="card float-right col-md-8 py-4 mb-4">      
		  <div class="card pb-4 px-0">
		     <h3 class="bg-secondary text-light p-2 rounded-top"><?php echo gettext('Product Details'); ?></h3>
		<div class="row px-4">
                  <div class='col-md-6 form-group'>
                      <label class="col-md-12 p-0 control-label"><?php echo gettext('Reseller can resell'); ?></label>
                      <select  name="can_resell" class="col-md-12 form-control selectpicker  form-control-lg" data-live-search='true' datadata-live-search-style='begins'>
			<?php if(isset($add_array['can_resell'])){ ?>
		                <option value="1" <?php if($add_array['can_resell'] == '1'){ ?> selected="selected" <?php } ?>><?php echo gettext('No');?></option>
		                <option value="0" <?php if($add_array['can_resell'] == '0'){ ?> selected="selected" <?php } ?>><?php echo gettext('Yes');?></option>
			<?php } else { ?>
				 <option value="1"><?php echo gettext('No'); ?></option>
		                 <option value="0"><?php echo gettext('Yes'); ?></option>
			<?php } ?>
                      </select>
                  </div>
                  <div class='col-md-6 form-group'> 
                      <label class="col-md-12 p-0 control-label"><?php echo gettext('Commission');?> (%)</label>
                      <input class="col-md-12 form-control form-control-lg m-0" name="commission" value="<?php echo (isset($add_array['commission']))?$add_array['commission']:'' ?>" size="16" type="text"/>
			<div class="tooltips error_div pull-left no-padding" id="commission_error_div" style="display: none;"><i style="color:#D95C5C; padding-right: 6px; padding-top: 10px;" class="fa fa-exclamation-triangle"></i><span class="popup_error error  no-padding" id="commission_error">   </span></div>	
                  </div>
		 <div class='col-md-6 form-group'> 
                      <label class="col-md-12 p-0 control-label"><?php echo gettext('Setup Fee').' ('.$currency.')'; ?></label>
                      <input class="col-md-12 form-control form-control-lg m-0" name="setup_fee" value="<?php echo (isset($add_array['setup_fee']))?$add_array['setup_fee']:'' ?>" size="16" type="text"/>	
			<div class="tooltips error_div pull-left no-padding" id="setup_fee_error_div" style="display: none;"><i style="color:#D95C5C; padding-right: 6px; padding-top: 10px;" class="fa fa-exclamation-triangle"></i><span class="popup_error error  no-padding" id="setup_fee_error">   </span></div>		
                  </div>
		   <div class='col-md-6 form-group'> 
                      <label class="col-md-12 p-0 control-label"><?php echo gettext('Price').' ('.$currency.')'; ?> *</label>
                      <input name="price" id="price" class="col-md-12 form-control form-control-lg m-0" value="<?php echo (isset($add_array['price']))?$add_array['price']:'' ?>" size="16" type="text"/>
			<div class="tooltips error_div pull-left no-padding" id="price_error_div" style="display: none;"><i style="color:#D95C5C; padding-right: 6px; padding-top: 10px;" class="fa fa-exclamation-triangle"></i><span class="popup_error error  no-padding" id="price_error">   </span></div>	
                  </div>
		
                  <div class='col-md-6 form-group'>
                      <label class="col-md-12 p-0 control-label"><?php echo gettext('Billing Type'); ?></label>
                      <select  name="billing_type" class="col-md-12 form-control selectpicker form-control-lg" data-live-search='true' datadata-live-search-style='begins'>
			<?php if(isset($add_array['billing_type'])){ ?>
				<option value="0" <?php if($add_array['billing_type'] == '0'){ ?> selected="selected" <?php } ?>><?php echo gettext('One Time');?></option>
		                <option value="1" <?php if($add_array['billing_type'] == '1'){ ?> selected="selected" <?php } ?>><?php echo gettext('Recurring');?></option>

			<?php }else { ?>
                        <option value="0"><?php echo gettext('One Time'); ?></option>
                        <option value="1"><?php echo gettext('Recurring'); ?></option>
			<?php } ?>
                      </select>
                  </div>
                  <div class='col-md-6 form-group'> 
                      <label class="col-md-12 p-0 control-label"><?php echo gettext('Billing Days'); ?> *</label>
                      <input class="col-md-12 form-control form-control-lg m-0" value="<?php echo (isset($add_array['billing_days']))?$add_array['billing_days']:'' ?>"  name="billing_days" size="16" type="text"/>
			<div class="tooltips error_div pull-left no-padding" id="billing_days_error_div" style="display: none;"><i style="color:#D95C5C; padding-right: 6px; padding-top: 10px;" class="fa fa-exclamation-triangle"></i><span class="popup_error error  no-padding" id="billing_days_error"></span></div>	
                  </div>
                  <div class='col-md-6 form-group'>
                      <label class="col-md-12 p-0 control-label"><?php echo gettext('Apply on existing accounts'); ?> *</label>
                      <select  name="apply_on_existing_account" class="col-md-12 form-control selectpicker form-control-lg" data-live-search='true' datadata-live-search-style='begins'>
			<?php if(isset($add_array['apply_on_existing_account'])){ ?>
                        	<option value="1" <?php if($add_array['apply_on_existing_account'] == '1'){ ?> selected="selected" <?php } ?>><?php echo gettext('No');?></option>
		                <option value="0" <?php if($add_array['apply_on_existing_account'] == '0'){ ?> selected="selected" <?php } ?>><?php echo gettext('Yes');?></option>
			<?php } else { ?>
				<option value="1"><?php echo gettext('No'); ?></option>
		                <option value="0"><?php echo gettext('yes'); ?></option>
			<?php } ?>
                      </select>
                  </div>
		 <div class='col-md-6 form-group'>
                      <label class="col-md-12 p-0 control-label"><?php echo gettext('Release if no balance'); ?></label>
			  <select  name="release_no_balance" class="col-md-12 form-control selectpicker form-control-lg" data-live-search='true' datadata-live-search-style='begins'>
				<?php if(isset($add_array['release_no_balance'])){ ?>
					<option value="1" <?php if($add_array['release_no_balance'] == '1'){ ?> selected="selected" <?php } ?>><?php echo gettext('No');?></option>
		                	<option value="0" <?php if($add_array['release_no_balance'] == '0'){ ?> selected="selected" <?php } ?>><?php echo gettext('Yes');?></option>

				<?php } else { ?>
		                	<option value="1"><?php echo gettext('No'); ?></option>
		               		<option value="0"><?php echo gettext('yes'); ?></option>
				<?php } ?>
                      </select>	      
                  </div>
 </form>
<?php
						if (isset($validation_errors) && $validation_errors != '') { ?>
						<script>
							var ERR_STR = '<?php echo $validation_errors; ?>';
							print_error(ERR_STR);
						</script>
					<? } ?>
                  <div class="col-md-12 my-4">
                    <div class="col-md-2 float-right">
                      <button class="btn btn-success btn-block" name="add_product" id="add_product" value="Add Product" type="button"> <?php echo gettext('Save'); ?></button>
                    </div>
                    <div class="col-md-2 float-right">
                      <button class="btn btn-secondary mx-2 btn-block" name="cancel" onclick="return redirect_page('/products/products_list/')" value="Cancel" type="button"> <?php echo gettext('Cancel'); ?> </button>
                    </div>                        
                  </div>
    </div>
  </div>
</section>
<script>
    jQuery(document).ready(function() {  
        $('#add_product').click(function () {
    	    $('#product_add_form').submit();
        });
    });
</script>

<? endblock() ?>  
<? end_extend() ?> 

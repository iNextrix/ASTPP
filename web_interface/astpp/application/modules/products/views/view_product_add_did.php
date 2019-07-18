<? extend('master.php') ?>
<? startblock('extra_head') ?>
<? endblock() ?>
<? startblock('page-title') ?>
<?= $page_title ?>
<? endblock() ?>
<? startblock('content') ?>        
<section class="slice color-three pb-4">
  <div id="floating-label" class="w-section inverse p-0">
	<form method="post" name="product_add_form" id="product_add_form" action="<?= base_url()."products/products_save/";?>">
	 <div class="col-md-4 float-left pl-0">      
	  <div class="card float-left p-4">      
          <div class="card pb-4 px-0">
              <h3 class="bg-secondary text-light p-2 rounded-top"><?php echo gettext('Basic Information') ?></h3>
		<div class="row px-4">
		 <div class='col-md-12 form-group'>
                      <label class="col-md-12 p-0 control-label"><?php echo gettext('Product Category')?></label>
                      <div class="col-md-12 form-control selectpicker form-control-lg p-0" >
					   <?php $product_add = array("id" => "product_category", "name" => "product_category", "class" => "product_category");
					echo form_dropdown($product_add, $product_category, isset($add_array['product_category'])?$add_array['product_category']:'','');?>
			</div>	
                  </div>
                  <div class='col-md-12 form-group'> 
                      <label class="col-md-12 p-0 control-label"><?php echo gettext('DID'); ?> *</label>
                      <input class="col-md-12 form-control form-control-lg m-0" name="number" value="<?php echo (isset($add_array['number']))?$add_array['number']:'' ?>" size="16" type="text"/>
			<div class="tooltips error_div pull-left no-padding" id="number_error_div" style="display: none;"><i class="fa fa-exclamation-triangle error_triangle"></i><span class="popup_error error  no-padding" id="number_error">  
 </span></div>	
                  </div>
                 <div class='col-md-12 form-group'>
                      <label class="col-md-12 p-0 control-label"><?php echo gettext('Provider')?></label>
                       <?php
								$accountinfo = $this->session->userdata ( "accountinfo" );
								$where = array("status"=>0,"deleted"=>0,"type"=>3);
								$provider_arr = array("id" => "provider_id", "name" => "provider_id", "class" => "provider_id");
								$provider = form_dropdown($provider_arr, $this->db_model->build_concat_dropdown("id,first_name,last_name,number", "accounts","",  $where),isset($add_array['provider_id'])?$add_array['provider_id']:'');

								echo $provider;
								?>
                  </div>

                  <div class='col-md-12 form-group'> 
                      <label class="col-md-12 p-0 control-label"><?php echo gettext('Country')?></label>
                      <?php
								$accountinfo = $this->session->userdata ( "accountinfo" );
								$country_arr = array("id" => "country_id", "name" => "country_id", "class" => "country_id");
								$country = form_dropdown($country_arr, $this->db_model->build_dropdown("id,country", "countrycode", "", ""), isset($add_array['country_id'])?$add_array['country_id']:$accountinfo['country_id']);
								echo $country;
								?>
			
                  </div>
		 <div class='col-md-12 form-group'> 
                      <label class="col-md-12 p-0 control-label"><?php echo gettext('Buy Cost') ?> (<?php echo ($currency)?>)</label>
                      <input class="col-md-12 form-control form-control-lg m-0" name="product_buy_cost" value= "<?php echo (isset($add_array['product_buy_cost']))?$add_array['product_buy_cost']:'' ?>"  size="16" type="text"/>
                  </div>

                  <div class='col-md-12 form-group'> 
                      <label class="col-md-12 p-0 control-label"><?php echo gettext('City')?> </label>
                      <input class="col-md-12 form-control form-control-lg m-0" name="city" value="<?php echo (isset($add_array['city']))?$add_array['city']:'' ?>" size="16" type="text"/>
                  </div>

                  <div class='col-md-12 form-group'>
                      <label class="col-md-12 p-0 control-label"><?php echo gettext('Province')?></label>
                     <input class="col-md-12 form-control form-control-lg m-0" name="province"  value="<?php echo (isset($add_array['province']))?$add_array['province']:'' ?>" size="16" type="text"/>
                  </div>
            	 
		 <div class='col-md-12 form-group'>
                      <label class="col-md-12 p-0 control-label"><?php echo gettext('Status') ?></label>
                      <select  name="status" class="col-md-12 form-control selectpicker form-control-lg" data-live-search='true' datadata-live-search-style='begins'>
                        <?php if(isset($add_array['status'])){ ?>
                         <option value="0" <?php if($add_array['status'] == '0'){ ?> selected="selected" <?php } ?>><?php echo gettext('Active'); ?></option>
			<option value="1" <?php if($add_array['status'] == '1'){ ?> selected="selected" <?php } ?>><?php echo gettext('Inactive'); ?></option>
			<?php } else { ?>
			 <option value="0"><?php echo gettext('Active')?></option>
                         <option value="1"><?php echo gettext('Inactive')?></option>
		 	 <?php } ?>
                      </select>
                  </div>
              </div>
             </div>
           </div>
          </div>
		<div id="did_view" class="card float-right col-md-8 py-4 mb-4">      
		  <div class="card pb-4 px-0">
		     <h3 class="bg-secondary text-light p-2 rounded-top"><?php echo gettext('Product Details') ?></h3>
		<div class="row px-4">
                  <div class='col-md-6 form-group'>
                      <label class="col-md-12 p-0 control-label"><?php echo gettext('Connection Cost').' ( '.$currency.' )'?></label>
                      <input class="col-md-12 form-control form-control-lg m-0" name="connectcost"  value="<?php echo (isset($add_array['connectcost']))?$add_array['connectcost']:'' ?>"  size="16" type="text"/>
			<div class="tooltips error_div pull-left no-padding" id="connectcost_error_div" style="display: none;"><i class="fa fa-exclamation-triangle error_triangle"></i><span class="popup_error error  no-padding" id="connectcost_error">  
 </span></div>	
                  </div>
                  <div class='col-md-6 form-group'> 
                      <label class="col-md-12 p-0 control-label"><?php echo gettext('Grace Time')." (Sec.)"; ?> </label>
                      <input class="col-md-12 form-control form-control-lg m-0" name="includedseconds" value="<?php echo (isset($add_array['includedseconds']))?$add_array['includedseconds']:'' ?>" size="16" type="text"/>
			<div class="tooltips error_div pull-left no-padding" id="includedseconds_error_div" style="display: none;"><i class="fa fa-exclamation-triangle error_triangle"></i><span class="popup_error error  no-padding" id="includedseconds_error">  
 </span></div>	
			
                  </div>
		<div class='col-md-6 form-group'>
                      <label class="col-md-12 p-0 control-label"><?php echo gettext('Cost/Min').' ('.$currency.') '; ?></label>
			<input class="col-md-12 form-control form-control-lg m-0" name="cost" value="<?php echo (isset($add_array['cost']))?$add_array['cost']:'' ?>" size="16" type="text"/>
			<div class="tooltips error_div pull-left no-padding" id="cost_error_div" style="display: none;"><i class="fa fa-exclamation-triangle error_triangle"></i><span class="popup_error error  no-padding" id="cost_error">  
 </span></div>	
                  </div>
                 
                  <div class='col-md-6 form-group'> 
                      <label class="col-md-12 p-0 control-label"><?php echo gettext('Initial Increment')?></label>
                      <input name="init_inc" id="init_inc" value="<?php echo (isset($add_array['init_inc']))?$add_array['init_inc']:'' ?>" class="col-md-12 form-control form-control-lg m-0"  size="16" type="text"/>
			<div class="tooltips error_div pull-left no-padding" id="init_inc_error_div" style="display: none;"><i class="fa fa-exclamation-triangle error_triangle"></i><span class="popup_error error  no-padding" id="init_inc_error">  
 </span></div>			
                  </div>
                  <div class='col-md-6 form-group'>
                      <label class="col-md-12 p-0 control-label"><?php echo gettext('Increment')?></label>
			 <input name="inc" id="inc" class="col-md-12 form-control form-control-lg m-0" value="<?php echo (isset($add_array['inc']))?$add_array['inc']:'' ?>"  size="16" type="text"/>
			<div class="tooltips error_div pull-left no-padding" id="inc_error_div" style="display: none;"><i class="fa fa-exclamation-triangle error_triangle"></i><span class="popup_error error  no-padding" id="inc_error">  
 </span></div>		
                  </div>

		<div class='col-md-6 form-group'> 
                      <label class="col-md-12 p-0 control-label"><?php echo gettext('Setup Fee').' ('.$currency.')'; ?></label>
                      <input class="col-md-12 form-control form-control-lg m-0"  name="setup_fee" value="<?php echo (isset($add_array['setup_fee']))?$add_array['setup_fee']:'' ?>" size="16" type="text"/>
				
                  </div>
		 <div class='col-md-6 form-group'>
                      <label class="col-md-12 p-0 control-label"><?php echo gettext('MonthlyFee').' ('.$currency.')'; ?> *</label>
                     <input class="col-md-12 form-control form-control-lg m-0" name="price" value="<?php echo (isset($add_array['price']))?$add_array['price']:'' ?>"  size="16" type="text"/>
			<div class="tooltips error_div pull-left no-padding" id="price_error_div" style="display: none;"><i class="fa fa-exclamation-triangle error_triangle"></i><span class="popup_error error  no-padding" id="price_error">   </span></div>
                  </div>
		 <div class='col-md-6 form-group'>
                      <label class="col-md-12 p-0 control-label"><?php echo gettext('Call Timeout')?> (Sec.)</label>
                     <input class="col-md-12 form-control form-control-lg m-0" name="leg_timeout" value="<?php echo (isset($add_array['leg_timeout']))?$add_array['leg_timeout']:'' ?>" size="16" type="text"/>
			<div class="tooltips error_div pull-left no-padding" id="leg_timeout_error_div" style="display: none;"><i class="fa fa-exclamation-triangle error_triangle"></i><span class="popup_error error  no-padding" id="leg_timeout_error">  
 </span></div>		
                  </div>
	
		  <div class='col-md-6 form-group'>
                      <label class="col-md-12 p-0 control-label"><?php echo gettext('Billing Type') ?></label>
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
                      <label class="col-md-12 p-0 control-label"><?php echo gettext('Billing Days')?> *</label>
                     <input class="col-md-12 form-control form-control-lg m-0" name="billing_days" size="16"  value="<?php echo (isset($add_array['billing_days']))?$add_array['billing_days']:'' ?>" type="text"/>
			<div class="tooltips error_div pull-left no-padding" id="billing_days_error_div" style="display: none;"><i class="fa fa-exclamation-triangle error_triangle"></i><span class="popup_error error  no-padding" id="billing_days_error"></span></div>	
                  </div>
		<div class='col-md-6 form-group'>
                      <label class="col-md-12 p-0 control-label"><?php echo gettext('Concurrent Calls')?></label>
                     <input class="col-md-12 form-control form-control-lg m-0" name="maxchannels" value="<?php echo (isset($add_array['maxchannels']))?$add_array['maxchannels']:'' ?>"  size="16" type="text"/>
                  </div>
 </form>
				<?php
						if (isset($validation_errors) && $validation_errors != '') { ?>
						<script>
							var ERR_STR = '<?php echo $validation_errors; ?>';
							print_error(ERR_STR);
						</script>
					<? } ?>
                   <div class="col-md-12">
                    <div class="col-md-6 float-left ">
                        <button class="btn btn-success btn-block btn-block" name="action" id ="action" value="Save" type="submit"><?php echo gettext('Save') ?> </button>
                    </div>
		   <div class="col-md-6 float-left">
                      <?if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] == "".base_url()."did/did_list/") { ?>
                      		<button class="btn btn-secondary mx-2 btn-block" name="cancel" onclick="return redirect_page('/did/did_list/')" value="Cancel" type="button"><?php echo gettext(' Cancel') ?> </button>
			<?} else{ ?>
				<button class="btn btn-secondary mx-2 btn-block" name="cancel" onclick="return redirect_page('/products/products_list/')" value="Cancel" type="button"> <?php echo gettext('Cancel') ?> </button>

			<?php } ?>
                    </div>    
                  </div>
    </div>
  </div>
</section>
<script>
jQuery(document).ready(function() { 
	$("#product_category").change(function(){ 
		$('#product_add_form').attr('action', "<?php echo base_url();?>products/products_add/");
		$('#product_add_form').submit();
	});
	$("#action").click(function(){ 
		var $myForm = $("#product_add_form");
		$myForm.submit(function(){
	   		 $myForm.submit(function(){
				return false;
   		 	});
		})
	});
})
</script>
<? endblock() ?>  
<? end_extend() ?> 

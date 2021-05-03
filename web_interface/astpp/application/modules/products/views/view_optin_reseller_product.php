<?php include(FCPATH.'application/views/popup_header.php'); ?>
<section class="slice color-three">
  <div id="floating-label" class="w-section inverse p-0">
	<div class="col-md-12 p-0 card-header">
	        <h3 class="fw4 p-4 m-0"><?php echo gettext('Edit Info') ?></h3>
	  </div>
	<form class="p-4" method="post" name="product_add_form" id="product_edit_form" action="<?= base_url()."products/products_reseller_option_save/";?>">
    <div class="card" id="floating-label">
    <div class="col-12 form-inline">
	 	  <h3 class="bg-secondary text-light p-3 rounded-top"><?php echo gettext('Product Information') ?></h3>
		<input class="col-md-12 form-control form-control-lg" name="productid" value="<?php echo $product_info['id']?>" type="hidden"/>

      <div class='col-md-12 p-4'>
        <div class="col-md-3 float-left text-center"> </div>
        <div class="col-md-4 float-left text-center font-weight-bold"><?php echo gettext('BUY COST') ?></div>
        <div class="col-md-5 float-left text-center font-weight-bold"><?php echo gettext('SELL COST') ?></div>
      </div>
	<input type="hidden" name="country_id" value="<?php echo (isset($product_info['country_id'])) ?  ($product_info['country_id']): '' ?>" />
	<div class='col-md-12 form-group'>
        <label class="col-md-3 p-0"><?php echo gettext('Product Category') ?></label>
        <div class="col-md-4 p-0">
           <input class="col-md-12 form-control form-control-lg" name="product_category" disabled value="<?php echo $this->common->get_field_name("name","category",array("id"=>$product_info['product_category'])) ?>" type="text"/>
        </div>
        <div class="col-md-5 pr-0">
	   <input class="col-md-12 form-control form-control-lg w-100" name="product_category2" disabled value="<?php echo $this->common->get_field_name("name","category",array("id"=>$product_info['product_category'])) ?>" type="text"/>      
        </div>
      </div>
      <div class='col-md-12 form-group'> 
        <label class="col-md-3 p-0"><?php echo gettext('Name') ?></label>
        <div class="col-md-4 p-0">
          <input class="col-md-12 form-control form-control-lg" disabled value="<?php echo (isset($product_info['name']))?$product_info['name']:'' ?>" name="product_name" type="text"/>
        </div>
        <div class="col-md-5 pr-0">
          <input class="col-md-12 form-control form-control-lg w-100" disabled value="<?php echo (isset($product_info['name']))?$product_info['name']:'' ?>" name="product_name" type="text"/>
        </div>
      </div>
      <div class='col-md-12 form-group'> 
        <label class="col-md-3 p-0"><?php echo gettext('Buy Cost') ?> (<?php echo ($currency)?>)</label>
        <div class="col-md-4 p-0">
          <input class="col-md-12 form-control form-control-lg" value= "<?php echo (isset($product_info['price']))?$this->common->convert_to_currency ( '', '', $product_info['price'] ):'' ?>" 	 type="text" disabled/>
        </div>
        <div class="col-md-5 pr-0">
         <input class="col-md-12 form-control form-control-lg w-100" value= "<?php echo (isset($product_info['price']))?$this->common->convert_to_currency ( '', '', $product_info['price'] ):'' ?>" name="product_buy_cost" type="text" readonly/>
        </div>
      </div>
      <?php if($this->session->userdata ( 'logintype' ) == 1  && $accountinfo['is_distributor'] == 1){ ?>
      <div class='col-md-12 form-group'> 
        <label class="col-md-3 p-0"><?php echo gettext('Commission')?> (%)</label>
        <div class="col-md-4 p-0">
          <input class="col-md-12 form-control form-control-lg" name="commission" value="<?php echo (isset($product_info['commission']))?$product_info['commission']:'' ?>" type="text" disabled/>
        </div>
        <div class="col-md-5 pr-0">
         <input class="col-md-12 form-control form-control-lg w-100" name="commission" value="<?php echo (isset($product_info['commission']))?$product_info['commission']:'' ?>" type="text" readonly/>
        </div>
      </div>
    <?php } ?>
<?php if($this->session->userdata ( 'logintype' ) == 1  && $accountinfo['is_distributor'] == 1){ ?>
      <div class='col-md-12 form-group'> 
        <label class="col-md-3 p-0"><?php echo gettext('Setup Fee') ?> (<?php echo ($currency)?>)</label>
        <div class="col-md-4 p-0">
          <input class="col-md-12 form-control form-control-lg"  value = "<?php echo  $this->common->convert_to_currency ( '', '', $product_info['setup_fee'] )?>" type="text" disabled/> 
        </div>
        <div class="col-md-5 pr-0">
	 <input class="col-md-12 form-control form-control-lg w-100"  name="setup_fee"  value = "<?php echo  $this->common->convert_to_currency ( '', '', $product_info['setup_fee'] )?>" type="text" readonly/> 
      
        </div>
      </div>
     <div class='col-md-12 form-group'> 
      <label class="col-md-3 p-0"><?php echo gettext('Price') ?> (<?php echo ($currency)?>)</label>
      <div class="col-md-4 p-0">
        <input class="col-md-12 form-control form-control-lg"  value="<?php echo (isset($product_info['price']))?$this->common->convert_to_currency ( '', '', $product_info['price'] ):'' ?>" type="text" disabled/>
      </div>
      <div class="col-md-5 pr-0">
        <input class="col-md-12 form-control form-control-lg w-100" name="price" value="<?php echo (isset($product_info['price']))?$this->common->convert_to_currency ( '', '', $product_info['price'] ):'' ?>" type="text" readonly/>
		
      </div>
    </div>     
<?php } else{ ?>
	<div class='col-md-12 form-group'> 
        <label class="col-md-3 p-0"><?php echo gettext('Setup Fee') ?> (<?php echo ($currency)?>)</label>
        <div class="col-md-4 p-0">
          <input class="col-md-12 form-control form-control-lg"  value = "<?php echo  $this->common->convert_to_currency ( '', '', $product_info['setup_fee'] )?>" type="text" disabled/> 
        </div>
        <div class="col-md-5 pr-0">
		<input class="col-md-12 form-control form-control-lg w-100"  name="setup_fee" id="setup_fees"  value = "<?php echo  $this->common->convert_to_currency ( '', '', $product_info['setup_fee'] )?>" type="text"/> 
			<div id="setup_fee_error" class="tooltips error_div float-left p-0"  style="display:block;"></div>
        </div>
      </div>
     <div class='col-md-12 form-group'> 
      <label class="col-md-3 p-0"><?php echo gettext('Price') ?> (<?php echo ($currency)?>)</label>
      <div class="col-md-4 p-0">
        <input class="col-md-12 form-control form-control-lg"  value="<?php echo (isset($product_info['price']))?$this->common->convert_to_currency ( '', '', $product_info['price'] ):'' ?>" type="text" disabled/>
      </div>
      <div class="col-md-5 pr-0">
        <input class="col-md-12 form-control form-control-lg w-100"  name= "price" id="prices" value="<?php echo (isset($product_info['price']))?$this->common->convert_to_currency ( '', '', $product_info['price'] ):'' ?>" type="text" />
		<div id="price_error" class="tooltips error_div float-left p-0"  style="display:block;"></div>
      </div>
    </div>     
<?php } ?>
      <div class='col-md-12 form-group'>
        <label class="col-md-3 p-0"><?php echo gettext('Billing Type') ?></label>
        <div class="col-md-4 p-0">
          <select  name="billing_type" class="form-control selectpicker form-control-lg"  disabled data-live-search='true' datadata-live-search-style='begins'>
           <option value="0" <?php if($product_info['billing_type'] == '0'){ ?> selected="selected" <?php } ?>><?php echo gettext('One Time')?></option>
           <option value="1" <?php if($product_info['billing_type'] == '1'){ ?> selected="selected" <?php } ?>><?php echo gettext('Recurring') ?></option>
         </select>
        </div>
       <div class="col-md-5 pr-0">
        <select  name="billing_type" class="form-control selectpicker form-control-lg"  disabled data-live-search='true' datadata-live-search-style='begins'>
           <option value="0" <?php if($product_info['billing_type'] == '0'){ ?> selected="selected" <?php } ?>><?php echo gettext('One Time') ?></option>
           <option value="1" <?php if($product_info['billing_type'] == '1'){ ?> selected="selected" <?php } ?>><?php echo gettext('Recurring') ?></option>
         </select>
       </div>
     </div>
     <div class='col-md-12 form-group'> 
      <label class="col-md-3 p-0"><?php echo gettext('Billing Days') ?></label>
      <div class="col-md-4 p-0">
        <input class="col-md-12 form-control form-control-lg" name="billing_days" value= "<?php echo (isset($product_info['billing_days']))?$product_info['billing_days']:'' ?>" type="text" disabled/>
      </div>
      <div class="col-md-5 pr-0">
         <input class="col-md-12 form-control form-control-lg w-100" name="billing_days" value= "<?php echo (isset($product_info['billing_days']))?$product_info['billing_days']:'' ?>" type="text" disabled/>
      </div>
    </div>
   
	<?php if($product_info['product_category'] == 1){ ?>
	<div class='col-md-12 form-group'> 
      <label class="col-md-3 p-0"><?php echo gettext('Free Minutes') ?></label>
      <div class="col-md-4 p-0">
        <input class="col-md-12 form-control form-control-lg" name="free_minutes" value="<?php echo (isset($product_info['free_minutes']))?$product_info['free_minutes']:'' ?>" type="text" disabled/>
      </div>
      <div class="col-md-5 pr-0">
         <input class="col-md-12 form-control form-control-lg w-100" name="free_minutes" value="<?php echo (isset($product_info['free_minutes']))?$product_info['free_minutes']:'' ?>" type="text" disabled/>
      </div>
    </div>
		<?php } ?>

  </div>
  </div>
  <div class="col-12 my-4">
    <div class="col-md-6 float-left">
     <button class="btn btn-success btn-block" name="action" value="Save" type="submit"><?php echo gettext('Save') ?></button>
   </div>
   <div class="col-md-6 float-left">
    <button class="btn btn-secondary btn-block mx-2" name="cancel" onclick="return redirect_page('/products/products_listing/')" value="Cancel" type="button">  <?php echo gettext('Cancel') ?> </button>
  </div>                        
</div>

   </form>	
 </div>
</section>

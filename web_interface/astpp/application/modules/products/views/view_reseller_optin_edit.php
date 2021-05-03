<? extend('master.php') ?>
<? startblock('extra_head') ?>
<? endblock() ?>
<? startblock('page-title') ?>
<?= $page_title ?>
<? endblock() ?>
<? startblock('content') ?>        
<section class="slice color-three">
  <div class="w-section inverse no-padding">
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
<section class="slice color-three padding-b-20">
  <div id="floating-label" class="w-section inverse no-padding">
	<form method="post" name="product_add_form" id="product_edit_form" action="<?= base_url()."products/products_reseller_optin_save/";?>">
	 <div class="col-md-4 float-left pl-0">      
	  <div class="card float-left p-4">      
          <div class="card pb-4 px-0">
              <h3 class="bg-secondary text-light p-2 rounded-top"><?php echo gettext("Basic Information");?></h3>
		<div class="row px-4">
		<input class="col-md-12 form-control form-control-lg m-0" name="product_id" value="<?php echo $optin_product['product_id']?>" size="16" type="hidden"/>
		 <div class='col-md-12 form-group'>
                      <label class="col-md-12 no-padding control-label"><?php echo gettext("Product Category");?></label>
                      <div class="col-md-12 form-control selectpicker form-control-lg p-0" >
                                 <input class="col-md-12 form-control form-control-lg m-0" value="<?php echo $this->common->get_field_name("name","category",array("id"=>$product_info['product_category']));?>" name="product_name" size="16" type="text" readonly/>
			</div>	
                  </div>

                  <div class='col-md-12 form-group'> 
                      <label class="col-md-12 no-padding control-label"><?php echo gettext("Name");?></label>
                      <input class="col-md-12 form-control form-control-lg m-0" value="<?php echo (isset($product_info['name']))?$product_info['name']:'' ?>" name="product_name" size="16" type="text" readonly/>
                  </div>

                 
                  <div class='col-md-12 form-group'> 
                      <label class="col-md-12 no-padding control-label"><?php echo gettext("Description"); ?></label>
                       <input class="col-md-12 form-control form-control-lg m-0" value= "<?php echo (isset($product_info['description']))?$product_info['description']:'' ?>" name="product_description" size="16" type="textarea" readonly/>
                  </div>

                  <div class='col-md-12 form-group'>
                      <label class="col-md-12 no-padding control-label"><?php echo gettext("Status"); ?></label>
                      <select  name="status" class="col-md-12 form-control selectpicker form-control-lg" data-live-search='true' datadata-live-search-style='begins' disabled>
                          <option value="0" <?php if($product_info['status'] == '0'){ ?> selected="selected" <?php } ?>><?php echo gettext("Active");?></option>
			<option value="1" <?php if($product_info['status'] == '1'){ ?> selected="selected" <?php } ?>><?php echo gettext("Inactive");?></option>
                      </select>
                  </div>

                  <div class='col-md-12 form-group'> 
                      <label class="col-md-12 no-padding control-label"><?php echo gettext("Buy Cost");?> (<?php echo ($currency)?>)</label>
                      <input class="col-md-12 form-control form-control-lg m-0" value= "<?php echo (isset($product_info['price']))?$this->common->convert_to_currency ( '', '', $optin_product['buy_cost'] ):'' ?>" name="product_buy_cost" size="16" type="text" readonly />
                  </div>

                 
            
              </div>
             </div>
           </div>
          </div>

		<div id="package_view" class="card float-right col-md-8 py-4 mb-4">      
		  <div class="card pb-4 px-0">
		     <h3 class="bg-secondary text-light p-2 rounded-top"><?php echo gettext("Product Details");?></h3>
		<div  class="row px-4">
		
                  <?php if($this->session->userdata ( 'logintype' ) == 1  && $accountinfo['is_distributor'] == 1){ ?>
                <div class='col-md-6 form-group'> 
                      <label class="col-md-12 no-padding control-label"><?php echo gettext("Commission");?> (%)</label>
                     <input class="col-md-12 form-control form-control-lg m-0" name="commission" value="<?php echo (isset($product_info['commission']))?$product_info['commission']:'' ?>" size="16" type="text" readonly/>
                  </div>
		<?php } ?>
                  <div class='col-md-6 form-group'>
                      <label class="col-md-12 no-padding control-label"><?php echo gettext("Billing Type");?></label>
                      <select  name="billing_type" class="col-md-12 form-control selectpicker form-control-lg" disabled data-live-search='true' datadata-live-search-style='begins'>
                       <option value="1" <?php if($product_info['billing_type'] == '0'){ ?> selected="selected" <?php } ?>><?php echo gettext("One Time");?></option>
			<option value="0" <?php if($product_info['billing_type'] == '1'){ ?> selected="selected" <?php } ?>><?php echo gettext("Recurring"); ?></option>
                      </select>
                  </div>
		 
			<div class='col-md-6 form-group'> 
                      <label class="col-md-12 p-0 control-label"><?php echo gettext('Setup Fee').' ('.$currency.')'; ?></label>
                      <input class="col-md-12 form-control form-control-lg m-0" name="setup_fee" value = "<?php echo  $this->common->convert_to_currency ( '', '', $optin_product['setup_fee'] )?>" size="16" type="text"/>
				
                  </div>
                  <div class='col-md-6 form-group'> 
                      <label class="col-md-12 no-padding control-label"><?php echo gettext("Billing Days");?></label>
                        <input class="col-md-12 form-control form-control-lg m-0" name="billing_days" value= "<?php echo (isset($product_info['billing_days']))?$product_info['billing_days']:'' ?>" size="16" type="text" readonly/>
                  </div>
		
               
                  
                  <div class='col-md-6 form-group'> 
                      <label class="col-md-12 no-padding control-label"><?php echo gettext("Price"); ?> (<?php echo ($currency)?>)</label>
                     <input class="col-md-12 form-control form-control-lg m-0" name="price" value= "<?php echo (isset($optin_product['price']))?$this->common->convert_to_currency ( '', '', $optin_product['price'] ):'' ?>" size="16" type="text"/>
                  </div>

                 
		<div class="col-md-12 my-4">
                    <div class="col-md-6 float-left">
                       <button class="btn btn-success btn-block" name="action" value="Save" type="submit"><?php echo gettext("Save"); ?> </button>
                    </div>
                    <div class="col-md-6 float-left">
			  <?php if($product_info['product_category'] == 4){ ?>
                      <button class="btn btn-secondary mx-2 btn-block" name="cancel" onclick="return redirect_page('/did/did_list/')" value="Cancel" type="button">  <?php echo gettext("Cancel");?> </button>
			<?php } else{ ?>
			 <button class="btn btn-secondary mx-2 btn-block" name="cancel" onclick="return redirect_page('/products/products_list/')" value="Cancel" type="button">  <?php echo gettext("Cancel");?> </button>
			<?php } ?>
                    </div>                        
                  </div>
		</div>
	 </form>
	</div>
      </section>

<? endblock() ?>  
<? end_extend() ?> 

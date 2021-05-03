<? extend('master.php') ?>
<? startblock('extra_head') ?>

<? endblock() ?>

<? startblock('page-title') ?>
<?= $page_title ?>
<? endblock() ?>

<? startblock('content') ?>     

<section class="slice color-three pb-4">
  <div id="floating-label" class="w-section inverse p-0">
   <form action="<?php echo base_url()."products/products_reseller_confirm_order/"; ?>" accept-charset="utf-8" id="ordres_assign_form" method="POST" name="ordres_assign_form">
     <div class="col-md-4 float-left pl-0">      
	 <div class="card p-4">      
          <div class="card pb-4 px-0">
              <h3 class="bg-secondary text-light p-2 rounded-top"><?php echo gettext("Basic Information"); ?></h3>
	        <div class="row px-4">
		<input class="col-md-12 form-control form-control-lg m-0" name="product_id"  value="<?php echo $product_data['id'] ?>" size="16" type="hidden"/>
		<input class="col-md-12 form-control form-control-lg m-0" name="account_id"  value="<?php echo $account_data['id'] ?>" size="16" type="text"/>				
		<div class='col-md-12 form-group'>
                      <label class="col-md-12 p-0 control-label"><?php echo gettext("Accounts");?></label>
                      <div class="col-md-12 form-control selectpicker form-control-lg p-0" >
			  <input class="col-md-12 form-control form-control-lg m-0" name="accountant_name" readonly value="<?php echo $account_data['first_name'].'('.$account_data['number'].')' ?>" size="16" type="text"/>
								
			</div>
                                
                  </div>

		<div class='col-md-12 form-group'>
                      <label class="col-md-12 p-0 control-label"><?php echo gettext("Category");?></label>
			<input class="col-md-12 form-control form-control-lg m-0" name="category" readonly value="<?php echo (isset($category_list))?$category_list:'' ?>" size="16" type="text"/>							
			</div>          
                  </div>
		<div class='col-md-12 form-group'>
                      <label class="col-md-12 p-0 control-label"><?php echo gettext("Payment By"); ?></label>
                       <select  name="payment_by" class="col-md-12 form-control selectpicker  form-control-lg" data-live-search='true' datadata-live-search-style='begins'>
                        <option value="0"><?php echo gettext("Account Balance");?></option>
                        <option value="1"><?php echo gettext("Paypal"); ?></option>
			 <option value="2"><?php echo gettext("Card Payment");?></option>
                      </select>          
                  </div>
      	   </div>
    	</div>
    </div>

 <div id="product_view" class="card float-right col-md-8 py-4 mb-4" >      
		  <div class="card pb-4 px-0">
		     <h3 class="bg-secondary text-light p-2 rounded-top"><?php echo gettext("Product Details"); ?></h3>
		  <div class="row px-4">
                

		<div class='col-md-6 form-group'> 
                      <label class="col-md-12 no-padding control-label"><?php echo gettext("Name"); ?></label>
                      <input class="col-md-12 form-control form-control-lg m-0" readonly value="<?php echo (isset($product_data['name']))?$product_data['name']:'' ?>" name="product_name" size="16" type="text"/>
                  </div>
		<div class='col-md-6 form-group'> 
                      <label class="col-md-12 no-padding control-label"><?php echo gettext("Commission"); ?>(%)</label>
                     <input class="col-md-12 form-control form-control-lg m-0" readonly name="commission" value="<?php echo (isset($product_data['commission']))?$product_data['commission']:'' ?>" size="16" type="text"/>
                  </div>
		 <div class='col-md-6 form-group'> 
                      <label class="col-md-12 no-padding control-label"><?php echo gettext("Billing Days");?></label>
                       <input class="col-md-12 form-control form-control-lg m-0" readonly name="billing_days" value= "<?php echo (isset($product_data['billing_days']))?$product_data['billing_days']:'' ?>" size="16" type="text"/>
                  </div>
		<div class='col-md-6 form-group'> 
                      <label class="col-md-12 no-padding control-label"><?php echo gettext("Price"); ?></label>
                      <input class="col-md-12 form-control form-control-lg m-0" readonly name="price" value= "<?php echo (isset($product_data['price']))?$product_data['price']:'' ?>" size="16" type="text"/>
                  </div>
		   <?php if($category_list == "Package"){?> 
			<div class='col-md-6 form-group'> 
                      <label class="col-md-12 no-padding control-label"><?php echo gettext("Free Minutes"); ?></label>
                      <input class="col-md-12 form-control form-control-lg m-0" readonly name="free_minutes" value= "<?php echo (isset($product_data['free_minutes']))?$product_data['free_minutes']:'' ?>" size="16" type="text"/>
                  </div>
		  <?php } ?>
		 <div class="col-md-12 my-4">
		  <div class="col-md-6 float-left">
                      <button class="btn btn-info btn-block" name="order_now" id="order_now" value="Order Now"  type="submit"> <i class="fa fa-plus-square-o"></i> <?php echo gettext("Order Now");?> </button>
			</div>
			 <div class="col-md-6 float-left">
                    		  <button class="btn btn-line-sky btn-block" name="cancel" onclick="return redirect_page('/products/products_list/')" value="Cancel" type="button"> <i class="fa fa-ban"></i> <?php echo gettext("Cancel");?> </button>
                   	 </div>  
                    </div>



	</div><div></div>
  </form>


  </div>
</section>



<? endblock() ?>  
<? end_extend() ?> 

<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        build_grid("refill_coupon_grid","",<? echo $grid_fields; ?>,<? echo $grid_buttons; ?>);        
        $("#refill_coupon_search_btn").click(function(){
            post_request_for_search("refill_coupon_grid","","refill_coupon_list_search");
        });        
        $("#id_reset").click(function(){
            clear_search_request("refill_coupon_grid","refill_coupon_clearsearchfilter");
        });
  
    });
</script>

<? endblock() ?>

<? startblock('page-title') ?>
    <?= $page_title ?>
<? endblock() ?>

<? startblock('content') ?>         
<section class="slice color-three">
	<div class="w-section inverse no-padding">
    	<div class="container">
   	    <div class="row">
            	<div class="portlet-content"  id="search_bar" style="cursor:pointer; display:none">
                    	<?php echo $form_search; ?>
    	        </div>
            </div>
        </div>
    </div>
</section>

<section class="slice color-three padding-b-20">
	<div class="w-section inverse no-padding">
    	<div class="container">
        	<div class="row">
                <div class="col-md-12">      
                        
                            <table id="refill_coupon_grid" align="left" style="display:none;"></table>
                        
                </div>  
            </div>
        </div>
    </div>
</section>
  
<? endblock() ?>	
<? end_extend() ?>  
 

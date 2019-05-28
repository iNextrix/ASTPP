<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
      
        build_grid("orders_grid","",<? echo $grid_fields; ?>,'');
        $('.checkall').click(function () {
            $('.chkRefNos').prop('checked', $(this).prop('checked'));
        });
        $("#user_order_search_btn").click(function(){ 
            post_request_for_search("orders_grid","","orders_list_search");
        });        
        $("#id_reset").click(function(){
            clear_search_request("orders_grid","user_products_list_search");
        });
	var currentdate = new Date(); 
        var datetime = currentdate.getFullYear() + "-"
            + ('0' + (currentdate.getMonth()+1)).slice(-2) + "-"
			+ ("0" + currentdate.getDate()).slice(-2) ;
            
        var datetime1 = currentdate.getFullYear() + "-"
           +('0' + (currentdate.getMonth()+1)).slice(-2) + "-" 
            + ("0" + currentdate.getDate()).slice(-2);
	$("#billing_date_from_date").datepicker({
	   value:datetime,
            uiLibrary: 'bootstrap4',
            iconsLibrary: 'fontawesome',
            modal:true,
            format: 'yyyy-mm-dd',
            footer:true
         });
         $("#billing_date_to_date").datepicker({
	    value:datetime1,
            uiLibrary: 'bootstrap4',
            iconsLibrary: 'fontawesome',
            modal:true,
            format: 'yyyy-mm-dd',
            footer:true
         });  
        
    });
</script>
<? endblock() ?>

<? startblock('page-title') ?>
<?= $page_title ?>
<? endblock() ?>

<? startblock('content') ?> 

<section class="slice color-three">
    <div class="w-section inverse p-0">
        <div class="col-12">
                <div class="portlet-content mb-4"  id="search_bar" style="display:none">
                        <?php echo $form_search; ?>
                </div>
            </div>
    </div>
</section>

<section class="slice color-three pb-4">
	<div class="w-section inverse p-0">
    	<div class="card col-md-12 pb-4">
        	             <table id="orders_grid" align="left" style="display:none;"></table>
               
        </div>
    </div>
</section>
<? endblock() ?>	
<? end_extend() ?>  

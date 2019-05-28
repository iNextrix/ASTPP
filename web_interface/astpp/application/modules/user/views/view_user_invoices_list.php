<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {     
        build_grid("invoice_grid","",<? echo $grid_fields; ?>,"");
        $("#user_invoice_search_btn").click(function(){
            post_request_for_search("invoice_grid","","user_invoice_search");
        });        
        $("#id_reset").click(function(){
            clear_search_request("invoice_grid","");
        });        
    });
     $(document).ready(function() {
        var currentdate = new Date(); 
        var datetime = currentdate.getFullYear() + "-"
            + ('0' + (currentdate.getMonth()+1)).slice(-2) + "-" 
                + currentdate.getDate() + " ";
            
        var datetime1 = currentdate.getFullYear() + "-"
           +('0' + (currentdate.getMonth()+1)).slice(-2) + "-" 
            + currentdate.getDate() + ""
      
        $("#invoice_to_date").datepicker({
            value:datetime,
            uiLibrary: 'bootstrap4',
            iconsLibrary: 'fontawesome',
            modal:true,
            format: 'yyyy-mm-dd',
            footer:true
         });  
         $("#invoice_from_date").datepicker({
             value:datetime1,
            uiLibrary: 'bootstrap4',
            iconsLibrary: 'fontawesome',
            modal:true,
            format: 'yyyy-mm-dd',
            footer:true
         }); 
          $("#generate_date").datepicker({
            uiLibrary: 'bootstrap4',
            iconsLibrary: 'fontawesome',
            modal:true,
            format: 'yyyy-mm-dd',
            footer:true
         });   
        
        
        
    });
</script>
<script type="text/javascript">
  $(document).ready(function(){
      $(".breadcrumb li a").removeAttr("data-ripple",""); 
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
            	<div class="portlet-content mb-4"  id="search_bar" style="cursor:pointer; display:none">
                    	<?php echo $form_search; ?>
    	        </div>
        </div>
    </div>
</section>
<section class="slice color-three padding">
    <div class="w-section inverse no-padding">
                <div class="card col-md-12 pb-4">
                    <table id="invoice_grid" align="left" style="display:none;"></table>
                </div>  
    </div>
</section>  
<? endblock() ?>	
<? end_extend() ?>  
<script type="text/javascript">
  $(document).ready(function(){
      $('.page-wrap').addClass('addon_wrap');
  });
</script>

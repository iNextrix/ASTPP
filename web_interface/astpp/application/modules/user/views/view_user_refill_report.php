<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        build_grid("refillreport_grid","",<? echo $grid_fields ?>,"");
        $("#user_refill_report_search_btn").click(function(){
            post_request_for_search("refillreport_grid","","user_refill_report_search");
        });
        $("#id_reset").click(function(){
            clear_search_request("refillreport_grid","");
        });
        
        $("#customer_cdr_from_date").datetimepicker({
            uiLibrary: 'bootstrap4',
            iconsLibrary: 'fontawesome',
            modal:true,
            format: 'yyyy-mm-dd HH:MM:ss',
            footer:true
         });  
         $("#customer_cdr_to_date").datetimepicker({
            uiLibrary: 'bootstrap4',
            iconsLibrary: 'fontawesome',
            modal:true,
            format: 'yyyy-mm-dd HH:MM:ss',
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
            	<div class="portlet-content mb-4"  id="search_bar" style="cursor:pointer; display:none">
                    	<?php echo $form_search; ?>
    	        </div>
        </div>
    </div>
</section>
<section class="slice color-three padding">
    <div class="w-section inverse no-padding">
                <div class="card col-md-12 pb-4">
                    <table id="refillreport_grid" align="left" style="display:none;"></table>
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

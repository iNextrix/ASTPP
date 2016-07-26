<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        build_grid("refill_report_grid","",<? echo $grid_fields; ?>,<? echo $grid_buttons; ?>);
        
        $("#cusotmer_cdr_refill_search_btn").click(function(){

            post_request_for_search("refill_report_grid","<?php echo base_url(); ?>reports/customer_refillreport_search/","cdr_refill_search");
        });        
        $("#id_reset").click(function(){
            clear_search_request("refill_report_grid","<?php echo base_url(); ?>reports/customer_refillreport_clearsearchfilter/");
        });
        var currentdate = new Date(); 
        var datetime = currentdate.getFullYear() + "-"
            + ('0' + (currentdate.getMonth()+1)).slice(-2) + "-" 
                + ("0" + currentdate.getDate()).slice(-2) + " 00:00:00";  
        var datetime1 = currentdate.getFullYear() + "-"
           +('0' + (currentdate.getMonth()+1)).slice(-2) + "-" 
            + ("0" + currentdate.getDate()).slice(-2) + " 23:59:59";
   /**************************/     
           jQuery("#refill_from_date").datetimepicker({format:'Y-m-d h:s:i'});		
   jQuery("#refill_to_date").datetimepicker({format:'Y-m-d h:s:i'});
        jQuery("#refill_from_date").val(datetime);
        jQuery("#refill_to_date").val(datetime1);
       
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
                        <form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">
                            <table id="refill_report_grid" align="left" style="display:none;"></table>
                        </form>
                </div>  
            </div>
        </div>
    </div>
</section>
<? endblock() ?>	
<? end_extend() ?>  

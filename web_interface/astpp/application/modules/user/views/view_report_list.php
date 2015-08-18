<? extend('master.php') ?>
<? startblock('extra_head') ?>

<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        build_grid("report_grid","",<? echo $grid_fields; ?>,"");
        $("#user_cdr_search_btn").click(function(){
            post_request_for_search("report_grid","<?php echo base_url(); ?>reports/user_cdrreport_search/","user_cdrs_report_search");
        });        
        $("#id_reset").click(function(){
            clear_search_request("report_grid","<?php echo base_url(); ?>reports/user_cdrreport_clearsearchfilter/");
        });
    });
</script>
<script>
       $(document).ready(function() {
        var currentdate = new Date(); 
        var datetime = currentdate.getFullYear() + "-"
            + ('0' + (currentdate.getMonth()+1)).slice(-2) + "-" 
                + currentdate.getDate() + " 00:00:01";
            
        var datetime1 = currentdate.getFullYear() + "-"
           +('0' + (currentdate.getMonth()+1)).slice(-2) + "-" 
            + currentdate.getDate() + " 23:59:59"

        $("#customer_cdr_from_date").val(datetime);		
        $("#customer_cdr_to_date").val(datetime1);
        $("#customer_cdr_from_date").datetimepicker({ dateFormat: 'yy-mm-dd' });		
        $("#customer_cdr_to_date").datetimepicker({ dateFormat: 'yy-mm-dd' });			
    });
</script>

<? endblock() ?>

<? startblock('page-title') ?>
<?= $page_title ?><br/>
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
                            <table id="report_grid" align="left" style="display:none;"></table>
                        </form>
                </div>  
            </div>
        </div>
    </div>
</section>


<? endblock() ?>	
<? end_extend() ?>  

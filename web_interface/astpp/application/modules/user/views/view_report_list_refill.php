<? extend('master.php') ?>
<? startblock('extra_head') ?>

<script type="text/javascript" language="javascript">
    $(document).ready(function() {

        build_grid("report_grid","",<? echo $grid_fields; ?>,"");
        $("#cusotmer_cdr_refill_search_btn").click(function(){
            post_request_for_search("report_grid","<?php echo base_url(); ?>reports/user_refillreport_search/","cdr_refill_search");
        });        
        $("#id_reset").click(function(){
            clear_search_request("report_grid","<?php echo base_url(); ?>reports/user_refillreport_clearsearchfilter/");
        });

        $("#customer_cdr_from_date").datetimepicker();
        $("#customer_cdr_to_date").datetimepicker();
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
                            <table id="report_grid" align="left" style="display:none;"></table>
                        </form>
                </div>  
            </div>
        </div>
    </div>
</section>

<? endblock() ?>	
<? end_extend() ?>  

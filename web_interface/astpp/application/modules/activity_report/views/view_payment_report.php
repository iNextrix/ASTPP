<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
    function account_change_search(val){
        $.ajax({
          type: "POST",
          url: "<?= base_url()?>/accounts/customer_account_change/"+val,
          data:'',
          success:function(alt) {
           $("#account_dropdown").html(alt);    
       }
   });
    }
    $(document).ready(function() {
      
        build_grid("payment_report_grid","",<? echo $grid_fields; ?>,"");
        
        $("#cusotmer_cdr_payment_search_btn").click(function(){

            post_request_for_search("payment_report_grid","<?php echo base_url(); ?>reports/customer_paymentreport_search/","cdr_payment_search");
        });        
        $("#id_reset").click(function(){
           var drp_down = '<select><option>--Select--</option></select>';
           $("#account_dropdown").html(drp_down);  
           clear_search_request("payment_report_grid","<?php echo base_url(); ?>reports/customer_paymentreport_clearsearchfilter/");
       });
        
        
        
    });
</script>
<script>
    $(document).ready(function() {
        $("#customer_cdr_from_date").datetimepicker();
        $("#customer_cdr_to_date").datetimepicker();
    });
</script>
<? endblock() ?>

<? startblock('page-title') ?>
<?= $page_title ?><br />
<? endblock() ?>

<? startblock('content') ?>


<section class="slice color-three">
	<div class="w-section inverse p-0">
		<div class="col-12">
			<div class="portlet-content mb-4" id="search_bar"
				style="cursor: pointer; display: none">
                <?php echo $form_search; ?>
            </div>
		</div>
	</div>
</section>

<section class="slice color-three padding-b-20">
	<div class="w-section inverse no-padding">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<form method="POST" action="del/0/" enctype="multipart/form-data"
						id="ListForm">
						<table id="payment_report_grid" align="left"
							style="display: none;"></table>
					</form>
				</div>
			</div>
		</div>
	</div>
</section>
<? endblock() ?>	
<? end_extend() ?>  

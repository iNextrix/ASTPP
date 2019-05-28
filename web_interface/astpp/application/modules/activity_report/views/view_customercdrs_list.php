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
           $("#account_dropdown_download").html(alt); 
       }
   });
    }
    function account_change_search_rategroup(val){
        $.ajax({
          type: "POST",
          url: "<?= base_url()?>/accounts/customer_rategroup_change/"+val,
          data:'',
          success:function(alt) {
           $("#account_dropdown_rategroup").html(alt);    
       }
   });
    }
    $(document).ready(function() {
      
        build_grid("configuration_grid","",<? echo $grid_fields; ?>,<? echo $grid_buttons; ?>);
        
        $("#cusotmer_cdr_search_btn").click(function(){
            post_request_for_search("configuration_grid","","cdr_customer_search");
        });        
        $("#id_reset").click(function(){
           var drp_down = '<select><option>--Select--</option></select>';
           $("#account_dropdown").html(drp_down);
           var drp_down_rategroup = '<select><option>--Select--</option></select>';
           $("#account_dropdown_rategroup").html(drp_down); 
           clear_search_request("configuration_grid","");
       });

        
    });
    function file_not_found()
    {
      alert("File not found");
  }
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

    var datetime_download = currentdate.getFullYear() + "-"
    + ('0' + (currentdate.getMonth()+1)).slice(-2) + "-" 
    + currentdate.getDate();
    
    var datetime1_download = currentdate.getFullYear() + "-"
    +('0' + (currentdate.getMonth()+1)).slice(-2) + "-" 
    + currentdate.getDate();
    $("#customer_cdr_from_date").val(datetime);		
    $("#customer_cdr_to_date").val(datetime1);
    jQuery("#download_cdr_from_date").datetimepicker({format:'Y-m-d'});		
    jQuery("#download_cdr_to_date").datetimepicker({format:'Y-m-d'});
    $("#download_cdr_from_date").val(datetime_download);		
    $("#download_cdr_to_date").val(datetime1_download);
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
<section class="slice color-three">
	<div class="w-section inverse no-padding">

		<div class="mb-4 col-12">
			<span id="error_msg" class=" success"></span>
			<div class="portlet-content" id="update_bar"
				style="cursor: pointer; display: none">
               <?php echo $form_batch_download; ?>
           </div>

		</div>
	</div>
</section
<section class="slice color-three padding-b-20">
	<div class="w-section inverse no-padding">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<form method="POST" action="del/0/" enctype="multipart/form-data"
						id="ListForm">
						<table id="configuration_grid" align="left" style="display: none;"></table>
					</form>
				</div>
			</div>
		</div>
	</div>
</section>


<? endblock() ?>	
<? end_extend() ?>  

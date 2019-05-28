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
      
        build_grid("configuration_grid","",<? echo $grid_fields; ?>,'');
        
        $("#cusotmer_cdr_search_btn").click(function(){
            post_request_for_search("configuration_grid","","cdr_customer_search");
        });        
        $("#id_reset").click(function(){
           var drp_down = '<select><option>--Select--</option></select>';
           $("#account_dropdown").html(drp_down);
           clear_search_request("configuration_grid","");
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
});
</script>
<? endblock() ?>

<? startblock('page-title') ?>
<?= $page_title ?><br />
<? endblock() ?>

<? startblock('content') ?>

<section class="slice color-three">
	<div class="w-section inverse no-padding">
		<div class="container">
			<div class="row">
				<div class="portlet-content" id="search_bar"
					style="cursor: pointer; display: none">
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
					<form method="POST" action="del/0/" enctype="multipart/form-data"
						id="ListForm">
						<table id="configuration_grid" align="left" style="display: none;"></table>
					</form>
				</div>
			</div>
		</div>
	</div>
	<!--<br/>
<div class="pull-right padding-r-20">
      <a class="btn-tw btn" href="/reports/customerReport_export_cdr_xls"><i class="fa fa-file-excel-o fa-lg"></i>Export CSV</a>
      <a class="btn-xing btn" href="/reports/customerReport_export_cdr_pdf"><i class="fa fa-file-pdf-o fa-lg"></i>Export PDF</a>
  </div><br/><br/> -->
</section>


<? endblock() ?>	
<? end_extend() ?>  

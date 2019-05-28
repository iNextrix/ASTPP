<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
    function account_change(val){
        $.ajax({
          type: "POST",
          url: "<?= base_url()?>/accounts/customer_account_change/"+val,
          data:'',
          success:function(alt) {
           $("#accountid").html(alt);    
       }
   });
    }
    function account_change_search(val){
        $.ajax({
          type: "POST",
          url: "<?= base_url()?>/accounts/customer_account_change_commission/"+val,
          data:'',
          success:function(alt) {
           $("#account_dropdown").html(alt);    
       }
   });
    }
    $(document).ready(function() {
        build_grid("charges_grid","",<? echo $grid_fields; ?>,"");
        $('.checkall').click(function () {
            $('.chkRefNos').attr('checked', this.checked);
        });
        $("#charges_search_btn").click(function(){
         
            post_request_for_search("charges_grid","","charges_search");
        });        
        $("#id_reset").click(function(){
           var drp_down = '<select><option>--Select--</option></select>';
           $("#account_dropdown").html(drp_down);  
           clear_search_request("charges_grid","");
       });
        
    });
</script>
<script>
 $(document).ready(function() {
     jQuery("#charge_from_date").datetimepicker({format:'Y-m-d h:s:i'});		
     jQuery("#charge_to_date").datetimepicker({format:'Y-m-d h:s:i'});
     var currentdate = new Date(); 
     var datetime = currentdate.getFullYear() + "-"
     + ('0' + (currentdate.getMonth()+1)).slice(-2) + "-" 
     + 01 + " 00:00:01";
     
     var datetime1 = currentdate.getFullYear() + "-"
     +('0' + (currentdate.getMonth()+1)).slice(-2) + "-" 
     + currentdate.getDate() + " 23:59:59"

     $("#charge_from_date").val(datetime);		
     $("#charge_to_date").val(datetime1);
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
						<table id="charges_grid" align="left" style="display: none;"></table>
					</form>
				</div>
			</div>
		</div>
	</div>
</section>
<? endblock() ?>	

<? end_extend() ?>  

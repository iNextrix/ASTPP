<? extend('master.php') ?>
<? startblock('extra_head') ?>

<script type="text/javascript" language="javascript">
  $(document).ready(function() {

    build_grid("flex1","",<? echo $grid_fields; ?>,<? echo $grid_buttons; ?>);
    $('.checkall').click(function () {
      $('.chkRefNos').prop('checked', $(this).prop('checked'));
    });
    $("#account_search_btn").click(function(){
      post_request_for_search("flex1","","account_search");
    });        
    $("#id_reset").click(function(){ 
      clear_search_request("flex1","");
    });
    $("#customer_batch_update_form").click(function(){
      submit_form("customer_batch_update");
    })
    
    $("#batch_update_btn").click(function(){
      submit_form("reseller_batch_update");
    })


          $("#id_batch_reset").click(function(){
            $(".update_drp").each(function() {
              var name=this.name;
              var split_name;
              if(name!=undefined){
               split_name=name.split("[");
               $('#'+split_name[0]).hide();
               $('#'+split_name[0]).val("");
               $('.update_drp').selectpicker('refresh');
             }else{
               $('.update_drp').val("1");
               $('.update_drp').selectpicker('refresh');
             }
             $('pGroup div').removeClass('dropdown');
             if(document.getElementById("expiry")){
               document.getElementById("expiry").style.display = "block";
             }
           });
            $(".pricelist_id").val("");
            $('.pricelist_id').selectpicker('refresh');
            $(".loss_less_routing").val("");
            $('.loss_less_routing').selectpicker('refresh');
            $(".is_recording").val("");
            $('.is_recording').selectpicker('refresh');
            $(".notify_flag").val("");
            $('.notify_flag').selectpicker('refresh');
            $(".allow_ip_management").val("");
            $('.allow_ip_management').selectpicker('refresh');
            $(".local_call").val("");
            $('.local_call').selectpicker('refresh');
            $(".status").val("");
            $('.status').selectpicker('refresh');
            
            
          });
          $(".update_drp").change(function(){
           var inputid = this.name.split("[");
           if(this.value != "1"){
             $('#'+inputid[0]).show();
           }else{
             $('#'+inputid[0]).hide();
           }
         }).each(function(){
          var inputid = this.name.split("[");
          if(this.value != "1"){
            $('#'+inputid[0]).show();
            $(this).addClass("mr-4");
          }else{
            $('#'+inputid[0]).hide();
            $(this).removeClass("mr-4");
          }
        });
       });
  
  $(document).ready(function() {
    $("#first_used").datepicker({
      uiLibrary: 'bootstrap4',
      iconsLibrary: 'fontawesome',
      modal:true,
      format: 'yyyy-mm-dd',
      footer:true
    });       
    $("#expiry").datepicker({
     uiLibrary: 'bootstrap4',
     iconsLibrary: 'fontawesome',
     modal:true,
     format: 'yyyy-mm-dd',
     footer:true
   });
    
    $("#expiry1").datepicker({
     uiLibrary: 'bootstrap4',
     iconsLibrary: 'fontawesome',
     modal:true,
     format: 'yyyy-mm-dd',
     footer:true
   });
    
    $("#creation").datepicker({
     uiLibrary: 'bootstrap4',
     iconsLibrary: 'fontawesome',
     modal:true,
     format: 'yyyy-mm-dd',
     footer:true
   });
  });
</script>
<script type="text/javascript">
 $(document).ready(function() {
  $('pGroup div').removeClass('dropdown');
  if(document.getElementById("expiry")){
   document.getElementById("expiry").style.display = "block";
 }

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
			<div class="portlet-content mb-4" id="search_bar"
				style="display: none;">
        <?php echo $form_search; ?>
      </div>
		</div>
	</div>
</section>

<section class="slice color-three">
	<div class="w-section inverse p-0">

		<div class="col-12">
			<span id="error_msg" class="text-danger"></span>
			<div class="portlet-content mb-4" id="update_bar"
				style="display: none;">
       <?php echo $form_batch_update; ?>
     </div>
		</div>
	</div>
</section>

<section class="slice color-three">
	<div class="w-section inverse p-0">
		<div class="card col-md-12 pb-4">
			<table id="flex1" align="left" style="display: none;"></table>
		</div>
	</div>
</section>

<? endblock() ?>	

<? end_extend() ?>  

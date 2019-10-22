<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery.validate.min.js"></script>
<script type="text/javascript" language="javascript">
 $(document).ready(function() {
    $( window ).load(function() {
	     $(".did_dropdown").removeClass("col-md-5");  
             $(".did_dropdown").addClass("col-md-3"); 
    });
    build_grid("did_purchase_grid","",<? echo $grid_fields; ?>,<? echo $grid_buttons; ?>);
    $('.checkall').click(function () { 
                $('.chkRefNos').prop('checked', $(this).prop('checked')); //if you want to select/deselect checkboxes use this
        });
    $("#did_purchase_search_btn").click(function(){        
            post_request_for_search("did_purchase_grid","","did_purchase_search");
    });        
    $("#id_reset").click(function(){ 
      clear_search_request("did_purchase_grid","");
    });

    $("#country_id").change(function() {

     		var country_id= $('#country_id').val();
				var url_new ='<?php echo base_url() . "did_purchase/did_purchase_country_change/"; ?>';
		    $.ajax({
					type: "POST",
					url: url_new,
					data:{ country_id : country_id},
					success:function(response) {
					var objJSON = JSON.parse(response);
						$("#provience_id_search_drp").html(objJSON);
						$("#provience_id_search_drp").prepend("<option value='' selected><?=gettext('--Select--')?></option>"); 
						$('#provience_id_search_drp').selectpicker('refresh');
					}
			});  
		});
		$("#provience_id_search_drp").change(function() {

			var provience_id= $('#provience_id_search_drp').val();
			var url_new ='<?php echo base_url() . "did_purchase/did_purchase_state_change/"; ?>';
			$.ajax({
				type: "POST",
				url: url_new,
				data:{ provience_id : provience_id},
				success:function(response) {
				var objJSON = JSON.parse(response);
				$("#city_id_search_drp").html(objJSON);
				$("#city_id_search_drp").prepend("<option value='' selected><?=gettext('--Select--')?></option>"); 
				$('#city_id_search_drp').selectpicker('refresh');
			        }
                        });  
		});
        $(".package").click(function () {
                
                var result = "";                        
                var idarr = [];
                $(".chkRefNos").each( function () {
                        if(this.checked == true) {     
                        result += ",'"+$(this).val()+"'";
                        idarr.push($(this).val());
                        } 
                });     
                result = result.substr(1);
                if(result != '')
                {       
                        var url_new ='<?php echo base_url() . "did_purchase/did_purchase_ids/"; ?>';
                        $.ajax({
				type: "POST",
				url: url_new,
				data:{ ids : result}
                        });
                }else{
                        alert("Please select atleast one record.");
                        return false;
                }
        });

 });
</script>
<style>
 #err{
   height:20px !important;width:100% !important;float:left;
 }
 label.error {
  float: left; color: red;
  padding-left: .3em; vertical-align: top;  
  padding-left:0px;
  margin-top:-10px;
  width:100% !important;
 }
</style>
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

<section class="slice color-three pb-4">
	<div class="w-section inverse p-0">
                <div class="card col-md-12 pb-4">     
                        <form method="POST"  enctype="multipart/form-data" id="ListForm">
                            <table id="did_purchase_grid" align="left" style="display:none;"></table>
                        </form>
                </div>  
    </div>
</section>
<? endblock() ?>
<? end_extend() ?>

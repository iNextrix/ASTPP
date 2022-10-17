<? extend('master.php') ?>
<? startblock('extra_head') ?>
<?php $current_locale = $this->session->userdata('user_language'); ?>
<script>
    $(document).ready(function() {
        $('.rm-col-md-12').addClass('float-right');
        $(".rm-col-md-12").removeClass("col-md-12");
        //Manish Issue 2341
        var from_date = date + " 00:00:00";
        var to_date = date + " 23:59:59";
        //End
        $("#last_did_from_date").datetimepicker({	
            // Kinjal issue no 2361 Call activity report - Search - "From Date" and "To Date" should not be blank displayed in search.
            value:from_date,
            // END
            uiLibrary: 'bootstrap4',
            iconsLibrary: 'fontawesome',
            modal:true,
            format: 'yyyy-mm-dd HH:MM:ss',
            footer:true
         });  
         $("#last_did_to_date").datetimepicker({
             // Kinjal issue no 2361 Call activity report - Search - "From Date" and "To Date" should not be blank displayed in search.
            value:to_date,
            // END
            uiLibrary: 'bootstrap4',
            iconsLibrary: 'fontawesome',
            modal:true,
            format: 'yyyy-mm-dd HH:MM:ss',
            footer:true
         }); 
        $("#last_outbound_from_date").datetimepicker({
            // Kinjal issue no 2361 Call activity report - Search - "From Date" and "To Date" should not be blank displayed in search.
            value:from_date,
            // END	
            uiLibrary: 'bootstrap4',
            iconsLibrary: 'fontawesome',
            modal:true,
            format: 'yyyy-mm-dd HH:MM:ss',
            footer:true
         });  
         $("#last_outbound_to_date").datetimepicker({
            // Kinjal issue no 2361 Call activity report - Search - "From Date" and "To Date" should not be blank displayed in search.
            value:to_date,
            // END
            uiLibrary: 'bootstrap4',
            iconsLibrary: 'fontawesome',
            modal:true,
            format: 'yyyy-mm-dd HH:MM:ss',
            footer:true
         }); 

		$(".reseller_id_search_drp").change(function(){
                if(this.value!=""){
					$.ajax({
						type:'POST',
						url: "<?= base_url()?>/reports/reseller_customerlist/",
						data:"reseller_id="+this.value, 
						success: function(response) {
							 $("#accountid_search_drp").html(response);
							 $("#accountid_search_drp").prepend("<option value='' selected='selected'><?php echo gettext('--Select--'); ?></option>");
							 $('.accountid_search_drp').selectpicker('refresh');
						}
					});
				}else{
						$("#accountid_search_drp").html("<option value='' selected='selected'><?php echo gettext('--Select--'); ?></option>");
						$('.accountid_search_drp').selectpicker('refresh');
					}	
        });
        
        $(".reseller_id_search_drp").change();
         });
</script>

<script type="text/javascript" language="javascript">
	
    $(document).ready(function() {
      
        build_grid("configuration_grid","",<? echo $grid_fields; ?>,<? echo $grid_buttons; ?>);
        
        $("#activity_search_btn").click(function(){
            post_request_for_search("configuration_grid","","activity_search");
        });        
        $("#id_reset").click(function(){
            clear_search_request("configuration_grid","");
            $("#accountid_search_drp").html("<option value='' selected='selected'><?php echo gettext('--Select--')?> </option>");
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
<?= $page_title ?>
<? endblock() ?>

<? startblock('content') ?>        

<section class="slice color-three">
	<div class="w-section inverse p-0">
		<div class="col-12">
			<div class="portlet-content mb-4" id="search_bar"
				style="display: none">
                        <?php echo $form_search; ?>
                </div>
		</div>
	</div>
</section>

<section class="slice color-three pb-4">
	<div class="w-section inverse p-0">
		<div class="card col-md-12 pb-4">
				<table id="configuration_grid" align="left" style="display: none;"></table>
		</div>
	</div>
</section>

<? endblock() ?>	
<? end_extend() ?>  

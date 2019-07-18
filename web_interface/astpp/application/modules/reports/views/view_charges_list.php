<? extend('master.php') ?>
<? startblock('extra_head') ?>


<script type="text/javascript" language="javascript">
    $(document).ready(function() {
      
        build_grid("charges_grid","",<? echo $grid_fields; ?>,"");
         $('.checkall').click(function () {
            $('.chkRefNos').prop('checked', $(this).prop('checked')); //if you want to select/deselect checkboxes use this
        });
       $("#charges_search_btn").click(function(){
           
            post_request_for_search("charges_grid","","charges_search");
        });        
        $("#id_reset").click(function(){
            clear_search_request("charges_grid","");
            $("#accountid_search_drp").html("<option value='' selected='selected'>--Select--</option>");
            
        });
        
    });
</script>
<script>
       $(document).ready(function() {
        var currentdate = new Date(); 
        var datetime = currentdate.getFullYear() + "-"
            + ('0' + (currentdate.getMonth()+1)).slice(-2) + "-01 00:00:00";
            
        var datetime1 = currentdate.getFullYear() + "-"
           +('0' + (currentdate.getMonth()+1)).slice(-2) + "-" 
            + ("0" + currentdate.getDate()).slice(-2) + " 23:59:59"
        $("#charge_from_date").datetimepicker({
             value:datetime,
            uiLibrary: 'bootstrap4',
            iconsLibrary: 'fontawesome',
            modal:true,
            format: 'yyyy-mm-dd HH:MM:ss',
            footer:true
         });  
         $("#charge_to_date").datetimepicker({
             value:datetime1,
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
			<form method="POST" action="del/0/" enctype="multipart/form-data"
				id="ListForm">
				<table id="charges_grid" align="left" style="display: none;"></table>
			</form>
		</div>
	</div>
</section>
<? endblock() ?>	

<? end_extend() ?>  

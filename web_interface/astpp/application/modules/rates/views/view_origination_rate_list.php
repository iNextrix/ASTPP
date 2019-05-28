<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
function search_btn(){
    document.getElementById('origination_rate_batch_dlt').style.display = 'block';
}
function check_btn(){
	 	$.ajax({
			type: "POST",
			url: "<?= base_url()?>/rates/origination_rates_list_delete/",
			data:'',
			success:function(alt) {
			 if(alt > 0){
		  	   post_request_for_batch_delete("origination_rate_grid",alt,"origination_rate_list_search");
	  	           $('#origination_rate_grid').flexOptions({newp:1}).flexReload();
			  }else{
			     alert('No record found');
			  }
			}
		});
}

    $(document).ready(function() {
        build_grid("origination_rate_grid","",<? echo $grid_fields; ?>,<? echo $grid_buttons; ?>);
        $('.checkall').click(function () {
            $('.chkRefNos').prop('checked', $(this).prop('checked')); //if you want to select/deselect checkboxes use this
        });
        $("#origination_rate_list_search_btn").click(function(){
            post_request_for_search("origination_rate_grid","","origination_rate_list_search");
        });        
        $("#id_reset").click(function(){
    document.getElementById('origination_rate_batch_dlt').style.display = 'none';
            clear_search_request("origination_rate_grid","");
            $("#pricelist_id_search_drp").html("<option value='' selected='selected'>--Select--</option>");
        });

         $("#batch_update").click(function(){
            submit_form("origination_rate_batch_update");
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
            });
            $(".trunk_id").val("");
            $('.trunk_id').selectpicker('refresh');
            
            
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
            }else{
                $('#'+inputid[0]).hide();
            }
        });
        
        $(".reseller_id_search_drp").change(function(){
                if(this.value!=""){
					$.ajax({
						type:'POST',
						url: "<?= base_url()?>/accounts/customer_pricelist/",
						data:"reseller_id="+this.value, 
						success: function(response) {
							 $("#pricelist_id_search_drp").html(response);
							 $("#pricelist_id_search_drp").prepend("<option value='' selected='selected'>--Select--</option>");
							 $('.pricelist_id_search_drp').selectpicker('refresh');
						}
					});
				}else{
							$("#pricelist_id_search_drp").html("");
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
				style="display: none;">
                        <?php echo $form_search; ?>
                </div>
		</div>
	</div>
</section>
<section class="slice color-three">
	<div class="w-section inverse p-0">
		<div class="col-12">
			<div class="portlet-content mb-4" id="update_bar"
				style="display: none;">
                        <?php echo $form_batch_update; ?>
                </div>
		</div>
	</div>
</section>
<section class="slice color-three pb-4">
	<div class="w-section inverse p-0">
		<div class="card col-md-12 pb-4">
			<form method="POST" enctype="multipart/form-data" id="ListForm">
				<table id="origination_rate_grid" align="left"
					style="display: none;"></table>
			</form>
		</div>
	</div>
</section>

<? endblock() ?>	
<? end_extend() ?>  

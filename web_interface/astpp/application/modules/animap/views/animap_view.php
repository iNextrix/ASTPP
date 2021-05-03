<? extend('master.php') ?>
<? startblock('extra_head') ?>


<script type="text/javascript" language="javascript">
    $(document).ready(function() {
      
        build_grid("animap_grid","",<? echo $grid_fields; ?>,<? echo $grid_buttons; ?>);
         $('.checkall').click(function () {
       		 $('.chkRefNos').prop('checked', $(this).prop('checked'));
        });
       $("#animap_search_btn").click(function(){
           
            post_request_for_search("animap_grid","","animap_search");
        });        
        $("#id_reset").click(function(){
            clear_search_request("animap_grid","");
        });
	$(".reseller_id_search_drp").change(function(){
                if(this.value!=""){
					$.ajax({
						type:'POST',
						url: "<?= base_url()?>/accounts/customer_depend_list/",
						data:"reseller_id="+this.value, 
						success: function(response) {
							 $("#accountid_search_drp").html(response);
							 $("#accountid_search_drp").prepend("<option value='' selected='selected'>--Select--</option>");
							 $('.accountid_search_drp').selectpicker('refresh');
						}
					});
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
				<table id="animap_grid" align="left" style="display: none;"></table>
			</form>
		</div>
	</div>
</section>




<? endblock() ?>	

<? end_extend() ?>  

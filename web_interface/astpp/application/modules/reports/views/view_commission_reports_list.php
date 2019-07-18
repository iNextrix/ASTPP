<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
      
        build_grid("commissions_report_grid","",<? echo $grid_fields; ?>,<? echo $grid_buttons; ?>);
        $('.checkall').click(function () {
            $('.chkRefNos').attr('checked', this.checked); 
        });
        $("#commissions_report_search_btn").click(function(){ 
            post_request_for_search("commissions_report_grid","","commissions_report_search");
        });        
        $("#id_reset").click(function(){
            clear_search_request("commissions_report_grid","");
            $("#accountid_search_drp").html("<option value='' selected='selected'><?php echo gettext('--Select--'); ?></option>");
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
			<table id="commissions_report_grid" align="left"
				style="display: none;"></table>

		</div>
	</div>
</section>
<? endblock() ?>	
<? end_extend() ?>  

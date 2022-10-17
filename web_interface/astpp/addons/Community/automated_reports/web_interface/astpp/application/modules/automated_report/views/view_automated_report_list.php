<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
	
    $(document).ready(function() {
        build_grid("automated_report_grid","",<? echo $grid_fields; ?>,<? echo $grid_buttons; ?>);
        $("#automated_search_btn").click(function(){
            post_request_for_search("automated_report_grid","","automated_report_search");
        });        
        $('.checkall').click(function () {
             $('.chkRefNos').prop('checked', $(this).prop('checked'));
        });
        $("#id_reset").click(function(){
            clear_search_request("automated_report_grid","");
        });
          $("#accountid_search_drp").html("<option value='' selected='selected'><?php echo gettext('--Select--')?> </option>");
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
				<table id="automated_report_grid" align="left" style="display: none;"></table>
		</div>
	</div>
</section>

<? endblock() ?>	
<? end_extend() ?>  

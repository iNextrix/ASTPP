<? extend('master.php') ?>

<? startblock('extra_head') ?>

<script type="text/javascript" language="javascript">
    $(document).ready(function() {
      
        build_grid("invoice_conf_grid","",<? echo $grid_fields; ?>,<? echo $grid_buttons; ?>);
        $('.checkall').click(function () {
            $('.chkRefNos').attr('checked', this.checked); 
        });
        
        $("#company_profile_search_btn").click(function(){
            post_request_for_search("invoice_conf_grid","","invoice_conf_search");
        });        
        $("#id_reset").click(function(){
            clear_search_request("invoice_conf_grid","invoice_confsearchfilter");
        });
        
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
				style="cursor: pointer; display: none">
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
				<table id="invoice_conf_grid" align="left" style="display: none;"></table>
			</form>
		</div>

</section>
<? endblock() ?>	
<? end_extend() ?>  

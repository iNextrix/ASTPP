<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
	 build_grid("reseller_rates_grid","",<?  echo $grid_fields; ?> ,<? echo $grid_buttons; ?>);
         $('.checkall').click(function () {
            $('.chkRefNos').attr('checked', this.checked); 
        });
       $("#resellerrates_list_search_btn").click(function(){
            post_request_for_search("reseller_rates_grid","","resellerrates_list_search");
        });        
        $("#id_reset").click(function(){
            clear_search_request("reseller_rates_grid","");
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

<section class="slice color-three padding-b-20">
	<div class="w-section inverse no-padding">
		<div class="">
			<div class="">
				<div class="card col-md-12 pb-4">
					<form method="POST" action="del/0/" enctype="multipart/form-data"
						id="ListForm">
						<table id="reseller_rates_grid" align="left"
							style="display: none;"></table>
					</form>
				</div>
			</div>
		</div>
	</div>
</section>


<br />
<br />
<? endblock() ?>	
<? end_extend() ?>  

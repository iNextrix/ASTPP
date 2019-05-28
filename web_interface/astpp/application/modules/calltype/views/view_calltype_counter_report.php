<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
       build_grid("package_counter_grid","",<? echo $grid_fields; ?>,<? echo $grid_buttons; ?>);
    });
</script>
<? endblock() ?>

<? startblock('page-title') ?>
<?= $page_title ?>
<? endblock() ?>

<? startblock('content') ?>
<section class="slice color-three padding-b-20">
	<div class="w-section inverse no-padding">
		<div class="">
			<div class="">
				<div class="card col-md-12 pb-4">
					<form method="POST" action="del/0/" enctype="multipart/form-data"
						id="ListForm">
						<table id="package_counter_grid" align="left"
							style="display: none;"></table>
					</form>
				</div>
			</div>
		</div>
	</div>
	</div>
</section>

<? endblock() ?>	
<? end_extend() ?>  

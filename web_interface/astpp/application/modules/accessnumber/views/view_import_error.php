<? extend('master.php') ?>
<? startblock('extra_head') ?>
<? endblock() ?>      
<? startblock('page-title') ?>
<?=$page_title?>
<? endblock() ?>
<? startblock('content') ?>

<section class="slice color-three bp-4">
	<div class="w-section inverse p-0">
		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<h3 class="bg-secondary text-light p-3 rounded-top"><?php echo gettext( "Error In CSV File"); ?> </h3>
					<div class="col-md-12 p-4">
						<?php echo gettext( "Records Imported Successfully:");?> <?= $import_record_count; ?>
						<br />
						<?php echo gettext( "Records Not Imported :"); ?> <?= $failure_count?>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-12 pb-2 mt-4 pr-0">
			<div class="float-right">
				<button class="btn btn-success" id="dwnld_err" type="button"
					name="action" value="Download Errors"
					onclick="return redirect_page('<?= base_url().'accessnumber/accessnumber_error_download/'?>')"><?php echo gettext( "Download Errors"); ?></button>
				<button class="btn btn-secondary mr-2" id="accessnumber_list"
					type="button" name="action" value="Back to AccessNumber List"
					onclick="return redirect_page('<?= base_url().'accessnumber/accessnumber_list/'?>')"><?php echo gettext( "Back to AccessNumber List"); ?> </button>
			</div>
		</div>
	</div>
</section>
<? endblock() ?>
<? startblock('sidebar') ?>
Filter by
<? endblock() ?>
<? end_extend() ?>  



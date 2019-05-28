<style>
.details_table td {
	text-shadow: 0 1px 0 white;
	padding: 6px;
	font-size: 11px;
	text-align: center;
	vertical-align: middle;
}
</style>
<? extend('master.php') ?>
  <? startblock('extra_head') ?>
  <? endblock() ?>      
    <? startblock('page-title') ?>
        <?=$page_title?><br />
<? endblock() ?>
	<? startblock('content') ?>
<section class="slice color-three bp-4">
	<div class="w-section inverse p-0">
		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<h3 class="bg-secondary text-light p-3 rounded-top"><?php echo gettext('Error In CSV File'); ?></h3>
					<div class="col-md-12 p-4"><?php echo gettext('Records Imported Successfully:'); ?>
									  <?= $import_record_count; ?>
									<br /><?php echo gettext('Records Not Imported:'); ?>
									 <?= $failure_count?>
							</div>
				</div>
			</div>
		</div>
		<div class="col-md-12 pb-2 mt-4 pr-0">
			<div class="float-right">
				<button class="btn btn-success" id="download_file" type="button"
					name="action" value="Download Errors"
					onclick="return redirect_page('<?= base_url().'ratedeck/ratedeck_error_download/'?>')"><?php echo gettext('Download Errors'); ?></button>
				<button class="btn btn-secondary" id="back_to_list" type="button"
					name="action" value="Back to Ratedeck List"
					onclick="return redirect_page('/ratedeck/ratedeck_list/')"><?php echo gettext('Back to Ratedeck List'); ?></button>
			</div>
		</div>
	</div>
</section>
<? endblock() ?>
 <? startblock('sidebar') ?>
Filter by
<? endblock() ?>
<? end_extend() ?>


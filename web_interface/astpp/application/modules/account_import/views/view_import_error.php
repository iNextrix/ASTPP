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
<?=$page_title?>
<? endblock() ?>
<? startblock('content') ?>
<section class="slice color-three bp-4">
	<div class="w-section inverse p-0">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="card">
						<h3 class="bg-secondary text-light p-3 rounded-top"> <?php echo gettext("Error In CSV File"); ?> </h3>
						<div class="col-md-12 p-4">
							<?php echo gettext("Records imported successfully").'. : '; ?><?php echo $count;?>
							<br />
							<?php echo gettext("Records not imported")." : "; ?> <?php echo $invalid_count;?>  
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="container">
			<div class="col-md-12 pb-2 mt-5">
				<i><?php echo gettext("Note : Duplicate accounts with account number / email are ignored.");?></i>
				<div class="pull-right">
					<a href="<?= base_url().'accounts/customer_list/'?>"><input
						class="btn btn-line-parrot" id="customer_list" type="button"
						name="action" value=<?php echo gettext("Back to Customer List"); ?> /> </a>
				</div>
			</div>
		</div>
	</div>
</section>
<? endblock() ?>
<? startblock('sidebar') ?>
<?php echo gettext("Filter by "); ?>
<? endblock() ?>
<? end_extend() ?>  




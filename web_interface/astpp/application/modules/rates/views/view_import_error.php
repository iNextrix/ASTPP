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
					<h3 class="bg-secondary text-light p-3 rounded-top"><?php echo gettext("Error In CSV File"); ?></h3>
					<div class="col-md-12 p-4">
						<?php echo gettext("Records Imported Successfully: "); ?><?= $impoted_count?>
								<br />
								<?php echo gettext("Records Not Imported :"); ?> <?= $failure_count?>
						</div>
				</div>
			</div>
		</div>
		<div class="col-md-12 pb-2 mt-4 pr-0">
				<?php if (isset($trunkid) && $trunkid != "") { ?>
								<div class="float-right">
										<?php
        $profile_url = explode("/", $_SERVER['REQUEST_URI']);
        if ($profile_url[1] == 'termination_rate_rates_mapper_import') {
            ?>
												<button class="btn btn-success" id="ok" type="button"
					name="action" value="Download Errors"
					onclick="return redirect_page('<?= base_url().'rates/termination_rate_error_download/'?>')"><?php echo gettext("Download Errors"); ?></button>
								 <?php }else{ ?>
											<button class="btn btn-success" id="ok" type="button"
					name="action" value="Download Errors"
					onclick="return redirect_page('<?= base_url().'rates/termination_rate_mapper_error_download/'?>')"><?php echo gettext("Download Errors"); ?></button>
									  <?php }?>
									<button class="btn btn-secondary" id="ok" type="button"
					name="action" value="Back to Termination Rates List"
					onclick="return redirect_page('/rates/termination_rates_list/')"><?php echo gettext("Back to Termination Rates List"); ?></button>
			</div>
									<?php  }?>   
									<?php if (isset($pricelistid) && $pricelistid != "") { ?>
										<div class="float-right">
				<button class="btn btn-success" id="download_file" type="button"
					name="action" value="Download Errors"
					onclick="return redirect_page('<?= base_url().'rates/origination_rate_error_download/'?>')"><?php echo gettext("Download Errors"); ?></button>
				<button class="btn btn-secondary" id="back_to_list" type="button"
					name="action" value="Back to Origination Rates List"
					onclick="return redirect_page('/rates/origination_rates_list/')"><?php echo gettext("Back to Origination Rates List"); ?></button>
			</div>
									<?}?>
					</div>
	</div>
</section>
<? endblock() ?>
    <? startblock('sidebar') ?>
        Filter by
    <? endblock() ?>
<? end_extend() ?>  
    


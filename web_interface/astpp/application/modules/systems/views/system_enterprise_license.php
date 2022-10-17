<?php
include(FCPATH.'application/views/popup_header.php'); ?>

<section class="slice color-three padding-b-20">
	<div id="floating-label" class="w-section inverse no-padding">
		<div class="col-md-12 p-0">
			<h3 class="text-dark fw4 pl-4 pr-4 pt-4 rounded-top">Mobile SIP Dialer</h3>
		</div>
		<form method="post" name="enterprise_form" id="enterprise_form"
			action="">
			<div class="p-4">
				<div class="m-2">
					<span> - </span>  White Labeling
				</div>
				<div class="m-2">
					<span> - </span> Branding
				</div>
				<div class="m-2">
					<span> - </span>  API Integration
				</div>
				<div class="m-2">
					<span> - </span> Fully Customized User interface (UI)
				</div>
				<div class="m-2">
					<span> - </span>  Android and iOS compatible
				</div>
				<div class="m-2">
					<span> - </span>  24x7 customer support
				</div>
			</div>
			<div class="text-center mt-4">
                    <a class="btn btn-success" href='https://www.astppbilling.org/mobile-dialer/' target="_blank" name="action" value="Save">Upgrade
					Now</a>
				<button class="btn btn-secondary ml-2" name="action" value="Save"
					type="button" onclick="return redirect_page('NULL')">Cancel</button>
			</div>
		</form>
	</div>
</section>
<?php exit; ?>

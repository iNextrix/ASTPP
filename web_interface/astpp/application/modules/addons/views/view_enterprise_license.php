<?php include(FCPATH.'application/views/popup_header.php'); ?>

<section class="slice color-three padding-b-20">
	<div id="floating-label" class="w-section inverse no-padding">
		<div class="col-md-12 p-0">
			<h3 class="text-dark fw4 pl-4 pr-4 pt-4 rounded-top">ASTPP Enterprise</h3>
		</div>
		<form method="post" name="enterprise_form" id="enterprise_form"
			action="<?= base_url() ?>addons/addons_install/<?= $type."/".$package_name ?>/install/<?= $new_version."/".$old_version ?>">
			<div class="p-4">
				<div class="m-2">
					<span> - </span> Access to all Enterprise add-ons
				</div>
				<div class="m-2">
					<span> - </span> Upgrade to future versions
				</div>
				<div class="m-2">
					<span> - </span> Funcational training sessions
				</div>
				<div class="m-2">
					<span> - </span> Bug fixes guarantee
				</div>
				<div class="m-2">
					<span> - </span> Premium support
				</div>
			</div>
			<div class="text-center mt-4">
				<button class="btn btn-success" name="action" value="Save"
					type="button"
					onclick="return redirect_page('https://www.astppbilling.org/contact-us/')">Upgrade
					Now</button>
				<button class="btn btn-secondary ml-2" name="action" value="Save"
					type="button" onclick="return redirect_page('NULL')">Cancel</button>
			</div>
		</form>
	</div>
</section>
<?php exit; ?>

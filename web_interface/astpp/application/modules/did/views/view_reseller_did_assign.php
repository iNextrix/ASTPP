<?php include(FCPATH.'application/views/popup_header.php'); ?>

<section class="slice color-three padding-b-20">
	<div id="floating-label" class="w-section inverse no-padding">
		<div class="col-md-12 p-0">
			<h3 class="text-dark fw4 pl-4 pr-4 pt-4 rounded-top"><?php echo gettext("Assign DID"); ?> #<?php echo $number;?> </h3>
		</div>
		<form method="post" name="assign_did_form" id="assign_did_form"
			action="<?php echo base_url();?>did/did_assign_number/">
			<input class="col-md-12 form-control form-control-lg m-0"
				name="product_id" value="<?php echo $product_id; ?>" size="16"
				type="hidden" />
			<div class='col-md-12 form-group'>
				<label class="col-md-12 p-0 control-label"><?php echo gettext("Accounts"); ?></label>
				<div class="col-md-12 form-control selectpicker form-control-lg p-0">
			  <?php
    $account_add = array(
        "id" => "accountid",
        "name" => "accountid",
        "class" => "accountid"
    );
    echo form_dropdown($account_add, $accounts_list, "", "");
    ?>				
			</div>
			</div>

			<div class="col-md-12 my-4">
				<div class="col-md-6 float-left">
					<input class="btn btn-success btn-block" name="order_now"
						id="order_now" value=<?php echo gettext("Assign"); ?>
						type="submit">
				</div>
				<div class="col-md-6 float-left">
					<button class="btn btn-secondary mx-2 btn-block" name="cancel"
						onclick="return redirect_page('/did/did_list/')" value="Cancel"
						type="button"> <?php echo gettext("Cancel"); ?> </button>
				</div>
			</div>

		</form>
	</div>
</section>
<?php exit; ?>

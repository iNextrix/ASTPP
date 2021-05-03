<? extend('left_panel_master.php') ?>
<?php error_reporting(E_ERROR); ?>
<? startblock('extra_head') ?>
<script type="text/javascript"
	src="<?php echo base_url(); ?>assets/js/jquery.validate.min.js"></script>

<script type="text/javascript">
    $("#submit").click(function(){
        submit_form("callerid_form");
    })
</script>
<? endblock() ?>
<? startblock('page-title') ?>
<?= $page_title ?>
<? endblock() ?>
<? startblock('content') ?>
<div id="main-wrapper">
	<div id="content" class="container-fluid">
		<div class="row">
			<div class="col-md-12 color-three border_box">
				<div class="float-left m-2 lh19">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb m-0 p-0">
							<li class="breadcrumb-item"><a
								href="<?= base_url()."accounts/".strtolower($accounttype)."_list/"; ?>"><?= gettext(ucfirst($accounttype)."s"); ?></a></li>
							<li class="breadcrumb-item"><a
								href="<?= base_url()."accounts/".strtolower($accounttype)."_edit/".$edit_id."/"; ?>">
									<?php echo gettext("Profile"); ?> </a></li>
							<li class="breadcrumb-item active" aria-current="page"><a
								href="<?= base_url()."accounts/".strtolower($accounttype)."_add_callerid/".$edit_id."/"; ?>">
                            <?php echo gettext("Force Caller ID");?>
                        </a></li>
						</ol>
					</nav>
				</div>
			</div>
			<div class="p-4 col-md-12">
        <?php

echo $form;
        if (isset($validation_errors) && $validation_errors != '') {
            ?>
           <script>
            var ERR_STR = '<?php echo $validation_errors; ?>';
            print_error(ERR_STR);
        </script>
    <?php } ?>
</div>
		</div>
	</div>
</div>
<? endblock() ?>
<? end_extend() ?>

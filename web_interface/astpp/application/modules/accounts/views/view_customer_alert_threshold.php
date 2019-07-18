<? extend('left_panel_master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript"
	src="<?php echo base_url(); ?>assets/js/jquery.validate.min.js"></script>
<style>
label.error {
	float: left;
	color: red;
	padding-left: .3em;
	vertical-align: top;
	padding-left: 40px;
	margin-top: 20px;
	width: 1500% !important;
}
</style>

<script type="text/javascript">
  $(document).ready(function(){
      $('.page-wrap').addClass('addon_wrap');
  });
</script>
<script type="text/javascript">
  $(document).ready(function(){
      $(".breadcrumb li a").removeAttr("data-ripple",""); 
  });
</script>
<?php endblock() ?>
<? startblock('page-title') ?>
<?= $page_title ?>
<? endblock() ?>
<?php startblock('content') ?>
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
								href="<?= base_url()."accounts/".strtolower($accounttype)."_edit/".$edit_id."/"; ?>"> <?php echo gettext("Profile");?> </a>
							</li>
							<li class="breadcrumb-item active" aria-current="page"><a
								href="<?= base_url()."accounts/".strtolower($accounttype)."_alert_threshold/".$edit_id."/"; ?>">
                               <?php echo gettext("Alert Threshold")?>
                            </a></li>
						</ol>
					</nav>
				</div>
				<div class="m-2 pull-right">


					<a class="btn btn-light btn-hight"
						href="<?= base_url()."accounts/".strtolower($accounttype)."_edit/".$edit_id."/"; ?>">
						<i class="fa fa-fast-backward" aria-hidden="true"></i> <?php echo gettext("Back");?></a>
				</div>
			</div>
			<div class="p-4 col-md-12">
				<div class="col-md-12 p-0">
                <?php echo $form; ?>
                <?php if (isset($validation_errors) && $validation_errors != '') { ?>
                    <script>
                        var ERR_STR = '<?php echo $validation_errors; ?>';
                        print_error(ERR_STR);
                    </script>
                <? } ?> 
                
            </div>
			</div>
		</div>
	</div>
</div>
<? endblock() ?>
<? end_extend() ?>
<script type="text/javascript">
  $(document).ready(function(){
      $(".breadcrumb li a").removeAttr("data-ripple",""); 
  });
</script>

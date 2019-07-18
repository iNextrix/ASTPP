<? extend('left_panel_master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        build_grid("emailhistory_grid","<?php echo base_url()."accounts/customer_details_json/emailhistory/$edit_id/"; ?>",<? echo $grid_fields ?>,"");
    });

</script>
<script type="text/javascript">
  $(document).ready(function(){
      $(".breadcrumb li a").removeAttr("data-ripple",""); 
  });
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
								href="<?= base_url()."accounts/".strtolower($accounttype)."_list/"; ?>"><?= gettext(ucfirst($accounttype)."s"); ?> </a></li>
							<li class="breadcrumb-item"><a
								href="<?= base_url()."accounts/".strtolower($accounttype)."_edit/".$edit_id."/"; ?>">
									Profile </a></li>
							<li class="breadcrumb-item active" aria-current="page"><a
								href="<?= base_url()."accounts/".strtolower($accounttype)."_emailhistory/".$edit_id."/"; ?>">
									Emails </a></li>
						</ol>
					</nav>
				</div>
				<div class="m-2 float-right">

					<a class="btn btn-light btn-hight"
						href="<?= base_url()."accounts/".strtolower($accounttype)."_edit/".$edit_id."/"; ?>">
						<i class="fa fa-fast-backward" aria-hidden="true"></i> <?php echo gettext('Back');?></a>

				</div>
			</div>
			<div class="p-4 col-md-12">
				<div class="col-md-12 p-0">
					<div
						class="col-md-12 color-three slice float-left content_border p-0">
						<div class="card col-md-12 pb-4">
							<table id="emailhistory_grid" align="left" style="display: none;"></table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<? endblock() ?>	

<? end_extend() ?>
<script type="text/javascript">
  $(document).ready(function(){
      $('.page-wrap').addClass('addon_wrap');
  });
</script>

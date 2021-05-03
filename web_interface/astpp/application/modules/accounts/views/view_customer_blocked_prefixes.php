<? extend('left_panel_master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
  $(document).ready(function() {
   $('a[rel*=facebox]').facebox();
   build_grid("pattern_grid","<?php echo base_url()."accounts/customer_details_json/pattern/$edit_id/"; ?>",<? echo $grid_fields ?>,"");
   $("#left_panel_quick_search").keyup(function(){
    quick_search("accounts/customer_details_search/"+'<?php echo $accounttype?>'+"_pattern/");
  });
   $('.checkall').click(function () {
    $('.chkRefNos').prop('checked', $(this).prop('checked'));
  });
 });
</script>
<script type="text/javascript">
  $(document).ready(function(){
    $('.page-wrap').addClass('addon_wrap');
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
								href="<?= base_url()."accounts/".strtolower($accounttype)."_list/"; ?>"><?= gettext(ucfirst($accounttype)."s"); ?></a></li>
							<li class="breadcrumb-item"><a
								href="<?= base_url()."accounts/".strtolower($accounttype)."_edit/".$edit_id."/"; ?>"> <?php echo gettext('Profile');?>
                  </a></li>
							<li class="breadcrumb-item active"><a
								href="<?= base_url()."accounts/".strtolower($accounttype)."_blocked_prefixes/".$edit_id."/"; ?>">
                    <?php echo gettext('Blocked Codes');?>
                  </a></li>
						</ol>
					</nav>
				</div>
				<div class="m-2 float-right">
					<a class="btn btn-light btn-hight"
						href="<?= base_url()."accounts/".strtolower($accounttype)."_edit/".$edit_id."/"; ?>">
						<i class="fa fa-fast-backward" aria-hidden="true"></i><?php echo gettext('Back');?></a>
				</div>
			</div>
			<div class="p-4 col-md-12">
				<div class="col-md-12 p-0">
					<div class="float-left">
						<a
							href='<?php echo base_url()."/accounts/customer_add_blockpatterns/$edit_id/"; ?>'
							rel="facebox_medium" title="Add"> <span
							class="btn btn-line-warning"><i class="fa fa-plus-circle fa-lg"></i><?php echo gettext('Add');?>       
              </span>
						</a>
					</div>
					<div id="left_panel_delete" class="float-left mt-0 px-1"
						onclick="delete_multiple('/accounts/customer_blockedprefixes_delete_multiple/')">
						<span class="btn btn-line-danger"> <i
							class="fa fa-times-circle fa-lg"></i>
            <?php echo gettext('Delete');?>
           </span>
					</div>
					<div id="show_search" class="float-right col-md-4 p-0">
						<input type="text" name="left_panel_quick_search"
							id="left_panel_quick_search"
							class="form-control form-control-lg m-0"
							value="<?php echo $this->session->userdata('left_panel_search_'.$accounttype.'_pattern')?>"
							placeholder=<?php echo gettext("Search"); ?> />
					</div>
				</div>

				<div id="blocked_prefixes"
					class="col-md-12 color-three slice float-left content_border mt-4 p-0">
					<div class="card col-md-12 pb-4">
						<table id="pattern_grid" align="left" style="display: none;"></table>
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
    $(".breadcrumb li a").removeAttr("data-ripple",""); 
  });
</script>

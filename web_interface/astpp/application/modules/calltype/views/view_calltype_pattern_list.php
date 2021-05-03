<? extend('left_panel_master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
		$('a[rel*=facebox]').facebox();
        build_grid("package_pattern_list","<? echo base_url()."package/package_pattern_list_json/".$edit_id."/"?>",<? echo $grid_fields ?>,"");
        $('.checkall').click(function () {
            $('.chkRefNos').attr('checked', this.checked); 
        });
        $("#left_panel_quick_search").keyup(function(){
            quick_search("package/package_quick_search/");
        });
    });
</script>
<? endblock() ?>
<? startblock('page-title') ?>
<?= $page_title ?>
<? endblock() ?>
<? startblock('content') ?>
<div id="main-wrapper" class="tabcontents">
	<div id="content">
		<div class="row">
			<div class="col-md-12 no-padding color-three border_box">
				<div class="pull-left">
					<ul class="breadcrumb">
						<li><a href="<?= base_url()."package/package_list/"; ?>"><?php echo gettext("Package List"); ?> </a></li>
						<li class="active"><a
							href="<?= base_url()."package/package_edit/".$edit_id."/"; ?>"><?php echo gettext("Details"); ?> </a>
						</li>
						<li class="active"><a
							href="<?= base_url()."package/package_pattern_list/".$edit_id."/"; ?>"><?php echo gettext("Codes"); ?> </a>
						</li>
					</ul>
				</div>
				<ul class="breadcrumb">
					<li class="active pull-right"><a
						href="<?= base_url()."package/package_edit/".strtolower($accounttype).$edit_id."/"; ?>">
							<i class="fa fa-fast-backward" aria-hidden="true"></i> <?php echo gettext("Back"); ?></a></li>
				</ul>
			</div>
			<div class="padding-15 col-md-12">
				<div class="col-md-12 no-padding">
					<div class="pull-left margin-t-10">
						<span class="btn btn-line-warning"> <a
							href='<?php echo base_url()."/package/package_patterns_add/".$edit_id."/"; ?>'
							rel="facebox_medium" title="Add"> <i
								class="fa fa-plus-circle fa-lg"></i> <?php echo gettext("Add"); ?>
                            </a>
						</span>
					</div>

					<div class="pull-left margin-t-10" style="padding-left: 5px">
						<a
							href='<?php echo base_url()."/package/package_patterns_import/".$edit_id."/"; ?>'
							title="Import"> <span class="btn btn-line-blue"> <i
								class="fa fa-upload fa-lg"></i> <?php echo gettext("Import"); ?>
                            
                        </span>
						</a>
					</div>

					<div id="left_panel_delete"
						class="pull-left margin-t-10 padding-x-4"
						onclick="delete_multiple('/package/package_patterns_selected_delete/')">
						<span class="btn btn-line-danger"> <i
							class="fa fa-times-circle fa-lg"></i>
                            <?php echo gettext("Delete"); ?>
                        </span>
					</div>
					<div id="show_search"
						class="pull-right margin-t-10 col-md-4 no-padding">
						<input type="text" name="left_panel_quick_search"
							id="left_panel_quick_search"
							class="col-md-5 form-control pull-right"
							value="<?php echo $this->session->userdata('left_panel_search_package_patterns') ?>"
							placeholder="Search" />
					</div>
				</div>

				<div id="package_patterns">
					<div class="col-md-12 color-three padding-b-20">
						<table id="package_pattern_list" align="left"
							style="display: none;"></table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<? endblock() ?>	

<? end_extend() ?>  

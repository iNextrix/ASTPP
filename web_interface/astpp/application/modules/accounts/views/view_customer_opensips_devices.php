<? extend('left_panel_master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
      $('a[rel*=facebox]').facebox();
      build_grid("opensips_grid","<?php echo base_url()."accounts/customer_details_json/opensips/$edit_id/"; ?>",<? echo $grid_fields ?>,"");
      $('.checkall').click(function () {
            $('.chkRefNos').attr('checked', this.checked); //if you want to select/deselect checkboxes use this
        });
      $("#left_panel_quick_search").keyup(function(){
        quick_search("accounts/customer_details_search/"+'<?php echo $accounttype ?>'+"_opensips/");
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
						<li><a
							href="<?= base_url()."accounts/".strtolower($accounttype)."_list/"; ?>"><?= ucfirst($accounttype); ?>s</a></li>
						<li><a
							href="<?= base_url()."accounts/".strtolower($accounttype)."_edit/".$edit_id."/"; ?>"><?= ucfirst($accounttype); ?> <?php echo gettext('Profile');?> </a>
						</li>
						<li class="active"><a
							href="<?= base_url()."accounts/".strtolower($accounttype)."_opensips/".$edit_id."/"; ?>"> <?php echo gettext('Opensips Devices');?> </a>
						</li>
					</ul>
				</div>
			</div>
			<div class="padding-15 col-md-12">
				<div class="col-md-12 no-padding">
					<div class="pull-left margin-t-10">
						<span class="btn btn-line-warning"> <a
							href='<?php echo base_url()."opensips/customer_opensips_add/".$edit_id."/"; ?>'
							rel="facebox" title="Add"> <i class="fa fa-plus-circle fa-lg"></i> <?php echo gettext('Create');?>
                            </a>
						</span>
					</div>
					<div id="show_search"
						class="pull-right margin-t-10 col-md-4 no-padding">
						<input type="text" name="left_panel_quick_search"
							id="left_panel_quick_search"
							class="col-md-5 form-control pull-right"
							value="<?php echo $this->session->userdata('left_panel_search_'.$accounttype.'_opensips')?>"
							placeholder="Search" />
					</div>
				</div>

				<div id="package_patterns">
					<div class="col-md-12 color-three padding-b-20">
						<table id="opensips_grid" align="left" style="display: none;"></table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<? endblock() ?>	

<? end_extend() ?>  

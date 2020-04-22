<? extend('left_panel_master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        $("#cusotmer_cdr_search_btn").click(function(){
            post_request_for_search("cdrs_grid","<?php echo base_url()."reports/customerReport_search"; ?>","cdr_customer_search");
        });

        $("#id_reset").click(function(){
            clear_search_request("cdrs_grid","reports/customerReport_clearsearchfilter");
        });

        $("#left_panel_quick_search").keyup(function(){
            quick_search("accounts/customer_details_search/"+'<?php echo $accounttype?>'+"_cdrs/");
        });

        $('.rm-col-md-12').addClass('float-right');
        $(".rm-col-md-12").removeClass("col-md-12");
        var currentdate = new Date();
        var from_date = currentdate.getFullYear() + "-"
            + ('0' + (currentdate.getMonth()+1)).slice(-2) + "-"
                + ("0" + currentdate.getDate()).slice(-2) + " 00:00:00";

        var to_date = currentdate.getFullYear() + "-"
           +('0' + (currentdate.getMonth()+1)).slice(-2) + "-"
            +("0" + currentdate.getDate()).slice(-2) + " 23:59:59"
        $("#customer_cdr_from_date").datetimepicker({
             value:from_date,
            uiLibrary: 'bootstrap4',
            iconsLibrary: 'fontawesome',
            modal:true,
            format: 'yyyy-mm-dd HH:MM:ss',
            footer:true
         });
         $("#customer_cdr_to_date").datetimepicker({
             value:to_date,
            uiLibrary: 'bootstrap4',
            iconsLibrary: 'fontawesome',
            modal:true,
            format: 'yyyy-mm-dd HH:MM:ss',
            footer:true
         });

        build_grid("cdrs_grid","<?php echo base_url()."reports/customerReport_json"; ?>",<? echo $grid_fields ?>,"");

        $('.page-wrap').addClass('addon_wrap');
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
								href="<?= base_url()."accounts/".strtolower($accounttype)."_list/"; ?>"><?= gettext(ucfirst($accounttype)."s"); ?></a></li>
							<li class="breadcrumb-item"><a
								href="<?= base_url()."accounts/".strtolower($accounttype)."_edit/".$edit_id."/"; ?>"><?php echo gettext('Profile');?> </a>
							</li>
							<li class="breadcrumb-item active" aria-current="page"><a
								href="<?= base_url()."accounts/".strtolower($accounttype)."_cdrs/".$edit_id."/"; ?>">
                                <?php echo gettext('CDRs');?>
                            </a></li>
							<ol>
					
					</nav>
				</div>
				<div class="m-2 float-right">
					<a class="btn btn-light btn-hight"
						href="<?= base_url()."accounts/".strtolower($accounttype)."_edit/".$edit_id."/"; ?>">
						<i class="fa fa-fast-backward" aria-hidden="true"></i><?php echo gettext('Back');?></a>
				</div>
                <div id="show_search" class="float-right btn btn-warning m-2 py-1" data-ripple=" " style="position: relative; min-width: 50px;">
                    <i class="fa fa-search"></i>
                </div>
			</div>
			<div class="p-4 col-md-12">
				<div class="col-md-12 p-0">
					<div id="show_search" class="float-right col-md-4 p-0">
						<input type="text" name="left_panel_quick_search"
							id="left_panel_quick_search"
							class="form-control form-control-lg m-0"
							value="<?php echo $this->session->userdata('left_panel_search_'.$accounttype.'_cdrs')?>"
							placeholder=<?php echo gettext("Search"); ?> />
					</div>
				</div>
                <div class="col-md-12 color-three slice float-left content_border mt-1 p-0">
                    <div class="portlet-content mb-0" id="search_bar" style="display: none">
                        <?php echo $form_search; ?>
                    </div>
                </div>
				<div
					class="col-md-12 color-three slice float-left content_border mt-0 p-0">
					<div class="card col-md-12 pb-4">
						<table id="cdrs_grid" align="left" style="display: none;"></table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
</div>
<? endblock() ?>	

<? end_extend() ?>  


<? extend('left_panel_master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        build_grid("cdrs_grid","",<? echo $grid_fields; ?>,"");
        $("#left_panel_quick_search").keyup(function(){
            quick_search("user/user_details_search/"+'<?php echo $accounttype?>'+"_cdrs/");
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
                        <li><a href="<?= base_url()."user/user_myprofile/"; ?>">My Profile</a></li>
                        <li>
                            <a href="<?= base_url()."user/user_cdrs/"; ?>">CDRs</a>
                        </li>
                    </ul>
                </div>
                <div class="pull-right">
                    <ul class="breadcrumb">
		      <li class="active pull-right">
		      <a href="<?= base_url()."user/user_myprofile/"; ?>"> <i class="fa fa-fast-backward" aria-hidden="true"></i> Back</a></li>
                    </ul>
                </div>
            </div>
            <div class="padding-15 col-md-12">
                <div class="col-md-12 no-padding">
                    <div id="show_search" class="pull-right margin-t-10 col-md-4 no-padding">
                        <input type="text" name="left_panel_quick_search" id="left_panel_quick_search" class="col-md-5 form-control pull-right" value="<?php echo $this->session->userdata('left_panel_search_'.$accounttype.'_cdrs')?>" placeholder="Search"/>
                    </div>
                </div> 
                <div class="col-md-12 no-padding">
                    <div class="col-md-12 color-three padding-b-20 slice color-three pull-left content_border">
                        <table id="cdrs_grid" align="left" style="display:none;"></table>
                    </div>   
                </div>
            </div>
        </div>
    </div>
</div>
<? endblock() ?>	

<? end_extend() ?>  

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
<div id="main-wrapper"> 
    <div id="content" class="container-fluid">   
        <div class="row"> 
            <div class="col-md-12 color-three border_box"> 
                <div class="float-left m-2 lh19">
                     <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 p-0">
                         <li class="breadcrumb-item"><a href="<?= base_url() . "user/user_myprofile/"; ?>"><?php echo gettext('My Profile'); ?></a></li>
						 <li class="breadcrumb-item active">
                             <a href="<?= base_url() . "user/user_cdrs/"; ?>"><?php echo gettext('CDRs')?></a>
                          </li>
                        </ol>
                    </nav>
                </div>
                <div class="m-2 float-right">
						<a class="btn btn-light btn-hight" href="<?= base_url()."user/user_myprofile/"; ?>"> <i class="fa fa-fast-backward" aria-hidden="true"></i><?php echo gettext('Back') ?></a>
                </div>
            </div>
            <div class="p-4 col-md-12">
                <div class="col-md-12">
                    <div id="show_search" class="float-right col-md-4 p-0">
                        <input type="text" name="left_panel_quick_search" id="left_panel_quick_search" class="form-control form-control-lg m-0" value="<?php echo $this->session->userdata('left_panel_search_'.$accounttype.'_cdrs')?>" placeholder="Search"/>
                    </div>
                </div> 
                <div class="col-md-12 color-three slice float-left content_border mt-4">
                    <div class="card col-md-12 pb-4">
                        <table id="cdrs_grid" align="left" style="display:none;"></table>
                    </div>   
                </div>
            </div>
        </div>
    </div>
</div>
<? endblock() ?>	

<? end_extend() ?>  

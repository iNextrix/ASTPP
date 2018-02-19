<? extend('left_panel_master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        build_grid("user_packages_grid","",<? echo $grid_fields; ?>,"");
    });
</script>
<? endblock() ?>
<? startblock('content') ?>        
<div id="main-wrapper" class="tabcontents">  
    <div id="content">   
        <div class="row"> 
            <div class="col-md-12 no-padding color-three border_box"> 
                <div class="pull-left">
                    <ul class="breadcrumb">
                        <li><a href="<?= base_url()."user/user_myprofile/"; ?>">My Profile</a></li>
                        <li class='active'>
                            <a href="<?= base_url()."user/user_packages/"; ?>">Packages</a>
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
                    <div class="col-md-12 color-three padding-b-20 slice color-three pull-left content_border">
                        <table id="user_packages_grid" align="left" style="display:none;"></table>
                    </div>   
                </div>
            </div>
        </div>
    </div>
</div>
  
<? endblock() ?>	
<? end_extend() ?>  

<? extend('left_panel_master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        build_grid("emailhistory_grid","<?php echo base_url()."accounts/customer_details_json/emailhistory/$edit_id/"; ?>",<? echo $grid_fields ?>,"");
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
                        <li><a href="<?= base_url()."accounts/".strtolower($accounttype)."_list/"; ?>"><?= ucfirst($accounttype); ?>s </a></li>
                        <li>
                            <a href="<?= base_url()."accounts/".strtolower($accounttype)."_edit/".$edit_id."/"; ?>"> Profile </a>
                        </li>
                        <li class="active">
                            <a href="<?= base_url()."accounts/".strtolower($accounttype)."_emailhistory/".$edit_id."/"; ?>">
                                Emails
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="pull-right">
                    <ul class="breadcrumb">
		      <li class="active pull-right">
		      <a href="<?= base_url()."accounts/".strtolower($accounttype)."_edit/".$edit_id."/"; ?>"> <i class="fa fa-fast-backward" aria-hidden="true"></i> Back</a></li>
                    </ul>
                </div>
            </div>     
            <div class="padding-15 col-md-12">
                <div class="col-md-12 color-three padding-b-20">
                    <table id="emailhistory_grid" align="left" style="display:none;"></table>  
                </div>
            </div>
        </div>
    </div>
</div>
<? endblock() ?>	

<? end_extend() ?>  

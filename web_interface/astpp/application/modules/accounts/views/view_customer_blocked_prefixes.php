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
		$('.chkRefNos').attr('checked', this.checked); //if you want to select/deselect checkboxes use this
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
                        <li>
                            <a href="<?= base_url()."accounts/".strtolower($accounttype)."_list/"; ?>"><?= ucfirst($accounttype); ?>s</a></li>
                        <li>
                            <a href="<?= base_url()."accounts/".strtolower($accounttype)."_edit/".$edit_id."/"; ?>"> Profile
                            </a>
                        </li>
                        <li class="active">
                            <a href="<?= base_url()."accounts/".strtolower($accounttype)."_blocked_prefixes/".$edit_id."/"; ?>">
                                Blocked Codes
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
            <div class="col-md-12 no-padding">
                <div class="pull-left margin-t-10">
					<a href='<?php echo base_url()."/accounts/customer_add_blockpatterns/$edit_id/"; ?>' rel="facebox_medium" title="Add">
                    <span class="btn btn-line-warning">
                        
                        <i class="fa fa-plus-circle fa-lg"></i> Add
                       
                    </span>
                     </a>
                </div>
                <div id="left_panel_delete" class="pull-left margin-t-10 padding-x-4" onclick="delete_multiple('/accounts/customer_blockedprefixes_delete_multiple/')">
                   <span class="btn btn-line-danger">
                     <i class="fa fa-times-circle fa-lg"></i>
                      Delete
                     </span>
                </div>
                <div id="show_search" class="pull-right margin-t-10 col-md-4 no-padding">
                    <input type="text" name="left_panel_quick_search" id="left_panel_quick_search" class="col-md-5 form-control pull-right" value="<?php echo $this->session->userdata('left_panel_search_'.$accounttype.'_pattern')?>" placeholder="Search"/>
                </div>
            </div>    
                
            <div id="blocked_prefixes">
                <div class="col-md-12 color-three padding-b-20">
                    <table id="pattern_grid" align="left" style="display:none;"></table>  
                </div>
            </div>
             </div> 
        </div>
    </div>
</div>
<? endblock() ?>	

<? end_extend() ?>  

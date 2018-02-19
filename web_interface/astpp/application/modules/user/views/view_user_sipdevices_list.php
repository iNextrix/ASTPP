<? extend('left_panel_master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
	$('a[rel*=facebox]').facebox();
        build_grid("user_sipdevices_grid","",<? echo $grid_fields; ?>,"");
        $("#user_sipdevices_search_btn").click(function(){
            post_request_for_search("user_sipdevices_grid","","user_sipdevices_search");
        });
        $('.checkall').click(function () {
            $('.chkRefNos').attr('checked', this.checked);
        });
        $("#id_reset").click(function(){
            clear_search_request("user_sipdevices_grid","");
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
                         <?php $accountinfo=$this->session->userdata('accountinfo');
						  if($accountinfo['type']==1){ ?>
                          <li><a href="<?= base_url() . "user/user_myprofile/"; ?>">My Profile</a></li>
                          <?php } else{ ?>
			    <li><a href="#"><?php echo gettext('Configuration')?></a></li>
                          <?php }
					?>
                        
                        <li class='active'>
                           <a href="<?= base_url() . "user/user_sipdevices/"; ?>"><?php echo gettext('SIP Devices')?></a>
                        </li>
                    </ul>
                </div>
                <?php if($accountinfo['type']==1) { ?>
                   <div class="pull-right">
                    <ul class="breadcrumb">
		      <li class="active pull-right">
		      <a href="<?= base_url() . "user/user_myprofile/"; ?>"> <i class="fa fa-fast-backward" aria-hidden="true"></i> Back</a></li>
                    </ul>
                   </div>
                <?php }?>
            </div>     
            <div class="padding-15 col-md-12">
                <div class="col-md-12 no-padding">
                    <div class="pull-left margin-t-10">
                        <a href='<?php echo base_url()."user/user_sipdevices_add/"; ?>' rel="facebox_medium" title="Add">
                            <span class="btn btn-line-warning">
                                <i class="fa fa-plus-circle fa-lg"></i> Create
                            </span>
                        </a>
                    </div>
                    <div id="left_panel_delete" class="pull-left margin-t-10 padding-x-4" onclick="delete_multiple('/user/user_sipdevices_delete_multiple/')">
                        <span class="btn btn-line-danger">
                            <i class="fa fa-times-circle fa-lg"></i>
                            Delete
                        </span>
                    </div>
                    <div  class="pull-right margin-b-10 col-md-4 no-padding">
                        <div  id="left_panel_search" class= "pull-right btn btn-warning btn margin-t-10"><i class="fa fa-search"></i> Search</div>
                    </div>
                <div class="margin-b-10 slice color-three pull-left content_border col-md-12" id="left_panel_search_form" style="cursor: pointer; display: none;">
                    	<?php echo $form_search; ?>
                </div>   
                <div class="col-md-12 no-padding">
                    <div class="col-md-12 color-three padding-b-20 slice color-three pull-left content_border">
                        <table id="user_sipdevices_grid" align="left" style="display:none;"></table>
                    </div>   
                </div>
                    </div>
            </div>
        </div>
    </div>
</div>
  
<? endblock() ?>	
<? end_extend() ?>  

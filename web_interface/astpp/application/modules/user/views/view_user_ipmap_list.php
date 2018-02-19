<? extend('left_panel_master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
      
        build_grid("ipsettings_grid","",<? echo $grid_fields; ?>,"");
        $("#user_ipmap_search_btn").click(function(){
            post_request_for_search("ipsettings_grid","","user_ipmap_search");
        });        
        $("#id_reset").click(function(){
            clear_search_request("ipsettings_grid","");
        });
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
                         <?php $accountinfo=$this->session->userdata('accountinfo');
						  if($accountinfo['type']==1){ ?>
                          <li><a href="<?= base_url() . "user/user_myprofile/"; ?>">My Profile</a></li>
                          <?php } else{ ?>
			    <li><a href="#"><?php echo gettext('Configuration')?></a></li>
                          <?php }
					?>
                        
                        <li class='active'>
                            <a href="<?= base_url() . "user/user_ipmap/"; ?>"><?php echo gettext('IP Settings')?></a>
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
                    <div class="pull-left margin-t-10" id="left_panel_add">
                        <span class="btn btn-line-warning"> <i class="fa fa-plus-circle fa-lg"></i> Create</span>
                    </div>
                    <div  class="pull-right margin-b-10 col-md-4 no-padding">
                        <div  id="left_panel_search" class= "pull-right btn btn-warning btn margin-t-10"><i class="fa fa-search"></i> Search</div>
                    </div>
                    <div class="margin-b-10 slice color-three pull-left content_border col-md-12" id="left_panel_form" style="cursor: pointer; display: none;">
                    <fieldset class="margin-b-20">
                        <legend>IP Settings</legend>
                        <form method="post" name="ip_map" id="ip_map" action="<?= base_url()."user/user_ipmap_action/add/" ?>">
                            <div class='col-md-4'> 
                                <label class="col-md-1 no-padding">Name</label>
                                <input class="col-md-2 form-control" name="name" size="16" type="text"/>
                            </div>
                            <div class='col-md-4'>
                                <label class="col-md-1 no-padding">IP </label>
                                <input class="col-md-2 form-control" name="ip" size="22" type="text">
                                <span id="err"></span>
                            </div>
                            <div class='col-md-4'>
                                <label class="col-md-1 no-padding">Prefix</label>
                                <input class="col-md-2 form-control" name="prefix" size="16" type="text">
                            </div>
                            <div class="col-md-12">
                            <input class=" btn btn-success" name="action" value="Map IP" type="submit">
                            </div>
                        </form>
                    </fieldset>
                </div>
                <div class="margin-b-10 slice color-three pull-left content_border col-md-12" id="left_panel_search_form" style="cursor: pointer; display: none;">
                    	<?php echo $form_search; ?>
                </div>   
                <div class="col-md-12 no-padding">
                    <div class="col-md-12 color-three padding-b-20 slice color-three pull-left content_border">
                        <table id="ipsettings_grid" align="left" style="display:none;"></table>
                    </div>   
                </div>
                    </div>
            </div>
        </div>
    </div>
</div>
  
<? endblock() ?>	
<? end_extend() ?>  

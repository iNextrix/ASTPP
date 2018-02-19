<? extend('left_panel_master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        build_grid("subscriptions_grid","",<? echo $grid_fields; ?>,"");
        $("#user_subscriptions_button").click(function(){
            post_request_for_search("subscriptions_grid","","user_subscription_search");
        });        
        $("#id_reset").click(function(){
            clear_search_request("subscriptions_grid","");
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
			    <li><a href="#"> <?php echo gettext('Billing')?></a></li>
                          <?php }
					?>
                        
                        <li class='active'>
                             <a href="<?= base_url() . "user/user_subscriptions/"; ?>"><?php echo gettext('Subscriptions')?> </a>
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
                    <div  class="pull-right margin-b-10 col-md-4 no-padding">
                        <div  id="left_panel_search" class= "pull-right btn btn-warning btn margin-t-10"><i class="fa fa-search"></i> Search</div>
                    </div>
                 
                <div class="margin-b-10 slice color-three pull-left content_border col-md-12" id="left_panel_search_form" style="cursor: pointer; display: none;">
                    	<?php echo $form_search; ?>
                </div>   
                <div class="col-md-12 no-padding">
                    <div class="col-md-12 color-three padding-b-20 slice color-three pull-left content_border">
                        <table id="subscriptions_grid" align="left" style="display:none;"></table>
                    </div>   
                </div>
                    </div>
            </div>
        </div>
    </div>
</div>
  
<? endblock() ?>	
<? end_extend() ?>  

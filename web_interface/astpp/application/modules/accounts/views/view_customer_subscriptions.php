<? extend('left_panel_master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/validate.js"></script>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        build_grid("subscription_list","<?php echo base_url()."accounts/customer_details_json/subscription/$edit_id/"; ?>",<? echo $grid_fields; ?>,"");
        $("#left_panel_quick_search").keyup(function(){
            quick_search("accounts/customer_details_search/"+'<?php echo $accounttype?>'+"_subscription/");
        });
        $('#purchase_subscription').validate({
            rules: {
                applayable_charge: {
                    required: true
                }
            },
            errorPlacement: function(error, element) {
                error.appendTo('#err');
            }
        });
    });
</script>
<style>
    #err
    {
         height:20px !important;width:100% !important;float:left;
    }
    label.error {
        float: left; color: red;
        padding-left: .3em; vertical-align: top;  
        padding-left:0px;
        margin-top:-10px;
        width:100% !important;
       
    }
</style>
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
                        <li><a href="<?= base_url()."accounts/".strtolower($accounttype)."_list/"; ?>"><?= ucfirst($accounttype); ?>s</a></li>
                        <li>
                            <a href="<?= base_url()."accounts/".strtolower($accounttype)."_edit/".$edit_id."/"; ?>"> Profile </a>
                        </li>
                        <li class="active">
                            <a href="<?= base_url()."accounts/".strtolower($accounttype)."_subscription/".$edit_id."/"; ?>">
                                Subscriptions
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
                    <div class="pull-left margin-t-10" id="left_panel_add">
                        <span class="btn btn-line-warning"> <i class="fa fa-plus-circle fa-lg"></i> Assign</span>
                    </div>
                    <div id="show_search" class="pull-right margin-t-10 col-md-4 no-padding">
                        <input type="text" name="left_panel_quick_search" id="left_panel_quick_search" class="col-md-5 form-control pull-right" value="<?php echo $this->session->userdata('left_panel_search_'.$accounttype.'_subscription')?>" placeholder="Search"/>
                    </div>
                </div> 
            
            <div class="col-md-12 no-padding margin-b-10" id="left_panel_form" style="cursor: pointer; display: none;">
                <div class="slice color-three pull-left content_border col-md-12">
                     <fieldset class="margin-b-20">
                        <legend>Subscriptions</legend>
                        <form method="post" name="purchase_subscription" id="purchase_subscription" action="<?= base_url()."accounts/customer_subscription_action/add/".$edit_id."/".$accounttype."/"; ?>" enctype="multipart/form-data">
                            
                            <div class="col-md-8">
                                <label class="col-md-3 no-padding">Subscriptions:</label>
                                   <div class="col-md-4 no-padding sel_drop">
                                        <? echo $chargelist; ?>
                                        <span id="err"></span>
                                   </div>
                                <div class="col-md-3">
                            <input class="btn btn-success" name="action" value="Assign" type="submit">
                             </div>
                            </div>
                            
                            
                        </form>
                    </fieldset>
                </div>
            </div>
            <div class="col-md-12 no-padding">
                <div class="col-md-12 color-three padding-b-20">
                    <table id="subscription_list" align="left" style="display:none;"></table>  
                </div>
            </div>
        </div>
    </div>
</div>
<? endblock() ?>	

<? end_extend() ?>  

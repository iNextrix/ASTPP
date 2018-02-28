<? extend('left_panel_master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/validate.js"></script>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        build_grid("ipsettings_list","<?php echo base_url()."accounts/customer_ipmap_json/$edit_id/$accounttype/"; ?>",<? echo $grid_fields; ?>,"");
        $.validator.addMethod('IP4Checker', function(value) {
            //var pattern = /^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/;
            var n = value.indexOf("/");
            if(n > 0){
              var pattern = /^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\/[0-9]{1,3}$/;
            }
            else{
              var pattern = /^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/;
            }            
            return pattern.test(value);
        }, 'The IP field have not valid ip.');
       	jQuery.validator.addMethod("IP_Validate", function() {
	  var message_flag;
	  var prefix=$("#prefix").val();        
	  var ip =$("#ip").val();
          $.ajax({
                 url: "<?=base_url(); ?>accounts/customer_validate_ip/",
                 type: "post",
                 async:false,
                 data: {
                        "prefix": prefix,
                        "ip": ip,
                       },
                 success: function (data_response) {
                     message_flag=data_response.trim();
                 }
          });
          if(message_flag =='FALSE'){
           return false;
          }
          else{
           return true;
          }
       }, "The IP field must contain a unique value."); 
        $('#ip_map').validate({
            rules: {
                ip: {
                    required: true,
                    IP4Checker: true,
                    IP_Validate : true
                }
            },
            messages:{
               ip:{
		  required: "The IP field is required."
               }
            },
            errorPlacement: function(error, element) {
                error.appendTo('#err');
            }
        });
        $("#left_panel_quick_search").keyup(function(){
            quick_search("accounts/customer_details_search/"+'<?php echo $accounttype?>'+"_ipmap/");
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
                            <a href="<?= base_url()."accounts/".strtolower($accounttype)."_edit/".$edit_id."/"; ?>"> Profile</a>
                        </li>
                        <li class="active">
                            <a href="<?= base_url()."accounts/".strtolower($accounttype)."_ipmap/".$edit_id."/"; ?>"> IP Settings</a>
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
                        <span class="btn btn-line-warning"> <i class="fa fa-plus-circle fa-lg"></i> Add</span>
                    </div>
                    <div id="show_search" class="pull-right margin-t-10 col-md-4 no-padding">
                        <input type="text" name="left_panel_quick_search" id="left_panel_quick_search" class="col-md-5 form-control pull-right" value="<?php echo $this->session->userdata('left_panel_search_'.$accounttype.'_ipmap')?>" placeholder="Search"/>
                    </div>
                </div> 
                <div class="margin-b-10 slice color-three pull-left content_border col-md-12" id="left_panel_form" style="cursor: pointer; display: none;">
                    <fieldset class="margin-b-20">
                        <legend>New IP</legend>
                        <form method="post" name="ip_map" id="ip_map" action="<?= base_url()."accounts/customer_ipmap_action/add/$edit_id/$accounttype/" ?>">
                            <div class='col-md-4'> 
                                <label class="col-md-1 no-padding">Name</label>
                                <input class="col-md-2 form-control" name="name" size="16" type="text"/>
                            </div>
                            <div class='col-md-4'>
                                <label class="col-md-1 no-padding">IP *</label>
                                <input id='ip' class="col-md-2 form-control" name="ip" size="22" type="text">
                                <span id="err"></span>
                            </div>
                            <div class='col-md-4'>
                                <label class="col-md-1 no-padding">Prefix</label>
                                <input id='prefix' class="col-md-2 form-control" name="prefix" size="16" type="text">
                                
                            </div>
                            <div class="col-md-12">
                            <input class=" btn btn-success" name="action" value="Save" type="submit">
                            </div>
                        </form>
                    </fieldset>
                </div>
            
            <div class="col-md-12 no-padding">
                 <div class="col-md-12 color-three padding-b-20 slice color-three pull-left content_border">
                    <table id="ipsettings_list" align="left" style="display:none;"></table>
                </div>   
            </div>
            </div>
        </div> 
    </div>
</div>
<? endblock() ?>	

<? end_extend() ?>  

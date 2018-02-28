<? extend('left_panel_master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/validate.js"></script>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        build_grid("dids_list","<?php echo base_url()."accounts/customer_details_json/did/$edit_id/"; ?>",<? echo $grid_fields; ?>,"");
        $("#country_id" ).change(function() {
            var country_id= $('#country_id').val();
            var url ='<?php echo base_url()."accounts/customer_did_country/"; ?>';
            var accountid ='<?php echo $edit_id; ?>';
            $.ajax({
                type: "POST",
                url: url,
                data:{ country_id : country_id, accountid : accountid },
                success:function(response) {
                    $(".free_didlist").html(response);  
                    $(".country_id").removeClass("col-md-5");  
                    $(".country_id").addClass("col-md-3"); 
                    $(".free_didlist").removeClass("col-md-5");  
                    $(".free_didlist").addClass("col-md-3");  
                    $('.selectpicker').selectpicker('refresh');      
                }
            });
        });
        $("#left_panel_quick_search").keyup(function(){
            quick_search("accounts/customer_details_search/"+'<?php echo $accounttype?>'+"_did/");
         });
        $("#country_id" ).change();
        $('#did_purchase').validate({
            rules: {
                free_didlist: {
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
                            <a href="<?= base_url()."accounts/".strtolower($accounttype)."_dids/".$edit_id."/"; ?>"><?= $page_title; ?></a>
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
                        <span class="btn btn-line-warning"> <i class="fa fa-plus-circle fa-lg"></i> Purchase</span>
                    </div>
                    <div id="show_search" class="pull-right margin-t-10 col-md-4 no-padding">
                        <input type="text" name="left_panel_quick_search" id="left_panel_quick_search" class="col-md-5 form-control pull-right" value="<?php echo $this->session->userdata('left_panel_search_'.$accounttype.'_did')?>" placeholder="Search"/>
                    </div>
                </div> 
                
                
          
         
            <div class="col-md-12 no-padding margin-b-10" id="left_panel_form" style="display: none;">
                <div class="slice color-three pull-left content_border col-md-12">
                     <fieldset class="margin-b-20">
                        <legend>Purchase DID</legend>

                        <form method="post" id="did_purchase" name="did_purchase" action="<?= base_url()."accounts/customer_dids_action/add/$edit_id/$accounttype/"; ?>" enctype="multipart/form-data">
                            <div class="col-md-4">
                                <label for="Country" class="col-md-3 no-padding">Country : </label>
                                <div class="col-md-8 no-padding sel_drop">
                                 <?php
								$country_arr = array("id" => "country_id", "name" => "country_id", "class" => "country_id");
								$country = form_dropdown($country_arr, $this->db_model->build_dropdown("id,country", "countrycode", "", ""), $country_id);
								echo $country;
								?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="col-md-4 no-padding">Available DIDs : </label>
                                   <div class="col-md-8 no-padding sel_drop">
                                        <? echo $didlist; ?>
                                        <span id="err"></span>
                                   </div>
                                
                            </div>
                           
                            <input class="margin-l-20 btn btn-success" name="action" value="Purchase DID" type="submit">
                        </form>
                    </fieldset>
                </div>
            </div>
         
                <div class="col-md-12 color-three padding-b-20">
                    <table id="dids_list" align="left" style="display:none;"></table>  
                </div>
            </div>
        </div>
    </div>
</div>    
<? endblock() ?>	

<? end_extend() ?>  

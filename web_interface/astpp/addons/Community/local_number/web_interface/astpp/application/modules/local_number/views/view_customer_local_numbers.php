<? extend('left_panel_master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/validate.js"></script>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        build_grid("dids_list","<?php echo base_url()."local_number/local_number_forwarding_json/$edit_id/"; ?>",<? echo $grid_fields; ?>,"");
        $("#country_id" ).change(function() {
            var country_id= $('#country_id').val();
            var url ='<?php echo base_url()."local_number/local_number_province/"; ?>';
            var accountid ='<?php echo $edit_id; ?>';

            $.ajax({
                type: "POST",
                url: url,
                data:{ country_id : country_id, accountid : accountid },
                success:function(response) {
                    $("#province_id").html(response);  
                    $('.selectpicker').selectpicker('refresh');      
                    $(".country_id").removeClass("col-md-5");  
                    $(".country_id").addClass("col-md-3"); 
		    $(".province_id").removeClass("col-md-5");  
		    $(".province_id").addClass("col-md-3");  

                }
            });
        });
        $("#province_id" ).change(function() {
            var country_id= $('#country_id').val();
            var province= $('#province_id').val();
            var url ='<?php echo base_url()."local_number/local_number_city/"; ?>';
            var accountid ='<?php echo $edit_id; ?>';
            $.ajax({
                type: "POST",
                url: url,
                data:{ country_id : country_id, accountid : accountid, province : province },
                success:function(response) {
                    $("#city_id").html(response);  
                    $('.selectpicker').selectpicker('refresh');      
                    $(".province_id").removeClass("col-md-5");  
                    $(".province_id").addClass("col-md-3"); 
                    $(".city_id").removeClass("col-md-5");  
                    $(".city_id").addClass("col-md-3");  
                }
            });
        });
        $("#city_id" ).change(function() {
//alert('1213');
            var country_id= $('#country_id').val();
            var province= $('#province_id').val();
            var city= $('#city_id').val();
            var url ='<?php echo base_url()."local_number/local_number_customer/"; ?>';
            var accountid ='<?php echo $edit_id; ?>';
            $.ajax({
                type: "POST",
                url: url,
                data:{ country_id : country_id, accountid : accountid, city : city, province : province },
                success:function(response) {
                    //alert(response);
                    $("#local_number_id").html(response);  
                    $('.selectpicker').selectpicker('refresh');      
                    $(".city_id").removeClass("col-md-5");  
                    $(".city_id").addClass("col-md-3"); 
                    $(".local_number_id").removeClass("col-md-5");  
                    $(".local_number_id").addClass("col-md-3");  
                }
            });
        });
        $("#left_panel_quick_search").keyup(function(){
            quick_search("accounts/customer_details_search/customer_local_number/");
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
  function form_submit(){
	var local_number_id= $('#local_number_id').val();
	var flag = 0;
	document.getElementById("local_err").innerHTML="";
	document.getElementById("name_err").innerHTML="";
	document.getElementById("number_err").innerHTML="";
	if(local_number_id == ''){
		flag = 1
		document.getElementById("local_err").innerHTML="Please select Local Number";
		return false;
	}
	var name = $('#name').val();
	if(name == ''){
		flag = 1
		document.getElementById("name_err").innerHTML="Please Enter Destination Name";
		return false;
	}
	var number = $('#number').val();
	if(number == ''){
		flag = 1
		document.getElementById("number_err").innerHTML="Please Enter Destination Number";
		return false;
	}
	if(flag == 0){
		document.getElementById("local_num").submit();
	}
  }
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
                        <span class="btn btn-line-warning"> <i class="fa fa-plus-circle fa-lg"></i> Add Local Number</span>
                    </div>
                    <div id="show_search" class="pull-right margin-t-10 col-md-4 no-padding">
                        <input type="text" name="left_panel_quick_search" id="left_panel_quick_search" class="col-md-5 form-control pull-right" value="<?php echo $this->session->userdata('left_panel_search_'.$accounttype.'_did')?>" placeholder="Search"/>
                    </div>
                </div> 
                
                
          
         
            <div class="col-md-12 no-padding margin-b-10" id="left_panel_form" style="display: none;">
                <div class="slice color-three pull-left content_border col-md-12">
                     <fieldset class="margin-b-20">
                        <legend>Local Number</legend>

                        <form method="post" id="local_num" name="local_num" action="<?= base_url()."local_number/local_number_action/add/$edit_id/"; ?>" enctype="multipart/form-data">
                            <div class="col-md-4">
                                <label for="Country" class="col-md-3 no-padding">Country : </label>
                                <div class="col-md-8 no-padding sel_drop">
                                 <?php
								$country_arr = array("id" => "country_id", "name" => "country_id", "class" => "country_id");
								// $country = form_dropdown($country_arr, $this->db_model->build_dropdown("id,country", "countrycode", "", ""), $country_id);
                                $country = form_dropdown($country_arr, $this->common->set_country(), $country_id);
                                // echo $country;
								echo $country;
								?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="col-md-4 no-padding">Province/State : </label>
                                   <div class="col-md-8 no-padding sel_drop">
                                        <? echo $province; ?>
                                        <span id="err"></span>
                                   </div>
                                
                            </div>
                            <div class="col-md-4">
                                <label class="col-md-4 no-padding">City : </label>
                                   <div class="col-md-8 no-padding sel_drop">
                                        <? echo $city; ?>
                                        <span id="err"></span>
                                   </div>
                                
                            </div>
                            <div class="col-md-4">
                                <label class="col-md-3 no-padding">Local Number* : </label>
                                   <div class="col-md-8 no-padding sel_drop">
                                        <? echo $local_number; ?>
                                        <font color="red"><span id="local_err"></span></font>
                                   </div>
                                
                            </div>
                            <div class="col-md-4">
                                <label class="col-md-4 no-padding">Destination Name* : </label>
                                   <div class="col-md-8 no-padding sel_drop">
					<input type="text" class="col-md-5 form-control" name="name" id="name">
					<font color="red"><span id="name_err"></span></font>
                                   </div>
                            </div>
                            <div class="col-md-4">
                                <label class="col-md-4 no-padding">Destination Number* : </label>
                                   <div class="col-md-8 no-padding sel_drop">
					<input type="text" class="col-md-5 form-control" name="number" id="number">
					<font color="red"><span id="number_err"></span></font>
                                   </div>
                            </div>
                            <div class="col-md-4">               
                            	<input class="margin-l-20 btn btn-success" name="action" value="Add Local Number" type="button" onclick = "form_submit();">
			    </div>
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

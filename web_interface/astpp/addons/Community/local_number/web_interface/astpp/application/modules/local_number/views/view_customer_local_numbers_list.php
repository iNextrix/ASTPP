<? extend('master.php') ?>
<? startblock('extra_head') ?>

<?php
$accountinfo = $this->session->userdata ( 'accountinfo' );
$edit_id = $accountinfo ['id'];
?>
<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.js"></script>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        build_grid("local_number_grid","",<? echo $grid_fields; ?>,<? echo $grid_buttons; ?>);
        $('.checkall').click(function () {
            $('.chkRefNos').prop('checked', $(this).prop('checked'));
        });
        $("#local_number_search_btn").click(function(){
            post_request_for_search("local_number_grid","","local_number_list_search");
        });        
        $("#id_reset").click(function(){
            clear_search_request("local_number_grid","local_number_list_clearsearchfilter");
        });
    });
</script>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        $(function () {
            $("#country_id").change();
        });
        $("#country_id" ).change();
        build_grid("dids_list","<?php echo base_url()."local_number/local_number_forwarding_json/$edit_id/"; ?>",<? echo $grid_fields; ?>,"");
        
        $("#country_id" ).change(function() {
            // alert();
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
                    // alert(response);
                    $("#local_number_id1").html(response);  
                    $('.selectpicker').selectpicker('refresh');      
                    $(".city_id").removeClass("col-md-5");  
                    $(".city_id").addClass("col-md-3"); 
                    $("#local_number_id1").removeClass("col-md-5");  
                    $("#local_number_id1").addClass("col-md-3");  
                }
            });
        });
        $("#left_panel_quick_search").keyup(function(){
            quick_search("accounts/customer_details_search/customer_local_number/");
         });
        
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
    var local_number_id= $('#local_number_id1').val();
    jQuery('#local_number_id1').removeClass('borderred');
    jQuery('#name').removeClass('borderred');
    jQuery('#number').removeClass('borderred');
    var flag = 0;
    document.getElementById("local_err").innerHTML="";
    document.getElementById("name_err").innerHTML="";
    document.getElementById("number_err").innerHTML="";
    var error="";
    if(local_number_id == ''){
        flag = 1
        document.getElementById("local_err").innerHTML="<i style='color:#D95C5C; padding-right: 6px; padding-top: 10px;' class='fa fa-exclamation-triangle'></i><span class='popup_error error  p-0'>Please select Local Number</span>";
        jQuery('#local_number_id1').addClass('borderred');
        error="1";
        //return false;
    }
    var name = $('#name').val();
    if(name == ''){
        flag = 1
        document.getElementById("name_err").innerHTML="<i style='color:#D95C5C; padding-right: 6px; padding-top: 10px;' class='fa fa-exclamation-triangle'></i><span class='popup_error error  p-0'>Please Enter Destination Name</span>";
        jQuery('#name').addClass('borderred');
        error="1";
        //return false;
    }
    var number = $('#number').val();
    if(number == ''){
        flag = 1
        document.getElementById("number_err").innerHTML="<i style='color:#D95C5C; padding-right: 6px; padding-top: 10px;' class='fa fa-exclamation-triangle'></i><span class='popup_error error  p-0'>Please Enter Destination Number</span>";
        jQuery('#number').addClass('borderred');
        error="1";
        //return false;
    }
    if(error != '')
    {
        return false;
    }
    if(flag == 0){
        document.getElementById("local_num").submit();
    }
  }
</script>
<? endblock() ?>
<? startblock('page-title') ?>
<?= $page_title ?>
<? endblock() ?>
<? startblock('content') ?>        
<section class="slice color-three">
    <div class="w-section inverse p-0">
			<div class="col-12">
                <div class="portlet-content mb-4"  id="search_bar" style="cursor:pointer; display:none">
                        <?php echo $form_search; ?>
                </div>
            </div>
    </div>
</section>
<section class="slice color-three">
    <div class="w-section inverse p-0">
			<div class="col-12">
				 <div class="pull-left " id="left_panel_add">
                        <span class="btn btn-line-warning"> <i class="fa fa-plus-circle fa-lg"></i> Add Local Number</span>
                 </div>
			</div>
			<div class="pop_md col-12 pb-4 pt-2" id="left_panel_form" style="display: none;">
				<form method="post" id="local_num" name="local_num" action="<?= base_url()."local_number/local_number_customer_action/add/$edit_id/"; ?>" enctype="multipart/form-data">
					<div class="row">
						<div class="col-md-12">
							<div class="card">
						    	<div class="pb-4" id="floating-label">
									<h3 class="bg-secondary text-light p-3 rounded-top">Local Number</h3>
									 <div class="col-md-12">
										  <div class="row p-0">
											  <div class="form-group col-md-3">	
												  <label class="col-md-12 p-0 control-label">Country<span style="color:black;"> *</span></label>
												  <?php $country_arr = array("id" => "country_id", "name" => "country_id", "class" => "country_id");
												  $country = form_dropdown($country_arr, $this->db_model->build_dropdown("id,country", "countrycode", "", ""), '');
												  echo $country;?>
                                                  <div id="country_err" class="tooltips error_div float-left p-0"  style="display: block;"></div>	
											  </div>
											  <div class="form-group col-md-3">
												  <label class="col-md-12 p-0 control-label">Province/State</label>
												  <?	echo $province; ?>
                                                  <div id="province_err" class="tooltips error_div float-left p-0"  style="display: block;"></div>	
											  </div>
											  <div class="form-group col-md-3">
												  <label class="col-md-12 p-0 control-label">City</label>
												  <?	echo $city; ?>
                                                  <div id="city_err" class="tooltips error_div float-left p-0"  style="display: block;"></div>	
											  </div>
											 <div class="form-group col-md-3">
														<label class="col-md-12 p-0 control-label">Local Number<span style="color:black;"> * </span></label>
														<? echo $local_number; ?>
														<div id="local_err" class="tooltips error_div float-left p-0"  style="display: block;"></div>	
											  </div>
											  <div class="form-group col-md-3">
													  <label class="col-md-12 p-0 control-label">Destination Name<span style="color:black;"> * </span></label>
													  <input type="text" class="col-md-12 form-control form-control-lg" name="name" id="name">
													  <div id="name_err" class="tooltips error_div float-left p-0"  style="display: block;"></div>	
											  </div>
											  <div class="form-group col-md-3">
												     <label class="col-md-12 p-0 control-label">Destination Number<span style="color:black;"> * </span></label>
													 <input type="text" class="col-md-12 form-control form-control-lg" name="number" id="number">
													 <div id="number_err" class="tooltips error_div float-left p-0"  style="display: block;"></div>	
											  </div>	  
										 </div>	 
									</div>
									 <div class="col-12 my-4 text-center">
									   <input class="margin-l-20 btn btn-success" name="action" value="Add Local Number" type="button" onclick ="form_submit();">
									</div>
								</div>
							</div>
						</div>
					</div>				
				</form>
			</div>	
	</div>
</section>				
<section class="slice color-three mt-4">
	<div class="w-section inverse p-0">
        <div class="card col-md-12 pb-4">      
               <form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">
                            <table id="local_number_grid" align="left" style="display:none;"></table>
                </form>
        </div>  
    </div>
</section>
<? endblock() ?>    
<? end_extend() ?>  

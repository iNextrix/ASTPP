<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery.validate.min.js"></script>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {

        build_grid("did_grid","",<? echo $grid_fields; ?>,"");
        $("#user_did_search_btn").click(function(){
            post_request_for_search("did_grid","","user_did_search");
        }); 
        $("#id_reset").click(function(){ 
            clear_search_request("did_grid","");
        });
        $('#purchase_did_form').validate({
            rules: {
                free_didlist: {
                    required: true,
                }
            },
            messages:{
               free_didlist:{
		  required: "<i style='color:#D95C5C; padding-right: 6px; padding-top: 20px;float: right;' class='fa fa-exclamation-triangle'></i><span class='popup_error error  p-0'><?php echo gettext('The Available DIDs field is required.'); ?></span>"
               }
            },
            errorPlacement: function(error, element) {
                error.appendTo('#err');
            }
        });
        document.getElementById("err").style.display = "block"; 
        $("#purchase_did").click(function () {z
	  $("#search_generate_bar").slideToggle("slow");
    });
	
	$("#country_id").change(function() {
     		var country_id= $('#country_id').val();
		var url_new ='<?php echo base_url() . "user/user_purchase_did/"; ?>';
		    $.ajax({
		        type: "POST",
		        url: url_new,
		        data:{ country_id : country_id},
		        success:function(response) {
					var objJSON = JSON.parse(response);
					if(objJSON.state_list && objJSON.state_list!=""){
						$("#provience_id_search_drp").html(objJSON.state_list);
						$("#provience_id_search_drp").prepend("<option value='' selected> <?php echo gettext('--Select--'); ?> </option>"); 
						$('#provience_id_search_drp').selectpicker('refresh');
					}else{
						$("#provience_id_search_drp").html("");
						$("#provience_id_search_drp").prepend("<option value='' selected> <?php echo gettext('--Select--'); ?> </option>"); 
						$('#provience_id_search_drp').selectpicker('refresh');
					}
					$("#city_id_search_drp").html("");
					$("#city_id_search_drp").prepend("<option value='' selected> <?php echo gettext('--Select--'); ?> </option>"); 
					$('#city_id_search_drp').selectpicker('refresh');
					if(objJSON.didlist && objJSON.didlist!=""){
						$("#free_didlist").html(objJSON.didlist); 
						$('#free_didlist').selectpicker('refresh'); 	
					}else{
						$("#free_didlist").html(""); 
						$('#free_didlist').selectpicker('refresh'); 	
					}		 
		    }});  
	});
	$("#country_id").change();
	$("#provience_id_search_drp").change(function() {
     		var country_id= $('#country_id').val();
     		var provience= $('#provience_id_search_drp').val();
			var url_new ='<?php echo base_url() . "user/user_purchase_did/"; ?>';
		    $.ajax({
		        type: "POST",
		        url: url_new,
		        data:{ country_id : country_id,provience:provience},
		        success:function(response) {
					var objJSON = JSON.parse(response);
					if(objJSON.city_list && objJSON.city_list!=""){
						$("#city_id_search_drp").html(objJSON.city_list);
						$("#city_id_search_drp").prepend("<option value='' selected> <?php echo gettext('--Select--'); ?> </option>"); 
						$('#city_id_search_drp').selectpicker('refresh');
					}else{
						$("#city_id_search_drp").html("");
						$("#city_id_search_drp").prepend("<option value='' selected> <?php echo gettext('--Select--'); ?> </option>"); 
						$('#city_id_search_drp').selectpicker('refresh');
					}
					if(objJSON.didlist && objJSON.didlist!=""){
						$("#free_didlist").html(objJSON.didlist); 
						$('#free_didlist').selectpicker('refresh'); 	
					}
		    }});  
	});
	
	$("#city_id_search_drp").change(function() {
     		var country_id= $('#country_id').val();
     		var provience= $('#provience_id_search_drp').val();
     		var city=$('#city_id_search_drp').val();
			var url_new ='<?php echo base_url() . "user/user_purchase_did/"; ?>';
		    $.ajax({
		        type: "POST",
		        url: url_new,
		        data:{ country_id : country_id,provience:provience,city:city},
		        success:function(response) {
					var objJSON = JSON.parse(response);
					if(objJSON.didlist && objJSON.didlist!=""){
						$("#free_didlist").html(objJSON.didlist); 
						$('#free_didlist').selectpicker('refresh'); 	
					}
		    }});  
	});
    });
    </script>

<style>
    label.error {
        margin-top:-10px;
    }
</style>
<? endblock() ?>

<? startblock('page-title') ?>
<?= $page_title; ?>
<? endblock() ?>
<? startblock('content') ?>

<section class="slice color-three">
    <div class="w-section inverse p-0">
        <div class="col-12">
            <div class="portlet-content mb-4"  id="search_bar" style="display:none">
                <?php echo $form_search; ?>
            </div>
        </div>
    </div>
</section>
<div class="slice color-three">
    <div class="w-section inverse p-0">
        <div class="col-12">
             <div class="text-right pb-4">
                 <input type="button" id="left_panel_add" class="btn btn-info" name="purchase_did" value=<?php echo gettext("Purchase DID")?> id="purchase_did"> 
             </div>
         </div>

        <div class="pop_md col-12 pb-4" id="left_panel_form" style="display: none;">
            <form  id="purchase_did_form" name='purchase_did_form' method="post" action="<?= base_url() ?>user/user_dids_action/add/" enctype="multipart/form-data">
              <div class="row">
                 <div class="col-md-12">
                   <div class="card">
                      <div class="pb-4" id="floating-label">
                        <div class="col-md-12">

			      <div class="row">
                                 <div class='form-group col-md-3'>
                                    <label class="col-md-3 col-md-12 control-label p-0"><?php echo gettext('Country')?> </label>
                                 <select name="country_id" id="country_id" class="selectpicker select field  col-md-12 form-control form-control-lg"  data-hide-disabled='true' data-actions-box='true' data-live-search='true' datadata-live-search-style='begins'>
					<?php $country_list =$this->db_model->getSelect("*","countrycode",""); 
					$country_info = $country_list->result_array(); ?>
					<?php foreach($country_info as $key => $country) {    ?>
					<option value= "<?php echo $country['id']; ?>"> <?php echo  $country['nicename'] ?> </option>
					<?php } ?>
				</select>
				</div>
						<div class='form-group col-md-3'>
                                    <label class="col-md-12 control-label p-0" id="provience" name="provience_didlist_command"><?php echo gettext('Province')?> </label>
                                    <select name="provience" id="provience_id_search_drp" class="col-md-12 form-control form-control-lg selectpicker provience_id_search_drp selectpicker" data-live-search="true" tabindex="-98">
										<option></option>
                                    </select>
                                   
                       </div>  
                       <div class='form-group col-md-3'>
                                    <label class="col-md-12 control-label p-0" id="city" name="city_didlist_command"><?php echo gettext('City')?> </label>
                                    <select name="city" id="city_id_search_drp" class="col-md-12 form-control form-control-lg selectpicker city_id_search_drp selectpicker" data-live-search="true" tabindex="-98">
										<option></option>
                                    </select>
                                   
                       </div>  
                               
                                 <div class='form-group col-md-3'>
                                    <label class="col-md-12 control-label p-0" id="user_didlist_command" name="user_didlist_command"><?php echo gettext('Available DIDs')?> </label>
                                    <div class="text-danger tooltips error_div float-left p-0" id="err"></div>
                                    <? echo $didlist; ?>
                                </div>  
                              
                            			                               
                        </div>
                        <div class="col-md-12 text-right">
                              <input class="btn btn-success btn-lg" onclick="validateForm();" name="action" value=<?php echo gettext("Purchase DID");?> type="submit">
                        </div>
                      </div>
                   </div>
                 </div>					
              </div>	
            </form>

        </div>
    </div>
</div>


<section class="slice color-three">
	<div class="w-section inverse p-0">
        <div class="card col-md-12 pb-4">      
                <form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">
                    <table id="did_grid" align="left" style="display:none;"></table>
                </form>
        </div>  
    </div>
</section>

<? endblock() ?>  

<? end_extend() ?>  

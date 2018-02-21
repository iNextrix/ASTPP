
<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/validate.js"></script>
<script type="text/javascript" language="javascript">



//        $(".invoice_day").hide();
//        $('label[for="Billing Day"]').hide()
        
    $(document).ready(function() {
        build_grid("chrges_grid","<?php echo base_url(); ?>accounts/customer_details_json/charges/<?= $account_data[0]['id']; ?>",<? echo $charges_grid_field ?>,"");

        build_grid("ipmap_grid","<?php echo base_url(); ?>accounts/customer_ipmap_json/<?= $account_data[0]['id']; ?>/customer/",<? echo json_encode($ipmap_grid_field) ?>,"");

        build_grid("sip_iax_grid","<?php echo base_url(); ?>accounts/customer_details_json/freeswitch/<?= $account_data[0]['id']; ?>",<? echo $sipiax_grid_field ?>,<? echo $fs_grid_buttons; ?>);

        build_grid("opensips_grid","<?php echo base_url(); ?>accounts/customer_details_json/opensips/<?= $account_data[0]['id']; ?>",<? echo $opensips_grid_field ?>,<? echo $opensips_grid_buttons; ?>);
	
        build_grid("did_grid","<?php echo base_url(); ?>accounts/customer_details_json/did/<?= $account_data[0]['id']; ?>",<? echo $did_grid_fields ?>,"");

        build_grid("invoice_grid","<?php echo base_url(); ?>accounts/customer_details_json/invoices/<?= $account_data[0]['id']; ?>",<? echo $invoice_grid_fields ?>,"");

        build_grid("pattern_grid","<?php echo base_url(); ?>accounts/customer_details_json/pattern/<?= $account_data[0]['id']; ?>",<? echo $pattern_grid_fields ?>, <?= $pattern_grid_buttons ?>);
        
        build_grid("cdrs_grid","<?php echo base_url(); ?>accounts/customer_details_json/reports/<?= $account_data[0]['id']; ?>",<? echo $cdrs_grid_fields ?>,"");

	 build_grid("animap_list","<?php echo base_url(); ?>accounts/customer_animap_json/<?= $account_data[0]['id']; ?>", <? echo json_encode($animap_grid_field) ?>,"");
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
        }, 'Invalid IP address');


	$('#ip_map').validate({
             rules: {
                 ip: {
                     required: true,
                     IP4Checker: true
                 }
             },
             errorPlacement: function(error, element) {
                 error.appendTo('#err');
             }
         });
    
        $("#ani_map").validate({
            rules: {
                ANI: {
                    required: true
                }
            }
        });
 

        $(".sweep_id").change(function(e){
        var sweep_id =$('.sweep_id option:selected').val();
            if(sweep_id != 0){
                $.ajax({
                    type:'POST',
                    url: "<?= base_url()?>/accounts/customer_invoice_option/<?=$invoice_date?>",
                    data:"sweepid="+sweep_id, 
                    success: function(response) {
                        $(".invoice_day").html(response);
                        $('.invoice_day').show();
                        $('label[for="Billing Day"]').show()
                    }
                });
            }else{
                $('label[for="Billing Day"]').hide()
                $('.invoice_day').css('display','none');                
            }
        });
        
        
          $(".change_pass").click(function(){
            $.ajax({type:'POST',
                url: "<?= base_url()?>accounts/customer_generate_password/",
                success: function(response) {
                    $('#password').val(response.trim());
                }
            });
        })
        $(".change_number").click(function(){
            $.ajax({type:'POST',
                url: "<?= base_url()?>accounts/customer_generate_number/"+10,
                success: function(response) {
                    var data=response.replace('-',' ');
                    $('#number').val(data.trim());
                }
            });
        })
        
      /*  $(".sweep_id").change();
        $.validator.addMethod('IP4Checker', function(value) {
            var pattern = /^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/;
            return pattern.test(value);
        }, 'Invalid IP address');
	$("#expiry").datetimepicker({ format: 'Y-m-d H:i:00' });
        $('#ip_map').validate({
            rules: {
                ip: {
                    required: true,
                    IP4Checker: true
                }
            }
        }); 
        $('#ani_map').validate({
            rules: {
                number: {
                    required: true,
                }
            }
        }); */
        
});
        

</script>
<style>
     label.error {
        float: left; color: red;
        padding-left: .3em; vertical-align: top;  
        padding-left:40px;
        margin-top:20px;
        width:1500% !important;
    }
</style>
<? endblock() ?>

<? startblock('page-title') ?>
<?= $page_title ?><br/>
<? endblock() ?>

<? startblock('content') ?>        
<? $variable=common_model::$global_config['system_config']['opensips'] == 0 ? "SIP": "Opensips"; ?>
    <ul class="tabs" data-persist="true">
        <li><a href="#customer_details"><?= ucfirst($entity_name);?> Details</a></li>
        <li><a href="#account_ip">IP Settings</a></li>
        <li><a href="#animap">
Caller Id</a></li>
	<li><a href="#accounts"><?=$variable?> Settings</a></li>
        <li><a href="#packages">Subscriptions</a></li>
        <li><a href="#did">DID</a></li>
        <li><a href="#invoices">Invoices</a></li>
        <li><a href="#block_prefixes">Block Prefixes</a></li>
        <li><a href="#cdrs">CDRs</a></li>
    </ul>	
    <div class="tabcontents">        
      <div>
          <div class="col-md-12">
            <section class="slice color-three no-margin">
                <div class="w-section inverse no-padding">
<div class="w-section inverse no-padding" id ='customer_details'>
				     <?php echo $form; ?>
				     <?php
					if(isset($validation_errors) && $validation_errors != ''){ ?>
					    <script>
						var ERR_STR = '<?php echo $validation_errors; ?>';
						print_error(ERR_STR);
					    </script>
				     <? } ?>

<!--                                <?php
                                $data_errrors = json_decode($validation_errors);
                                foreach ($data_errrors as $key => $value) {
                                    echo $value . "<br/>";
                                }
                                ?> 
                          </div>
                        <?php echo $form; ?> -->
                          </div>  
<!--Accounts Tab Start-->
    <div id='account_ip'>
<!--         <div class="container">
          <div class="row">
            <section class="slice no-margin">
                <div class="w-section inverse no-padding col-md-12"> -->
                <div class="col-md-12 color-three padding-b-20 padding-t-20">
                    <form method="post" name="ip_map" id="ip_map" action="<?= base_url() ?>accounts/customer_ipmap_action/add/<?= $account_data[0] ['id'] ?>/<?= $entity_name; ?>/" enctype="multipart/form-data">
                        <label class="col-md-1" style="padding-left:50px;">Name: </label><input class="col-md-2 form-control" name="name" size="16" type="text">
                        <label class="col-md-1" style="padding-left:70px;">IP:</br> <span id="err"></label></span><input class="col-md-2 form-control" name="ip" size="22" type="text">
                        <label class="col-md-1" style="padding-left:50px;">Prefix: </label><input class="col-md-2 form-control" name="prefix" size="16" type="text">
                    
                        <input class="margin-l-20 btn btn-success" name="action" value="Map IP" type="submit">
                    </form>
                </div>                
               <div class="col-md-12 color-three ">
                        <table id="ipmap_grid" align="left" style="display:none;"></table> 
                </div>
               
              <!--      <div class="col-md-12 color-three padding-b-20 margin-t-10">
                        <? if (common_model::$global_config['system_config']['opensips'] == 0) {
                            echo "SIP Devices";
                        } else {
                            echo "Opensips Devices";
                        }
                        ?>
		        <span id="error_msg" class=" success"></span>
                        <? if (common_model::$global_config['system_config']['opensips'] == 0) { ?>
                            <table id="sip_iax_grid" align="left" style="display:none;"></table>        
                        <? } else { ?>
                            <table id="opensips_grid" align="left" style="display:none;"></table>                 
                        <? } ?>
                    </div> -->
              <!--  </div>
            </section>
         </div>
        </div>-->
    </div>


    <!--Accounts Tab Start-->
<div id='accounts'>
	<div class="col-md-12 color-three padding-b-20 padding-t-10">
  <!--                      <? if (common_model::$global_config['system_config']['opensips'] == 0) {
                            echo "SIP Devices";
                        } else {
                            echo "Opensips Devices";
                        }
                        ?>   -->
		        <span id="error_msg" class=" success"></span>
                        <? if (common_model::$global_config['system_config']['opensips'] == 0) { ?>
                            <table id="sip_iax_grid" align="left" style="display:none;"></table>        
                        <? } else { ?>
                            <table id="opensips_grid" align="left" style="display:none;"></table>                 
                        <? } ?>
          </div>
             
</div>
    <!--Account Tab End-->

    <!-- Package table started -->
    <div id='packages'>
<!--      <div class="container">
          <div class="row">
            <section class="slice color-three no-margin">
                <div class="w-section inverse no-padding"> -->
                   <!--<div class="col-md-6 color-three padding-b-20 margin-t-10">
                        <form method="post" action="<?= base_url() ?>accounts/customer_add_postcharges/customer/<?= $account_data[0]['id'] ?>" enctype="multipart/form-data">
                            <label class="col-md-3">Description</label><input class="col-md-2 form-control" name="desc" size="16" type="text">
                            <label class="col-md-3">Amount</label><input class="col-md-2 form-control"  name="amount" size="8" type="text">
                            <input class="padding-l-20 btn btn-success" name="action" value="Post Charge..." type="submit">
                        </form>
                    </div> 
                    -->
                    <div class="col-md-12 color-three padding-t-10" style="padding-top:15px;">  <!-- Add Charge --><br/>
                        <form method="post" action="<?= base_url() ?>accounts/customer_charges_action/add/<?= $account_data[0]['id'] ?>/<?= $entity_name;?>/" enctype="multipart/form-data">
                             <label class="col-md-2">Applyable Subscriptions</label>
			      <div style="width:500px;">
                                <? echo $chargelist; ?>
			      </div>
                                <input class="margin-l-20 btn btn-success" name="action" value="Add Subscriptions" type="submit">
                        </form>
                    </div>   
                
                   <div class="col-md-12 color-three padding-b-20">
                       <table id="chrges_grid" align="left" style="display:none;"></table>  
                   </div>   
            <!--    </div>
            </section>
          </div>
      </div>-->
    </div>
    <div id='did'>
<!--         <div class="container">
          <div class="row">
            <section class="slice no-margin">
                <div class="w-section inverse no-padding col-md-12"> -->
                    <div class="col-md-12 color-three padding-t-10" style="padding-top:15px;"><br/><!-- Purchase DIDs -->
                        <form method="post" action="<?= base_url() ?>accounts/customer_dids_action/add/<?= $account_data[0]['id'] ?>/<?= $entity_name;?>/" enctype="multipart/form-data">
                        <label class="col-md-2">Available DIDs : </label>
                        <div style="width:500px;">
                            <? echo $didlist; ?>
			</div>
                        <input class="margin-l-20 btn btn-success" name="action" value="Purchase DID" type="submit">
                        </form>
                    </div>                 
                    <div class="col-md-12 color-three padding-b-20">
                          <table id="did_grid" align="left" style="display:none;"></table>  
                    </div>
                   
<!--                </div>
            </section>
    </div></div>
       </div> -->
    </div>
 <div id='invoices'>
<!--      <div class="container">
          <div class="row">
            <section class="slice no-margin">
                <div class="w-section inverse no-padding col-md-12"> -->
                      <div class="col-md-12 color-three padding-b-20">
                          <table id="invoice_grid" align="left" style="display:none;"></table>                 
                      </div>
<!--                </div>
             </section>
          </div>
      </div>-->
    </div>

   


    <div id='block_prefixes'>
<!--      <div class="container">
          <div class="row">
            <section class="slice no-margin">
                <div class="w-section inverse no-padding"> -->
                      <div class="col-md-12 color-three padding-b-20">
                         <table id="pattern_grid" align="left" style="display:none;"></table>                 
                      </div>
<!--                </div>
             </section>
          </div>
      </div> -->
    </div>
    
    
    <div id='cdrs'>
<!--      <div class="container">
          <div class="row">
            <section class="slice no-margin">
                <div class="w-section inverse no-padding"> -->
                    <div class="col-md-12 color-three padding-b-20">
                       <table id="cdrs_grid" align="left" style="display:none;"></table>              
                    </div>
<!--                </div>
             </section>
          </div>
      </div> -->
    </div> 
    <div id='animap'>
		<div class="col-md-12 color-three padding-b-20 padding-t-20">
                    <form method="post" name="ani_map" id="ani_map" action="<?= base_url() ?>accounts/customer_animap_action/add/<?= $account_data[0] ['id'] ?>/<?= $entity_name; ?>" enctype="multipart/form-data">
                              <!--  <input type="hidden" id="animap_id" name="animap_id" value="" />
                                <input type="hidden" name="id" id='id' value='' /> -->
                   <label class="col-md-1" style="padding-left:50px; width:10%;">Caller Id:</label>
				<input type="input" class="col-md-2 form-control" name="number" id="number" maxlength="20">
                       <!--         <label>Status: </label>
                                <select name="status" id="status" class="field select">
                                        <option value="0">Active</option>
                                        <option value="1">Inactive</option>
                                </select> -->
                                <input class="margin-l-20 btn btn-success" id="animap" name="action" value="
Caller Id" type="submit">
                         </form>
                </div>                 
		<div class="col-md-12 color-three padding-b-20">
			<table id="animap_list" align="left" style="display:none;"></table>
   		</div>
   
   
   
        </div> 
     
    </section>        
  </div>
</div>    
</div>
<? endblock() ?>	
<? startblock('sidebar') ?>
Filter by
<? endblock() ?>
<? end_extend() ?>  

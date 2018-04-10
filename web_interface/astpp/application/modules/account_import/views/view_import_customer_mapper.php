<?php extend('master.php') ?>
<? startblock('extra_head') ?>
<?php
   if (!isset($csv_tmp_data)) { ?>
<style>
	.w-section{
		text-align:none !important;
	}
	.w-box{
		display: inline-block;
		overflow: visible !important;
		
		}
</style>
<?php }?>	   
<?php endblock() ?>
<?php startblock('page-title') ?>
<?php echo $page_title; ?>
<?php endblock() ?>
<?php startblock('content') ?>
<?php
   if (!isset($csv_tmp_data)) { ?>
<section class="slice color-three padding-t-20">
   <div class="w-section">
      <div class="container">
         <form method="post" action="<?php echo base_url() ?>account_import/customer_import_preview/" enctype="multipart/form-data" id="account_import">
            <div class="row">
               <div class="col-md-12">
                  <div class="w-box">
                     <span class="padding-l-16" style="text-align: center;background-color: none;color:#DD191D;">
                     <?php
                        if (isset($error) && !empty($error)) {
                        	echo $error;
                        } ?>
                     </span>
                     <h3 class="padding-l-16">You must either select a field from your file OR provide a default value for the following fields:</h3>
                     <p>Account Number,Password,First Name,Last Name,Company,Phone,Mobile,Email,Address,city,Province/State,Zip/Postal Code,Number Translation,Out Callerid Translation,In Callerid Translation,Concurrent Calls,CPS,Balance,Credit Limit,SIP Username,SIP Password</p>
                     <i class="padding-l-16"><?php echo gettext("Note : Records with duplicate account number and email will be ignored.");?></i> 
                  </div>
               </div>
               <div class="col-md-12">
                  <div class="col-md-8">
                     <div class="w-box" style="z-index:11 !important;">
                        <h3 class="padding-t-10 padding-l-16 padding-b-10">Import Customers:</h3>
                        <div class="no-padding">
                           <input type="hidden" name="mode" value="import_account_mapper" />
                           <div class="col-md-6">
							   <div class='col-sm-12 form-inline form-group'>
									<div class='col-sm-6'>
										<label class="control-label">Generate Pin : </label>	 
									</div>
									<div class='col-sm-4'>
										<?php echo $config_array['pin']; ?>
									</div>
								</div>
								<div class='col-sm-12 form-inline form-group'>
									<div class='col-sm-6'>
										<label class="control-label">Allow Recording : </label>	 
									</div>
									<div class='col-sm-4'>
										<?php echo $config_array['is_recording']; ?> 
									</div>
								</div>
								<div class='col-sm-12 form-inline form-group'>
									<div class='col-sm-6'>
										<label class="control-label">Allow IP Management : </label>	 
									</div>
									<div class='col-sm-4'>
										<?php echo $config_array['allow_ip_management']; ?> 
									</div>
								</div>
								<div class='col-sm-12 form-inline form-group'>
									<div class='col-sm-6'>
										<label class="control-label">Rate Group : </label>	 
									</div>
									<div class='col-sm-4'>
										<?php echo $config_array['pricelist_id'];?>
									</div>
								</div>
								<div class='col-sm-12 form-inline form-group'>
									<div class='col-sm-6'>
										<label class="control-label">Create Sip Device : </label>	 
									</div>
									<div class='col-sm-4'>
										<?php echo $config_array['sipdevice_flag']; ?> 
									</div>
								</div>
								<div class='col-sm-12 form-inline form-group'>
									<div class='col-sm-6'>
										<label class="control-label">Timezone : </label>	 
									</div>
									<div class='col-sm-4'>
										<?php echo $config_array['timezone_id']; ?> 
									</div>
								</div>
								<div class='col-sm-12 form-inline form-group'>
									<div class='col-sm-6'>
										<label class="control-label">Country : </label>	 
									</div>
									<div class='col-sm-4'>
										<?php echo $config_array['country_id']; ?> 
									</div>
								</div>
								<div class='col-sm-12 form-inline form-group'>
									<div class='col-sm-6'>
										<label class="control-label">Currency : </label>	 
									</div>
									<div class='col-sm-4'>
										<?php echo $config_array['currency_id']; ?> 
									</div>
								</div>
                           </div>
                           <div class="col-md-6">
								<div class='col-sm-12 form-inline form-group'>
									<div class='col-sm-6'>
										<label class="control-label">Account Type : </label>	 
									</div>
									<div class='col-sm-4'>
										<?php echo $config_array['posttoexternal']; ?>
									</div>
								</div>
								<div class='col-sm-12 form-inline form-group'>
									<div class='col-sm-6'>
										<label class="control-label" for='sweep_id'>Billing Schedule : </label>	 
									</div>
									<div class='col-sm-4'>
										<?php echo $config_array['sweep_id']; ?> 
									</div>
								</div>
								<div class='col-sm-12 form-inline form-group'>
									<div class='col-sm-6'>
										<label class="control-label" for="invoice_day">Billing Day : </label>	 
									</div>
									<div class='col-sm-4'>
										<?php echo $config_array['invoice_day']; ?> 
									</div>
								</div>
								<div class='col-sm-12 form-inline form-group'>
									<div class='col-sm-6'>
										<label class="control-label">Allow Local Calls : </label>	 
									</div>
									<div class='col-sm-4'>
										<?php echo $config_array['local_call'];?>
									</div>
								</div>
								
								<div class='col-sm-12 form-inline form-group'>
									<div class='col-sm-6'>
										<label class="control-label">LC Charge/Min : </label>	 
									</div>
									<div class='col-sm-4'>
										<?php echo $config_array['charge_per_min']; ?>
									</div>
								</div>
								
								<div class='col-sm-12 form-inline form-group'>
									<div class='col-sm-6'>
										<label class="control-label">Tax: </label>	 
									</div>
									<div class='col-sm-4'>
										<?php echo $config_array['tax_id']; ?>
									</div>
								</div>
								<div class='col-sm-12 form-inline form-group'>
									<div class='col-sm-6'>
										<label class="control-label">Email Alerts ? : </label>	 
									</div>
									<div class='col-sm-4'>
										<?php echo $config_array['notify_flag']; ?>
									</div>
								</div>
							</div>
							<div class="col-sm-8 form-inline form-group padding-l-16">
									<div class="col-sm-4 padding-l-16">
										<label class="control-label" style="margin-left:12px;">Select the file : </label>	 
									</div>
									<div class="col-sm-4" style="margin-left:18px;">
										<div class="fileinput fileinput-new input-group" data-provides="fileinput">
											<div class="form-control" data-trigger="fileinput">
												<span class="fileinput-filename"></span>
											</div>
											<span class="input-group-addon btn btn-primary btn-file" style="display: inline-block;float:  right;position:  absolute;">
											<span class="fileinput-new">Select file</span>
											<input style="height:33px;" type="file" name="customer_import_mapper" size="35" id="customer_import_mapper"></span>
										</div> 
									</div>
							</div>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="w-box padding-b-10">
                       <div class="col-md-12 padding-t-20">
                               <label class="col-md-6" style="font-size:14px;text-transform:none !important;">Download sample file:</label>
                               <div class="col-md-6">
								   <a href="<?= base_url(); ?>account_import/customer_import_download_sample_file/" class="btn btn-success">Click Here</a>
								</div>
                       </div>
                   </div>
               </div>
            </div>
            <div class="col-md-12 padding-b-10">
               <div class="pull-right">
                  <input class="btn btn-line-parrot" type="submit" name="action" value="Import" />
                  <a href="<?php echo base_url() . 'accounts/customer_list/' ?>" >
                  <input class="btn btn-line-sky margin-x-10" id="ok" type="button" name="action" value="Cancel"/>
                  </a>
               </div>
            </div>
         </form>
      </div>
   </div>
   </div>
</section>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {

        $(".sweep_id").change(function(){
            var sweep_id =$('.sweep_id option:selected').val();
            if(sweep_id != 0){
                $.ajax({
                    type:'POST',
                    url: "<?= base_url() ?>/accounts/customer_invoice_option/<?= $invoice_date ?>",
                    data:"sweepid="+sweep_id, 
                    success: function(response) {
                        $(".invoice_day").html(response);
                        $('.selectpicker').selectpicker('refresh');
                        $('.invoice_day').show();
                        $('label[for="invoice_day"]').show()
                    }
                });
            }else{
                $('label[for="invoice_day"]').hide()
                $('.invoice_day').css('display','none');                
            }
        });
		$(".sweep_id").change();
    });
    /************************************************************************/       

</script>
<?php
   } ?>
<?php // echo "<pre>";	print_R($csv_tmp_data);exit;
   if (!empty($csv_tmp_data)) { ?>
<section class="slice color-three padding-b-20">
   <div class="w-section inverse no-padding">
      <div class="container">
         <div class="row">
         </div>
      </div>
      <div class="row">
         <div class="col-md-12">
            <form id="import_form" name="import_form" action="<?php echo base_url() ?>account_import/customer_import_data/" enctype="multipart/form-data" method="POST">
				<div class="col-md-6">
					<fieldeset>
						<legend>Account Information</legend>
						   <table class="table">
							  <thead>
								 <tr>
									<th>ASTPP Field</th>
									<th>DEFAULT VALUE</th>
									<th>Map to Field</th>
								 </tr>
							  </thead>
							  <tbody>
								 <?php  //echo "<pre>";	print_R($mapto_fields);exit;
									foreach($mapto_fields['general_info'] as $csv_key => $csv_value) {
											$custom_value = $csv_value."-select";
											$params_arr = array("id"=>$custom_value,"name"=>$custom_value,"class"=>$custom_value);
											echo "<tr>";
											echo "<td>" . $csv_key . '(' . $csv_value . ")</td>";
											echo "<td>$mapper_array[$csv_value]</td>";
											echo "<td>";
											if($csv_value != "number" && $csv_value !="email"){
												echo str_replace('col-md-5','col-md-12',str_replace('form-control','',form_dropdown_all ($params_arr,$file_data, '' )));
											}else{
												echo str_replace('col-md-5','col-md-12',str_replace('form-control','',form_dropdown($params_arr,$file_data, '' )));
											}	
											echo "</td>";
											echo "</tr>";

									} ?>
							  </tbody>
						   </table>
					</fieldeset>
               </div>
               <div class="col-md-6">
					<fieldeset>
						<legend>Billing Information</legend>
						<table class="table">
							<thead>
								 <tr>
									<th>ASTPP Field</th>
									<th>DEFAULT VALUE</th>
									<th>Map to Field</th>
								 </tr>
							  </thead>
							<tbody>
								<?php  //echo "<pre>";	print_R($mapto_fields);exit;
								foreach($mapto_fields['settings'] as $csv_key => $csv_value) {
									$custom_value = $csv_value."-select";
									$params_arr = array("id"=>$custom_value,"name"=>$custom_value,"class"=>$custom_value);
									echo "<tr>";
										echo "<td>" . $csv_key . '(' . $csv_value . ")</td>";
										echo "<td>$mapper_array[$csv_value]</td>";
										echo "<td>";
											echo str_replace('col-md-5','col-md-12',str_replace('form-control','',form_dropdown_all ($params_arr,$file_data, '' )));
										echo "</td>";
									echo "</tr>";

								} ?>
							</tbody>
						</table>
					</fieldeset>
				</div>
                <input type="hidden" name="post_array" value="<?php echo htmlspecialchars($post_array); ?>" />
				<input type="hidden" name="mode" value="import_customer_mapper" />
				<H2> Import File Data..</H2>
				<table width="100%" border="1"  class="details_table">
                  <?php
                     $cnt = 1;  //echo "<pre>";print_r($csv_tmp_data);exit;
                     foreach($csv_tmp_data as $csv_key => $csv_value) {   
                     	if ($csv_key <=5) {
                     		echo "<tr>";
                     		foreach($csv_value as $field_name => $field_val) {
                     			if ($csv_key == 0) {
                     				 echo "<th>".ucfirst($field_val)."</th>";
                     			}
                     			else {
                     				echo "<td class='portlet-content'>" . $field_val . "</td>";
                     				$cnt++;
                     			}
                     		}
                     		echo "</tr>";
                     	}
                     }
                     echo "<tr><td colspan='" . $cnt . "'>
                     <a href='" . base_url() . "accounts/customer_list/'><input type='button' class='btn btn-line-sky pull-right  margin-x-10' value='Back'/></a>
                                            <input type='submit' class='btn btn-line-parrot pull-right'' id='Process' value='Process Records'/></td></tr>";
                     ?>
               </table>
            </form>
         </div>
      </div>
   </div>
   </div>
</section>
<?php
   } ?>
<?php
   endblock() ?>
<?php
   end_extend() ?>

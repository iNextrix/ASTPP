<? extend('master.php') ?>
<?php error_reporting(E_ERROR); ?>
<? startblock('extra_head') ?>
<?php endblock() ?>
<?php startblock('page-title') ?>
<?= $page_title ?>
<?php endblock() ?>
<?php startblock('content') ?>
<script type="text/javascript">
    $("#submit").click(function(){
        submit_form("department_form");
    })
</script>
 
<!-- <div class="container">
        <div class="row">
		<section class="slice color-three">
			<div class="w-section inverse no-padding"> -->
<div class="">
	<section class="slice color-three">
		<div class="w-section inverse pb-4">
			<div class="content">
				<div class="col-md-12 p-0">
					<div class="pop_md col-12 pb-4 pt-2">
					<form action="<?= base_url()?>department/department_save/" accept-charset="utf-8" id="department_form" method="POST" name="department_form">	
						
						<div class="card-columns">
							<div class="card">
								<div class="pb-4 col-12" id="floating-label">
								  <h3 class="bg-secondary text-light p-3 rounded-top">Department List</h3>
									<div class="col-md-12">
									  <div class="row p-0">

										<div class="form-group col-md-12">
											<label class="col-md-12 p-0 control-label">Name<span style='color:red;'> *</span></label>
											<input type="text" name="name" value="<?php echo isset($values['name'])?$values['name']:"";?>" size="20" maxlength="40" class="col-md-12 form-control form-control-lg"  />
											<div class="tooltips error_div pull-left no-padding display_none" id="name_error_div" >
												<i style="color:#D95C5C; padding-left: 3px; padding-top: 10px;" class="fa fa-exclamation-triangle"></i>
												<span class="popup_error error  no-padding" id="name_error"></span>
											</div>
										</div>
										<div class="form-group col-md-12">
											<label class="col-md-12 p-0 control-label">Email Address
												<span style='color:red;'> *</span>
											</label>
											<input type="text" name="email_id" value="<?php echo isset($values['email_id'])?$values['email_id']:"";?>" size="50" class="col-md-12 form-control form-control-lg""  />
											<div class="tooltips error_div pull-left no-padding display_none" id="email_id_error_div" >
												<i style="color:#D95C5C; padding-left: 3px; padding-top: 10px;" class="fa fa-exclamation-triangle"></i>
												<span class="popup_error error  no-padding" id="email_id_error"></span>
											</div>
										</div>
										<div class="form-group col-md-12">
											<label class="col-md-12 p-0 control-label">Status</label>
											<select  id="select2_1"  name="status" data-live-search="true" class='col-md-12 form-control form-control-lg selectpicker' >
												<option value="0">Active</option>
												<option value="1">Inactive</option>
											</select>
											<div class="tooltips error_div pull-left no-padding display_none" id="status_error_div" >
												<i style="color:#D95C5C; padding-left: 3px; padding-top: 10px;" class="fa fa-exclamation-triangle"></i>
												<span class="popup_error error  no-padding" id="status_error"></span>
											</div>
                                        </div>
									  </div>
									</div>
								</div>
							</div>
						
							<div class="card">
								<div class="pb-4 col-12" id="floating-label">
								  <h3 class="bg-secondary text-light p-3 rounded-top">Department User</h3>
									<div class="col-md-12">
									  <div class="row p-0">
										<div class="form-group col-md-12">
											<label class="col-md-12 p-0 control-label">Admin</label>
												<select name="admin_user_id[]" multiple='multiple' class='select field multiselectable col-md-12 form-control form-control-lg selectpicker'>
												<?php foreach($drp_down as $key=>$val){  
														if(isset($admin_user_id_data) && isset($admin_user_id_data[$val['id']])){
															echo $selected="selected";
														}else{
															echo $selected="";
														}
												?> 
													<option value="<?php echo  $val['id']; ?>" <?php  echo $selected; ?>  ><?php echo  $val['first_name']."&nbsp;".$val['last_name']."(".$val['number'].")";  ?> </option>
												<?php }  ?>
												</select>
												<div class="tooltips error_div pull-left no-padding display_none" id="admin_user_id_error_div" >
													<i style="color:#D95C5C; padding-left: 3px; padding-top: 10px;" class="fa fa-exclamation-triangle"></i>
														<span class="popup_error error  no-padding" id="admin_user_id_error"></span>
												</div>
										</div>
										<div class="form-group col-md-12">
											<label class="col-md-12 p-0 control-label">Sub Admin</label>
												<select name="sub_admin_user_id[]" multiple='multiple' class='select field multiselectable col-md-12 form-control form-control-lg selectpicker'>
												<?php foreach($drp_downlist_subadmin as $val){  
														if(isset($subadmin_user_id_data) && isset($subadmin_user_id_data[$val['id']])){
															echo $selected="selected";
														}else{
															echo $selected="";
														}
												?> 
													<option value="<?php echo  $val['id']; ?>" <?php  echo $selected; ?> ><?php echo  $val['first_name']."&nbsp;".$val['last_name']."(".$val['number'].")";  ?> </option>
												<?php }  ?>
												</select>
												<div class="tooltips error_div pull-left no-padding display_none" id="sub_admin_user_id_error_div" >
													<i style="color:#D95C5C; padding-left: 3px; padding-top: 10px;" class="fa fa-exclamation-triangle"></i>
													<span class="popup_error error  no-padding" id="sub_admin_user_id_error"></span>
												</div>
										</div>
									</div>
								  </div>
								</div>	
							</div>
						
							<div class="card">
								<div class="pb-4 col-12" id="floating-label">
								  <h3 class="bg-secondary text-light p-3 rounded-top">SMTP Details</h3>
									<div class="col-md-12">
									  <div class="row p-0">
										<div class="form-group col-md-12">
											<label class="col-md-12 p-0 control-label">SMTP Host<span style='color:red;'> *</span></label>
											<input type="text" name="smtp_host" value="<?php echo isset($values['smtp_host'])?$values['smtp_host']:"";?>" size="20" maxlength="40" class="col-md-12 form-control form-control-lg"  />
											<div class="tooltips error_div pull-left no-padding display_none" id="smtp_host_error_div" >
												<i style="color:#D95C5C; padding-left: 3px; padding-top: 10px;" class="fa fa-exclamation-triangle"></i>
												<span class="popup_error error  no-padding" id="smtp_host_error"></span>
											</div>
										</div>
										<div class="form-group col-md-12">
											<label class="col-md-12 p-0 control-label">SMTP Port<span style='color:red;'> *</span></label>
											<input type="text" name="smtp_port" value="<?php echo isset($values['smtp_port'])?$values['smtp_port']:"";?>" size="20" maxlength="40" class="col-md-12 form-control form-control-lg"  />
											<div class="tooltips error_div pull-left no-padding display_none" id="smtp_port_error_div" >
												<i style="color:#D95C5C; padding-left: 3px; padding-top: 10px;" class="fa fa-exclamation-triangle"></i>
												<span class="popup_error error  no-padding" id="smtp_port_error"></span>
											</div>
										</div>
										<div class="form-group col-md-12">
											<label class="col-md-12 p-0 control-label">SMTP User<span style='color:red;'> *</span></label>
											<input type="text" name="smtp_user" value="<?php echo isset($values['smtp_user'])?$values['smtp_user']:"";?>" size="20" maxlength="40" class="col-md-12 form-control form-control-lg"  />
											<div class="tooltips error_div pull-left no-padding display_none" id="smtp_user_error_div" >
												<i style="color:#D95C5C; padding-left: 3px; padding-top: 10px;" class="fa fa-exclamation-triangle"></i>
												<span class="popup_error error  no-padding" id="smtp_user_error"></span>
											</div>
										</div>
										<div class="form-group col-md-12">
											<label class="col-md-12 p-0 control-label">SMTP Password <span style='color:red;'> *</span></label>
											<input type="password" name="smtp_password" value="<?php echo isset($values['smtp_password'])?$values['smtp_password']:"";?>" id="smtp_password_show" onmouseover="seetext(smtp_password_show)" onmouseout="hidepassword(smtp_password_show)" size="20" maxlength="40" class="col-md-12 form-control form-control-lg"  />
											<div class="tooltips error_div pull-left no-padding display_none" id="smtp_password_error_div" >
												<i style="color:#D95C5C; padding-left: 3px; padding-top: 10px;" class="fa fa-exclamation-triangle"></i>
												<span class="popup_error error  no-padding" id="smtp_password_error"></span>
											</div>
										</div>
									</div>
								  </div>
								</div>	
							</div>
						
							<div class="card">
								<div class="pb-4 col-12" id="floating-label">
								  <h3 class="bg-secondary text-light p-3 rounded-top">Department Additional Email Address</h3>
									<div class="col-md-12">
									  <div class="row p-0">
										<div class="form-group col-md-12">
											<label class="col-md-12 p-0 control-label">Email 1</label>										
											<input type="text" name="email_id_new1" value="<?php echo isset($values['email_id_new1'])?$values['email_id_new1']:"";?>" size="50" class="col-md-12 form-control form-control-lg"  />
											<div class="tooltips error_div pull-left no-padding display_none" id="email_id_new1_error_div" >
												<i style="color:#D95C5C; padding-left: 3px; padding-top: 10px;" class="fa fa-exclamation-triangle"></i>
												<span class="popup_error error  no-padding" id="email_id_new1_error"></span>
											</div>
										</div>
										<div class="form-group col-md-12">
											<label class="col-md-12 p-0 control-label">Email 2</label>
											<input type="text" name="email_id_new2" value="<?php echo isset($values['email_id_new2'])?$values['email_id_new2']:"";?>" size="50" class="col-md-12 form-control form-control-lg"  />
											<div class="tooltips error_div pull-left no-padding display_none" id="email_id_new2_error_div" >
												<i style="color:#D95C5C; padding-left: 3px; padding-top: 10px;" class="fa fa-exclamation-triangle"></i>
												<span class="popup_error error  no-padding" id="email_id_new2_error"></span>
											</div>
										</div>
										<div class="form-group col-md-12">
											<label class="col-md-12 p-0 control-label">Email 3</label>
											<input type="text" name="email_id_new3" value="<?php echo isset($values['email_id_new3'])?$values['email_id_new3']:"";?>" size="50" class="col-md-12 form-control form-control-lg"  />
											<div class="tooltips error_div pull-left no-padding display_none" id="email_id_new3_error_div" >
												<i style="color:#D95C5C; padding-left: 3px; padding-top: 10px;" class="fa fa-exclamation-triangle"></i>
												<span class="popup_error error  no-padding" id="email_id_new3_error"></span>
											</div>
										</div>
										<div class="form-group col-md-12">
											<label class="col-md-12 p-0 control-label">Email 4</label>
											<input type="text" name="email_id_new4" value="<?php echo isset($values['email_id_new4'])?$values['email_id_new4']:"";?>" size="50" class="col-md-12 form-control form-control-lg"  />
											<div class="tooltips error_div pull-left no-padding display_none" id="email_id_new4_error_div" >
												<i style="color:#D95C5C; padding-left: 3px; padding-top: 10px;" class="fafa-exclamation-triangle"></i>
												<span class="popup_error error  no-padding" id="email_id_new4_error"></span>
											</div>
										</div>
										<div class="form-group col-md-12">
											<label class="col-md-12 p-0 control-label">Email 5</label>
											<input type="text" name="email_id_new5" value="<?php echo isset($values['email_id_new5'])?$values['email_id_new5']:"";?>" size="50" class="col-md-12 form-control form-control-lg"  />
											<div class="tooltips error_div pull-left no-padding display_none" id="email_id_new5_error_div" >
												<i style="color:#D95C5C; padding-left: 3px; padding-top: 10px;" class="fa fa-exclamation-triangle"></i>
												<span class="popup_error error  no-padding" id="email_id_new5_error"></span>
											</div>
										</div>
									</div>
								  </div>
								</div>	
							</div>
							
							
						</div>
						<?php
						if (isset($validation_errors) && $validation_errors != '') { ?>
							<script>
								var ERR_STR = '<?php echo $validation_errors; ?>';
								print_error(ERR_STR);
							</script>
						<? } ?>
								<div class="col-12 my-4 text-center"> 
								<button name="action" type="submit" value="save" id="submit" class="btn btn-success" >Save</button>
								<button name="action" type="button" value="cancel" class="btn btn-secondary mx-2" onclick="return redirect_page('/department/department_list/')" >Close</button> 
								</div>
					</form>	   
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
<? endblock() ?>
<? startblock('sidebar') ?>
<? endblock() ?>
<? end_extend() ?>

<? extend('master.php') ?>
<? startblock('extra_head') ?>
<?php endblock() ?>
<?php startblock('page-title') ?>
<?= $page_title ?>
<?php endblock() ?>
<?php startblock('content') ?>
<div class="p-0">
    <section class="slice color-three">
		<div class="w-section inverse p-0">
           <div class="pop_md col-12 pb-4">
			<form action="<?=base_url()."systems/translation_save/";?>"  id="translation_id" method="post" name="translation_form">
			<div class="card pb-4" id="floating-label">
				<div class="col-md-12">
					
							<input name="id" value="" type="hidden">
							<div class="col-md-12 form-group">
							  <label class="col-md-3 p-0 control-label"><?php echo gettext('Module Name') ?><span>*</span></label>
							  <input type="text" name="module_name" value="<?php echo isset($details['module_name'])?$details['module_name']:"";?>" id='module_name' size="20" class="col-md-12 form-control form-control-lg"/>
							  <div class="tooltips error_div pull-left no-padding" id="module_name_error_div" style="display: none;"><i class="fa fa-exclamation-triangle error_triangle"></i><span class="popup_error error  no-padding" id="module_name_error"></span></div>   
							</div>   
							<?php
								$fields_data= $this->db->list_fields('translations');
								unset($fields_data ['0']);
								$this->db->from('languages');
								$query = $this->db->get();
								$query = $query->result_array();
								
								foreach ($query as $key => $value) {
									  if(isset($flag) && !empty($details)){
										  foreach ($details as $key => $value1) {
											  if ($value ['locale'] == $key) {?>
												<div class="col-md-12 form-group">      
												  <label class="col-md-3 p-0 control-label"><?php echo gettext($value ['name']);?><span>*</span></label>
												  <input type="text" name="<?php echo $value ['locale']; ?>" value="<?php echo $value1;?>" id='<?php echo $value ['locale']; ?>' size="20" class="col-md-12 form-control form-control-lg"/>     
												  <div class="tooltips error_div pull-left no-padding" id="<?php echo $value ['locale']; ?>_error_div" style="display: none;"><i class="fa fa-exclamation-triangle error_triangle"></i><span class="popup_error error p-0" id="<?php echo $value ['locale']; ?>_error"></span></div>   
											   </div>   
											<?php
											  }
										  }
										  
									  }else{
										  foreach ($fields_data as $key => $value1) {
											  if ($value ['locale'] == $value1) {?>
												<div class="col-md-12 form-group">      
												  <label class="col-md-3 p-0 control-label"><?php echo gettext($value ['name']);?><span>*</span></label>
												  <input type="text" name="<?php echo $value ['locale']; ?>" value="" id='<?php echo $value ['locale']; ?>' size="20" class="col-md-12 form-control form-control-lg"/>     
												  <div class="tooltips error_div pull-left no-padding" id="<?php echo $value ['locale']; ?>_error_div" style="display: none;"><i class="fa fa-exclamation-triangle error_triangle"></i><span class="popup_error error p-0" id="<?php echo $value ['locale']; ?>_error"></span></div>   
											   </div>   
											<?php
											  }
										  }
									  }
								  }
							?>
					</div>
				</div>
			</div>
					<div class="col-12 my-4 text-center">
					  <button  type="submit" value="save" id="submit" class="btn btn-success"><?php echo gettext('Save') ?></button>
					  <button  type="button" value="cancel" class="btn btn-secondary ml-2" onclick="return redirect_page('/systems/translation_list/')"><?php echo gettext('Cancel') ?></button>
					</div>
		  	</form>
		  	<?php 
					if (isset($validation_errors) && $validation_errors != '') { 
						?>
						<script>
							var ERR_STR = '<?php echo $validation_errors; ?>';
							print_error(ERR_STR);
						</script>
			<? } ?>
 			</div>
 </section>
</div>  
<? endblock() ?>
<? startblock('sidebar') ?>
<? endblock() ?>
<? end_extend() ?>

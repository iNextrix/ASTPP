<? extend('master.php') ?>
<? startblock('extra_head') ?>
<? endblock() ?>
<? startblock('page-title') ?>
<?= $page_title; ?>
<? endblock() ?>
<? startblock('content') ?>
<script type="text/javascript">
	$(document).ready(function() {
		 $("#import_database").submit(function(e) {
				err='';
				if($("#fname").val() == ''){
					$("#err").html('<?php echo gettext("The Name field is required."); ?>');
					$("#remove_div").css({"margin-bottom":"0rem"});
					$("#err_div").css({"height":"10px"});
					err='true';
				}
				if($("#userfile").val() == ''){
					$("#userfile_err").html('<?php echo gettext("Please select file."); ?>');
					$("#err_div").css({"margin-bottom":"0rem"});
					$("#err_file_div").css({"height":"10px"});
					err='true';
				}
				if(err == ''){
					return true;
				}else{
					return false;
				}
		 });
		$("#userfile").change(function(e) {
			var fuData = document.getElementById('userfile');
			var FileUploadPath = fuData.value;
			if (FileUploadPath != '') {
			    var Extension = FileUploadPath.substring(FileUploadPath.lastIndexOf('.') + 1).toLowerCase();
			    if ( Extension != "gz") {
				// alert("Database import allows only gzfile types of file.");
				$("#userfile_err").html('<?php echo gettext("Database import allows only gzfile types of file."); ?>');
				$("#err_div").css({"margin-bottom":"0rem"});
				$("#err_file_div").css({"height":"10px"});
				$("#userfile").val('');
				$('.custom-file-label').html('');
			    }else{
				var fileName = e.target.files[0].name;
				$('.custom-file-label').html(fileName);
				$("#userfile_err").html('');
				$("#err_div").css({"margin-bottom":"1rem"});
			}
			}

		    });
	});
</script>
<section class="slice color-three">
	<div class="w-section inverse p-0">
		<form method="post"
			action="<?= base_url()?>systems/database_import_file/"
			enctype="multipart/form-data" id="import_database">
			<div class="row">
				<div class="col-md-12">
					<div class="col-md-12 clo-sm-12 float-left p-0">
						<div class="w-box card py-3">
							<span style="margin-left: 10px;">
							<?
								if (isset($error) && ! empty($error)) {
										echo "<span class='row alert alert-danger m-2'>" . $error . "</span>";
								}
								?>
						   </span>
							<h3 class="px-4"><?php echo gettext("File must be in (.gz) format :"); ?></h3>
						   
						 </div>
					</div>
					
				</div>
				<div class="col-md-12">
					<div class="card col-md-12 p-0 mb-4">
						<div class="pb-4" id="floating-label">
							<h3 class="bg-secondary text-light p-3 rounded-top"><?php echo gettext("Import Database"); ?></h3>
							<div class="col-md-6 form-group float-left" id="remove_div">
									<label class="col-md-4 p-0 control-label"><?php echo gettext("Name"); ?> </label>
									<input type="text" name="fname" id="fname" value="" class="col-md-8 form-control pr-form-control form-control-lg" />
									<div>
								
									</div>
							</div>
							<div class="col-md-12 form-group" id="err_div"  style="height: 0px; margin-top:0px;">
									<span id="err" style="color:red;"></span>
							</div>
							<div class="col-md-12 form-group" id="file_div">
								<label class="col-12 control-label mb-4"><?php echo gettext("Select the file"); ?></label>
								<div class="col-12 mt-4 d-flex">
									<div class="col-md-4 float-left" data-ripple="">
										<input type="file" name="userfile"
											id="userfile"
											class="custom-file-input fileupload" /> <label
											class="custom-file-label btn-primary btn-file text-left"
											for="file"> </label>
									</div>
									</div>
								</div>
									<div class="col-md-12 form-group" id="err_file_div" style="height: 0px; margin-top:0px;">
									<span id="userfile_err" style="color:red;"></span>
								</div>
									<div class="col-md-6 float-left align-self-center">
										<span id="welcomeDiv" class="answer_list float-left d-none">
											<button type="button" title="Cancel" class="btn btn-danger"><?php echo gettext("Remove"); ?></button>
										</span>
									
							</div>

						</div>
					</div>
				</div>
				<div class="col-md-12">
					<div class="text-center">
						<button class="btn btn-success" id="import_origination_rate"
							type="submit" name="action" value="Import"><?php echo gettext("Import"); ?></button>
						<button class="btn btn-secondary mx-2" id="ok" type="button"
							name="action" value="Cancel"
							onclick="return redirect_page('/systems/database_restore/')" /><?php echo gettext("Cancel"); ?></button>
					</div>
				</div>
			</div>
		</form>
	</div>
</section>
<? endblock() ?>	
<? end_extend() ?>  


<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery.validate.min.js"></script>
<script type="text/javascript">
   $(document).ready(function() {
		
        $.validator.addMethod('Extension_verify', function(value) {
	    var extension_list = /(\.csv|\.tar.gz|\.sql|\.sql.gz)$/i;
	    if(!extension_list.exec(value)){
	      return false;
	    }else{
	      return true;
	    }
        }, 'Select File have not valid extension.');
        $("#database_import").validate({
            rules: {
                fname:{
                  required:true
                },
                userfile: {
                    required: true,
                    Extension_verify:true
                }
            },
            messages:{
             fname:{
              required : "<?=gettext('The Name field is required.');?>"
             },
             userfile:{
              required : "<?=gettext('Please select file.');?>"
             }
            },
				errorPlacement: function(error, element) {
				if (element.attr("name") == "fname" ) {
					$("#err").text($(error).text());
				}
				
				if (element.attr("name") == "userfile" ) {
					$("#file_err").text($(error).text());
				}
				
				}
			 	
        });
        
        $(".close").click(function(){
			$(".popup").hide();
		});	
    });
</script>
<style>
    #file_err,#fname_err
    {
         height:20px !important;width:100% !important;float:left;
         text-transform:none !important;
    }
    label.error {
        float: left; color: red;
        padding-left: .3em; vertical-align: top;  
        padding-left:0px;
        margin-top:-10px;
        width:100% !important;
       
    }
    input[type=file] {
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      width: 150px;
    }
</style>
<body style="width:500px;">
<section class="slice no-margin">
 <div class="w-section inverse no-padding">
   <div>
     <div>
        <div class="col-md-12 no-padding">
	        <h3 class="text-dark fw4 pl-4 pr-4 pt-4 rounded-top"><? echo $page_title; ?></h3 class="bg-secondary text-light p-3 rounded-top">
	        <a  class="close"><img alt="close_image" src="/assets/images/closelabel.png" class="close_image" title="close" style="height:15px;width:15px; margin:15px;"></a>
		</div>
     </div>
    </div>
  </div>    
</section>
<div>
  <div>
    <section class="slice no-margin">
		<div class="w-section inverse no-padding">
				<div class="pop_md col-12 pl-4 pr-4 pb-4 pt-2">
					<form id="database_import" name='database_import' action="<?= base_url()?>systems/database_import_file/" method="post"  enctype="multipart/form-data" >
						<div class="col-12 p-0">
							<div class="col-12 p-0">
								<ul class="card p-0">
									<div id="floating-label" class="pb-4">
										<h3 class="bg-secondary text-light p-3 rounded-top"><?php echo gettext('Import Database') ?></h3>
							
										<li class="col-md-12">
											<li class='col-md-12 form-group'>   
												<label class="col-md-3 no-padding control-label"><?php echo gettext('Name') ?> *:</label> 
												<input type="text" name="fname" id='fname' class="col-md-12 form-control form-control-lg"/>    
											</li>
											<span class="ml-3" id="err" style="color:red;"></span>
										</li>
										 <li class="col-md-12 ">
												<li class="col-sm-6">
																<label class="col-md-2 control-label pl-0"><?php echo gettext('Select File') ?> :</label>	 
																<span class="fileinput-filename"></span>
																<span class="btn btn-primary btn-file w-50 float-right">
																	<span class="fileinput-new"><?=gettext('Select file');?></span>
																		<input type="file" name="userfile" id="userfile">
																</span>
												</li>
												<span class="ml-3" id="file_err" style="color:red;"></span>
											</li>
											<li class="col-md-12">   
												<h5 style='font-weight:normal;color:#aa4940;margin-left:25%;text-transform:none !important;'><?php echo '('.gettext('Allowed file format is').' : .csv, .tar.gz, .sql)' ?></h5>
											</li>
									</div>
								</ul>	
							</div>
						</div> 
						<div class="col-12 margin-t-20 margin-b-20">
											<center>
												<input type="submit" class="btn btn-line-parrot btn-lg" name="action" value="<?=gettext('Upload');?>" />
											</center>
										</div>
					</form>
				</div>
		</div>      
    </section>
  </div>
</div>
<script type="text/javascript" language="javascript">
$(document).ready(function() {
	
    $("input[type='hidden']").parents('li.form-group').addClass("d-none");
  
  
});

</script>


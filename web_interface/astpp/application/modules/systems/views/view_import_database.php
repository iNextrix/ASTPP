<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/validate.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $.validator.addMethod('Extension_verify', function(value) {
	    var extension_list = /(\.csv|\.tar.gz|\.sql)$/i;
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
              required : "The Name field is required."
             },
             userfile:{
              required : "Please select file."
             }
            },
            errorPlacement: function(error, element) {
	      var placement = $(element).data('error');
	      if (placement) {
		$(placement).append(error)
	      } else {
		error.insertAfter(element);
	      }
	     }
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
<section class="slice gray no-margin">
 <div class="w-section inverse no-padding">
   <div>
     <div>
        <div class="col-md-12 no-padding margin-t-15 margin-b-10">
	        <div class="col-md-10"><b><? echo $page_title; ?></b></div>
	  </div>
     </div>
    </div>
  </div>    
</section>
<div>
  <div>
    <section class="slice color-three no-margin">
	<div class="w-section inverse no-padding">
	<form id="database_import" name='database_import' action="<?= base_url()?>systems/database_import_file/" method="post"  enctype="multipart/form-data" >
	<fieldset>
            <legend>Import Database</legend>
	  <ul class="padding-15">
	    <li class="col-md-12">   
		<label class="col-md-3 no-padding">Name *:</label> 
		<input type="text" name="fname" id='fname' class="col-md-5 form-control" data-error="#fname_err" />    
		<div class='col-md-12' style='margin:0px 0 0px 100px;'>
		      <span id="fname_err"></span>
		</div>
	    </li>
	    <li class="col-md-12">   
		<label class="col-md-3 no-padding">Select File:</label>     
		<div class="col-md-5 no-padding">
		  <div class="fileinput fileinput-new input-group" data-provides="fileinput">
		    <div class="form-control" data-trigger="fileinput">
			<span class="fileinput-filename"></span>
		    </div>
		    <span class="input-group-addon btn btn-primary btn-file" style="display: table-cell;">
		      <span class="fileinput-new">Select file</span>
		      <input style="height:33px;" name="userfile"   id="userfile" type="file"  data-error="#file_err"/>
		    </span>
		  </div>
		</div>
	    </li>
	    <div class='col-md-12' style='margin:10px 0 0px 115px;'>
		 <span id="file_err"></span>
	    </div>  
	    <li class="col-md-12" style='margin-top:0px;'>   
		  <h5 style='font-weight:normal;color:#aa4940;margin-left:25%;text-transform:none !important;'>(Allowed file format is : .csv, .tar.gz, .sql)</h5>
	    </li>	  
	</ul>	
	<div class="col-md-12">
	  <center>
		<input type="submit" class="btn btn-line-parrot" name="action" value="Upload" />
	  </center>
        </div>
        </fieldset>
      </form>
      </div>      
    </section>
  </div>
</div>



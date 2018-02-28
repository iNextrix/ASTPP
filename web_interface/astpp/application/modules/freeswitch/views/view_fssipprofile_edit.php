<? extend('master.php') ?>
<? startblock('extra_head') ?>


<script type="text/javascript">
$(document).ready(function() {
  build_grid("sip_profile_grid","<?php echo base_url(); ?>freeswitch/fssipprofile_params_json/<?=$edited_id?>",<? echo $grid_fields ?>,'');
})
   function validateForm(){
	var formflag = true;
         
      var ipaddress = document.getElementById("sip_ip").value;
      var pattern =/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/; 
      var match = ipaddress.match(pattern);
      var val=document.getElementById('sip_port').value;
      var length1=val.length;
// 
      var numbers  = isNaN(val);
      
      if(document.getElementById('sip_port').value == "")
      {
//           
	  $('#error_msg_port').text( "Please Enter SIP port" );
	  document.getElementById('sip_port').focus();
	  formflag = false;
	  //return false;
      }
      
      if(document.getElementById('sip_ip').value == "")
      {
//           
	  $('#error_msg_ip').text( "Please Enter SIP IP" );
	  document.getElementById('sip_ip').focus();
	  formflag = false;
	  //return false;
      }
//       if(match == null)
//       {
// //           
// 	  $('#error_msg_ip').text( "Please Enter Valid sip IP" );
// 	  document.getElementById('sip_ip').focus();
// 	  return false;
//       }
      
      
      if(document.getElementById('sip_name').value == "")
      {
//           
	  $('#error_msg_sip').text( "Please Enter Name" );
	  document.getElementById('sip_name').focus();
	  formflag = false;
	  //return false;
      }
      
      if(numbers == true)
      {
//           
	  $('#error_msg_port').text( "Please Enter Numeric value" );
	  document.getElementById('sip_port').focus();
	  formflag = false;
	  //return false;
      }
      if(length1 > 5)
      {
         $('#error_msg_port').text( "Please Enter port not exceed 5 number" );
	  document.getElementById('sip_port').focus();
	  formflag = false;
	  //return false;
      }
      if(formflag){
      $('#myForm1').submit();
  }
       
}
function cancel(){

       
      $('#paramname').val('');
      $('#paramvalue').val(''); 
}
</script>
<?php endblock() ?>
<?php startblock('page-title') ?>
<?= $page_title ?>
<?php endblock() ?>
<?php startblock('content') ?>




<div>
  <div>
    <section class="slice color-three">
	<div class="w-section inverse no-padding">
	    <div class="container">
        	<div class="row">
                  <div class="col-md-12 spacer" style='margin-top:15px;' align=center > 
		      <div style="color:red;margin-left: 60px;">
			  <?php if (isset($validation_errors)) {
	echo $validation_errors;
}
?> 
		      </div>
		     <form method="post" action="<?= base_url() ?>freeswitch/fssipprofile_add/edit/" enctype="multipart/form-data" name='form1' id ="myForm1">
			<input type='hidden' name='id' value="<?=$id?>" />
			<div class='col-md-12'><div style="width:550px;" ><label style="text-align:right;" class="col-md-3">Name *</label><input class="col-md-5 form-control" value="<?=$sip_name; ?>" id="sip_name" name="name" size="20" type="text"></div>
			<span style="color:red;margin-left: 10px;float:left;" id="error_msg_sip"></span>
			</div>
			<div class='col-md-12'>  
			<div style="width:550px;" ><label style="text-align:right;" class="col-md-3">SIP IP  *</label><input class="col-md-5 form-control " value="<?=@$sip_ip; ?>" name="sip_ip" size="20" id="sip_ip" type="text"></div>
			<span style="color:red;margin-left: 10px;float:left;" id="error_msg_ip"></span>
			</div>
			<div>
			<div style="width:550px;"><label style="text-align:right;"  class="col-md-3">SIP Port *</label><input class="col-md-5 form-control" value="<?=@$sip_port; ?>" name="sip_port" size="20" id="sip_port" type="text"></div>
			<span style="color:red;margin-left: 10px;float:left;" id="error_msg_port"></span>
			</div>
			<div class='col-md-12'>
			<div style="width:550px;"><label style="text-align:right;" class="col-md-3">Status </label>
			<select name="sipstatus" class="col-md-5 form-control selectpicker" data-live-search='true'>
			    
			    <option value="0" <?if ($status == 0)echo 'selected=selected;'?>>Active</option>
			    <option value="1" <?if ($status == 1)echo 'selected=selected;'?>>Inactive</option>
			  </select>
                       </div>
		       </div>
			<div class='col-md-12'>	
			<div class='col-lg-12 padding-t-10 padding-b-10'><input class="btn btn-line-parrot" name="action" value="Save" type="button" onclick="validateForm();">
			<input class="btn btn-line-sky  margin-x-10" onclick="javascript:window.location ='<?= base_url() ?>/freeswitch/fssipprofile/'" name="action" value="Cancel" type="button"></div>
			</div>
		    </form>
        </div>
        </div></div></div>
    </section>
  </div>
</div>

	   





<section class="slice color-three">
	<div class="w-section inverse no-padding">
    	<div class="container">
   	    <div class="row">
            	<div class="portlet-content"  id="search_bar" style="cursor:pointer; display:none">
                    	<?php echo $form_search;

?>
    	        </div>
            </div>
        </div>
    </div>
</section>

<section class="slice color-three padding-b-20">
	<div class="w-section inverse no-padding">
    	<div class="container">
        	<div class="row">
                <div class="col-md-12" style='margin-top:15px;'>      
                        
                       
                        <div style="color:red;margin-left: 60px;">
			<?php if (isset($validation_errors)) {
	echo $validation_errors;
}
?> 
		    </div>
                    <form method="post" name="form2" id="form2" action="<?= base_url() ?>freeswitch/fssipprofile_edit" enctype="multipart/form-data">
			<input type='hidden' name='id' value=<?=$id?> />
			<input type='hidden' name='type' value='save' />
			<div style="col-md-5">
			          <div class="col-md-1">

			          <label class="col-md-12 no-padding" style='padding-left:10px;'>Name  </label>
			          </div>
			          <div class='col-md-2'>
			          <input class="col-md-12 form-control" value="<?=$params_name?>" name="params_name" id='params_name' size="25" type="text">
			          </div>
		          
			</div>
			 <div style="col-md-5">
			          <div class='col-md-1'> 
			          <label class="col-md-12" style="padding-left:10px;">Value  </label>
			           </div>
			          <div class='col-md-2'>
			          <input class="col-md-12 form-control" value="<?=$params_value?>" name="params_value" id='params_value' size="25" type="text">
			           </div>
			          
			</div>
			<?
			  if($params_name!='')
			  {
				$type="edit_setting";
			  }else{
				$type="add_setting";
			  }
			?>
			<input type='hidden' name='type_settings' value='<?=$type?>' />
			<div class='col-md-5' style='padding-left:10px;'><input class="btn btn-success" name="action" value="<?=$button_name?>" type="submit">
			<input class="btn  btn-primary"  name="action" value="Reset" type="button" onclick="cancel();"></div>
		    </form>
		    </div>
			<span style="color:red;margin-left: 13%;float:left;" id="error_msg_params_name"></span>
			<span style="color:red;margin-left: 41%;float:left;" id="error_msg_params_value"></span>
		    <div class='col-md-12'>
		     <form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">
                            <table id="sip_profile_grid" align="left"></table>
                        </form>
                </div>  
            </div>
        </div>
    </div>
</section>

<? endblock() ?>
<? startblock('sidebar') ?>
<? endblock() ?>
<? end_extend() ?>

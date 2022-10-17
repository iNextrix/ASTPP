<?php include(FCPATH.'application/views/popup_header.php'); ?>
<script type="text/javascript">
	function uploadfile_broadcast(){
		filetype = document.forms["voice_broadcast_form"]["broadcast"].value;
		var fileName = document.getElementsByName('broadcast');
		var filenamevalue = fileName[0].files[0].name;
		var ext = (/[.]/.exec(filetype)) ? /[^.]+$/.exec(filetype) : undefined;
		if(ext != '')
			{
				if(ext == "wav" )
				{
					$('.broadcast-label').html(filenamevalue);
					document.getElementById("broadcast_error").innerHTML =""; 
					flag =1;
				}
				else
				{
					$('.broadcast-label').html(filenamevalue);
					document.getElementById("broadcast_error").innerHTML ="<?php echo gettext('Please upload only .wav file.')?>";
					$('#broadcast').val(""); 
					return false;
					flag =0;
				}
				if(fileName[0].files[0].size > 3276800){
					$('.broadcast-label').html(filenamevalue);
          $("#submit").attr('disabled','disabled');
					document.getElementById("broadcast_error").innerHTML ="<?php echo gettext('Please upload maximum 25 MB of .wav file.')?>";
          $('#broadcast_error_div').css("display","block");
					$('#broadcast').val(""); 
					return false;
					flag =0;
				}else{
          $('#submit').removeAttr("disabled");
          $('#broadcast_error_div').css("display","none");
        }
			}
	}
  function uploadfile_destination(){
		filetype = document.forms["voice_broadcast_form"]["destination_number"].value;
		var fileName = document.getElementsByName('destination_number');
		var filenamevalue = fileName[0].files[0].name;
		var ext = (/[.]/.exec(filetype)) ? /[^.]+$/.exec(filetype) : undefined;
		if(ext != '')
			{
				if(ext == "csv" )
				{
					$('.destination-label').html(filenamevalue);
					document.getElementById("destination_number_error").innerHTML =""; 
					flag =1;
				}
				else
				{
					$('.destination-label').html(filenamevalue);
					document.getElementById("destination_number_error").innerHTML ="<?php echo gettext('Please upload only .csv file.')?>";
					$('#destination_number').val(""); 
					return false;
					flag =0;
				}
			}
	}
  </script>
<script type="text/javascript">
$('#voice_broadcast_form').submit(function(e){
  e.preventDefault();
  $.ajax({
    url:'<?php echo base_url();?>voice_broadcast/voice_broadcast_save/',
    type:"post",
    data:new FormData(this),
    processData:false,
    contentType:false,
    cache:false,
    async:false,
    success: function(data){
      var tmp = jQuery.parseJSON(data);
      if (!tmp.SUCCESS) {
        var myObject = eval('(' + data + ')');
        for (i in myObject) {
          var fieldname = i.replace("_error", "");
          $("input[name='" + fieldname + "']").addClass("borderred");
          $("#" + i + "_div").css("display", "block");
          $("#" + i).html(gettext_custom(capitalizeFirstLetter(myObject[i])));
        }
      }else{
        $("#toast-container").css("display", "block");
        $(".toast-message").html(tmp.SUCCESS);
        $('.toast-top-right').delay(5000).fadeOut();
        $(document).trigger('close.facebox');
        $('.flex_grid').flexReload();
      }
      },error : function(error){
        alert(error);
      }
  });
});    
</script>
<section class="slice m-0">
 <div class="w-section inverse p-0">
   <div>
     <div>
        <div class="col-md-12 p-0 card-header">
          <h3 class="fw4 p-4 m-0"><? echo $page_title; ?></h3 class="text-light p-3 rounded-top">
    </div>
     </div>
    </div>
  </div>    
</section>
<div>
  <div>
      <section class="slice m-0">
     <div class="w-section inverse p-4">
            <div style="">
                <?php if (isset($validation_errors)) {
                  echo $validation_errors;
                } ?>
            </div>
            <?php echo $form; ?>
        </div>
      </section>
  </div>
</div>
<script type="text/javascript" language="javascript">
$(document).ready(function() {
    $("input[type='hidden']").parents('li.form-group').addClass("d-none");
    // $("input[type='file']").parent('li.form-group').find('label').addClass("custom-file-label btn-primary btn-file text-left");
});

$("#reseller_id_search_drp").change(function() {
  if(this.value!=""){
    doAjaxCall("voice_broadcast/customer_depend_list/",this.value,"accountid_search_drp","reseller_id");
    get_sip_devices($("#accountid_search_drp").val());
  }
});
$("#accountid_search_drp").change(function() {
    get_sip_devices(this.value);
});
function get_sip_devices(accountid){
  if(accountid!=""){
    doAjaxCall("voice_broadcast/voice_broadcast_sip_devices_list/",accountid,"sip_device_id_search_drp","accountid");
  }
}
function doAjaxCall(url,value,id,data){
  $.ajax({
    type:'POST',
    url: "<?= base_url()?>"+url,
    data:data+"="+value,
    async : false,
    success: function(response) {
      $("select#"+id).html(response);
      $('select#'+id).selectpicker('refresh');
    }
  });
}
$('input[type="file"]').addClass('custom-file-input');
$('input[type="file"]').closest('li.form-group').find('label').addClass('btn-file text-left');
$('input[type="file"]').closest('li.form-group').addClass('file-field');
$('input[type="file"]').wrap('<div class="col-12 mt-4 d-flex"><div data-ripple="" class="col-md-12 position-relative float-left">');
$('<label class="custom-file-label btn-primary btn-file text-start" for="file"></label>').insertAfter('li.form-group div.position-relative input[type="file"]');
$('input[id="broadcast"]').next('label.custom-file-label').addClass('broadcast-label');
$('input[id="destination_number"]').next('label.custom-file-label').addClass('destination-label');
$('.file-field label').contents().filter(function () {
  return this.nodeType === 3;
}).wrap('<span></span>');
</script>
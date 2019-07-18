<script type="text/javascript" language="javascript">
    function get_destination(id)
    {

$.ajax({
    type:'POST',
    url: "<?= base_url()?>accounts/customer_animap_list_edit/"+id,
    success: function(response) {
        var final_data=response.split(",");
        document.getElementById('id').value=final_data[0];
        document.getElementById('number').value=final_data[1];
    }
});
}
function get_alert_msg_destination(id){
    confirm_string = 'are you sure to delete?';
    var answer = confirm(confirm_string);
    var url ="<?= base_url()?>accounts/customer_animap_list_remove/"+id;
    if(answer){
      $.ajax({
        type:'POST',
        url: url,
        success: function(response) {
         $('.flex_grid').flexReload();
         if(response == 1){
             document.getElementById('added').innerHTML='';
             document.getElementById('deleted').innerHTML="<?php echo gettext('ANIMAP deleted successfully.'); ?>";
             document.getElementById('already').innerHTML="";
         }
     }
 }); 
  }
}
$(document).ready(function() {
    build_grid("animap_list","<?php echo base_url(); ?>accounts/customer_animap_list_json/<?=$animap_id; ?>",<? echo $grid_fields ?>,<?= $grid_buttons ?>);
    $("#animap").click(function(){
        var b=document.getElementById('id').value;
        document.getElementById('error_1').innerHTML="";
        document.getElementById('added').innerHTML="";
        document.getElementById('already').innerHTML="";
        document.getElementById('deleted').innerHTML="";
        var getvalue = document.getElementById("number").value;    
        if(isNaN(getvalue)|| getvalue==''){
            document.getElementById('error_1').innerHTML="Please Enter Only Numbers";
        }
        else{

           $.ajax({
            type:'POST',
            url: "<?= base_url()?>accounts/customer_animap_list_action/<?=$animap_id; ?>",
            data:$('#ip_map').serialize(), 
            success: function(response) {
                document.getElementById('number').value="";
                $('.flex_grid').flexReload();
                document.getElementById('id').value="";
                if(response==3){
                    document.getElementById('error_1').innerHTML="Please Enter Only Numbers";                
                }
                if(response == 2){
                    document.getElementById('already').innerHTML="ANI already exist in a list.";
                }
                if(response == 0){
                 document.getElementById('added').innerHTML="ANI added successfully...";
             }
             if(response ==1){
                 document.getElementById('added').innerHTML="ANI updated successfully...";
             }
         }
     });
       }
   });
    $("#custom_toggle1").click(function(){
        $("#search_bar1").toggle();
    });
});
</script>
<style>
.content {
	width: 750px;
}
</style>
<section class="slice gray no-margin">
	<div class="w-section inverse no-padding">
		<div>
			<div>
				<div class="col-md-12 no-padding margin-t-15 margin-b-10">
					<div class="col-md-10">
						<b><?php echo gettext("ANI Mapping");?>:</b>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="slice color-three padding-b-20">
	<div class="row">
		<div class="col-md-12">
			<form method="post" name="ip_map" id="ip_map" action=""
				enctype="multipart/form-data">
				<input type="hidden" id="animap_id" name="animap_id"
					value="<?=$animap_id; ?>" /> <input type="hidden" name="id" id='id'
					value='' /> <label><?php echo gettext("ANI"); ?><span style="color: red"> *</span>:
				</label> <input type="input" name="number" id="number"
					maxlength="15"> <label><?php echo gettext("Status"); ?>: </label> <select name="status"
					id="status" class="field select">
					<option value="0"><?php echo gettext("Active");?></option>
					<option value="1"><?php echo gettext("Inactive");?></option>
				</select> <input class="margin-l-20 btn btn-success" id="animap"
					name="action" value="Map ANI" type="button">
			</form>
		</div>
	</div>
</section>
<div id="success_1" style="display: block;">
	<div class="" style="margin-top: 10px; margin-left: 15px;">
		<span style="color: blue;"><font><span id="added"></span></font></span>
	</div>
	<div class="" style="margin-top: 10px; margin-left: 15px;">
		<span style="color: blue;"><font><span id="already"></span></font></span>
	</div>
	<div class="" style="margin-top: 10px; margin-left: 15px;">
		<span style="color: red;"><font><span id="deleted"></span></font></span>
	</div>
	<div class="" style="margin-top: 10px; margin-left: 15px;">
		<span style="color: red;"><font><span id="error_1"></span></font></span>
	</div>
</div>

<form method="POST" action="del/0/" enctype="multipart/form-data"
	id="ListForm">
	<table id="animap_list" align="left" style="display: none;"></table>
</form>


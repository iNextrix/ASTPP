
<? extend('master.php') ?>
<?php error_reporting(E_ERROR); ?>
<? startblock('extra_head') ?>
<?php endblock() ?>
<?php startblock('page-title') ?>
<?= $page_title ?>
<?php endblock() ?>
<?php startblock('content') ?>
<script type="text/javascript" language="javascript">
	 $(document).ready(function() {
		 $(".accountid").change(function(){

		 	
					var account_id=this.value;
					var type="add";
					
					if(this.value!=""){
						$.ajax({
							type:'POST',
							url: "<?= base_url()?>/conference/conference_get_greetings/",
							data:{account_id:account_id,type:type}, 
							success: function(response) {
								
								 $("#greetingid_id_search_drp").html(response);
								 $('.greetingid_id_search_drp').selectpicker('refresh');
							}
						});
						}else{
								$(".greetingid_id_search_drp").html("");
						}	
			});
			$(".accountid").change();
     });   
</script>

<style>
.btnplay {
    background: none !important;
}
.btnplay:hover {
    background: none !important;
}
</style>

<section class="p-0">
<div class="slice color-three">
<div class="w-section inverse p-0">	
	<form method="post" name='voicemail_form' id ="voicemail_id" enctype="multipart/form-data" action="<?= base_url()?>user/user_fssipdevices_voice_mail_save/">
		<div class="col-md-12 p-0">
		<ul class="card p-0">
		<?php //Nirali issue 3898 Start ?> 
			<div class="p-3 mb-3" id="floating-label">
			<?php  //Nirali issue 3898 END ?>				
			<input type="hidden" name="edit_id"  class="" value="<?php echo $edit_id; ?>">    
			<input type="hidden" name="redirect_url" class="col-md-5 form-control" value="<?= $_SERVER['HTTP_REFERER']; ?>"> 
			<div class="col-md-12">
			<div class="row">
				<div class="col-md-5 form-group">
					<label class="p-0 control-label"><?php echo gettext("Unavailable Greeting"); ?> </label>
					<select name="unavailable_greeting" id="id" class="col-md-12 form-control form-control-lg selectpicker greetingid_id_search_drp selectpicker" data-live-search="true" tabindex="-98">
						<option value="select">--Select--</option>
						<?php
						foreach($edit_data as $key => $value){
							$selected = '';
							if($unavailable_greeting == $value['id'])
							{
								$selected="selected";
							}
							 ?>
							 <?php //ASTPPENT-3769 Jaimin Start ?>
							<option value="<?php echo $value['id']; ?>" <?php echo $selected;?>><?php echo $value['name'];?></option> 
							 <?php //ASTPPENT-3769 Jaimin End ?>

						<?php } ?>
					</select>	
				</div>
				<div class="mt-5 pl-4 col-md-1">
					<span><?php  echo $unavailable_greeting_play ?> </span>
				</div>
				<div class="col-md-5 form-group">
					<label class="p-0 control-label"><?php echo gettext("Name Greeting");?></label>
					<select name="name_greeting" id="id" class="col-md-12 form-control form-control-lg selectpicker greetingid_id_search_drp selectpicker" data-live-search="true" tabindex="-98">
						<option value="select">--Select--</option>
						<?php 
						foreach($edit_data as $key => $value1){
							$selected = '';
							if($name_greeting == $value1['id'])
							{
								$selected="selected";
							}
							 ?>
							 <?php //ASTPPENT-3769 Jaimin Start ?>
							<option value="<?php echo $value1['id'] ?>" <?php echo $selected;?>><?php echo $value1['name']?></option>
							 <?php //ASTPPENT-3769 Jaimin End ?>
						<?php } ?>
					</select>	
				</div>
				<div class="mt-5 pl-4 col-md-1"> 
					<span  ><?php  echo $name_greeting_play ?> </span>
				</div>
          	 	<div class="col-md-5 form-group">
					<label class="p-0 control-label"><?php echo gettext("Busy Greeting"); ?></label> 
					<select name="busy_greeting" id="id" class="col-md-12 form-control form-control-lg selectpicker greetingid_id_search_drp selectpicker" data-live-search="true" tabindex="-98">
						<option value="select">--Select--</option>
						<?php 
						foreach($edit_data as $key => $value2){
							$selected = '';
							if($busy_greeting == $value2['id'])
							{
								$selected="selected";
							} ?>
							 <?php //ASTPPENT-3769 Jaimin Start ?>
							<option value="<?php echo $value2['id']?>" <?php echo $selected;?> ><?php echo $value2['name']?></option>
							<?php //ASTPPENT-3769 Jaimin End ?>
						<?php }?>
					</select>	
				</div>
				<div class="mt-5 pl-4 col-md-1">
				 	<span  ><?php  echo $busy_greeting_play ?> </span>
				</div>
              	<div class="col-md-5 form-group">
					<label class="p-0 control-label"><?php echo gettext("Temporary Greeting"); ?></label>
					<select name="temporary_greeting" id="id" class="col-md-12 form-control form-control-lg selectpicker greetingid_id_search_drp selectpicker" data-live-search="true" tabindex="-98">
						<option value="select">--Select--</option>
						<?php 
						foreach($edit_data as $key => $value3){ 
							$selected = '';
							if($temporary_greeting == $value3['id'])
							{
								$selected="selected";
							} ?>
							 <?php //ASTPPENT-3769 Jaimin End ?>
							<option value="<?php echo $value3['id']?>" <?php echo $selected;?> ><?php echo $value3['name']?></option>
							<?php //ASTPPENT-3769 Jaimin End ?>
        				<?php }?>
                    </select>	
				</div>
				<div class="mt-5 pl-4 col-md-1"> 
					<span  ><?php  echo $temporary_greeting_play ?> </span>
				</div>			
			</div>	
		</div>
	</div>
	</ul>
</div>
			<div class="col-12 my-4 text-center">
			<?php //Nirali issue 3898 Start ?>
			<a href="javascript:history.go(-1)" class="btn text-primary ml-2" onclick="return redirect_page('/freeswitch/fssipdevices/')"><?php echo gettext("Cancel"); ?></a>	
			<button name="action" type="submit" value="save" id="submit" class="btn btn-primary" onclick="return validateForm();"><?php echo gettext("Save"); ?></button>
			<?php //Nirali issue 3898 END ?>
			</div>
  	   </form><?php  // play or push button  ?>
  	   <?php 
			if (isset($validation_errors) && $validation_errors != '') {
				?>
				<script>
					var ERR_STR = '<?php echo $validation_errors; ?>';
					print_error(ERR_STR);
				</script>
			<? } ?>
    	</div>    
    </div> 

<script>
var lastClicked = '';
function playAudio(val) { 
	if(lastClicked != '' && lastClicked != val){
		var y = document.getElementById("myAudio_"+lastClicked);
		$("#play_"+lastClicked).css("display","block");
		$("#pause_"+lastClicked).css("display","none"); 
		y.pause();
		y.currentTime = 0;
	}
	var x = document.getElementById("myAudio_"+val);
	$("#play_"+val).css("display","none");
	$("#pause_"+val).css("display","block"); 
	x.play();
	lastClicked= val; 
} 
function pauseAudio(val) { 
	var x = document.getElementById("myAudio_"+val); 
	$("#play_"+val).css("display","block");
	$("#pause_"+val).css("display","none");
	x.pause(); 
} 
</script>
</section>
<? endblock() ?>
<? end_extend() ?>


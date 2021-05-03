<? extend('master.php') ?>
<? startblock('extra_head') ?>
<?php
session_start();
include "view_freeswitch_request.php";
include "config.php";
$url_array=explode("/",$_SERVER['REQUEST_URI']);
$url='';
$folder_name=$url_array[1];
for($i=1;$i<count($url_array)-1;$i++){
	$url.="/".$url_array[$i];
}
?>
<script type="text/javascript">
$(document).ready(function(){ 
  var ip = location.host;
  $.ajax({
    type:'POST',
    url: "<?php echo base_url();?>fsmonitor/sip_devices_file_exits/",
    cache    : false,                 
    async: false, 
    success: function(data) {
	if(!data){
	      window.location.href = "<?php echo base_url();?>fsmonitor/fs_cli/";
	}
    }
   });
});
</script>

<? endblock() ?>
<? startblock('page-title') ?>
<?=$page_title?>
<? endblock() ?>
<? startblock('content') ?>
<?php 	$command_show = str_replace("api ","",$command); ?>
<script>
$(document).ready(function(){
  $("#freeswitch_command").change(function(){
    $('#error_field_command').text('');
    return false;
  });
});
</script>
<style>
predemo{

	display:block;
	padding:9.5px;
	margin:0 0 10px;
	font-size:13px;
	line-height:1.428571429;
	color:#FFFFFF;
	word-break:break-all;
	word-wrap:break-word;
	background-color:#000000;
	border:1px solid #ccc;
	margin-top:1px;
	margin-bottom:1px;
	border-radius:4px
}

</style>
<script type="text/javascript">
function validateForm(){
      if(document.getElementById('freeswitch_command').value == "")
      {
	  $('#error_field_command').html( "<i class='fa fa-exclamation-triangle error_triangle'></i><span class='popup_error error  p-0'>Please Enter Switch command</span>" );
	  document.getElementById('freeswitch_command').focus();
    document.getElementById("error_field_command").style.display = "block"; 
	  return false;
      }
      $('#form').submit();
}
</script>
<section class="slice color-three">
   <div class="w-section inverse p-0">
	   <?php  $permissioninfo = $this->session->userdata('permissioninfo'); 
			
	   ?>
       <div class="row">
        <div class="col-md-12 color-three"><!-- Purchase DIDs -->
        <div class="card col-md-12 px-0 pb-4" id="floating-label">
        <h3 class="bg-secondary text-light p-2 rounded-top">Freeswitch CLI</h3>
         <form method="POST" id="form" name="form" action="<?php echo base_url(); ?>fsmonitor/fs_cli_command/" enctype="multipart/form-data">  
          <div class="col-md-12">
          <div class="row">
            <div class="col-md-5 form-group">
             <label class="col-md-12 control-label p-0">Switch Host:</label>
             
               <select class="col-md-12 form-control form-control-lg selectpicker"  name="host_id" id="host_id">
                <?php
                foreach($fs_data as $name) { ?>
                <option value="<?= $name['id'] ?>"<?php if(isset($host_id) && ($name['id'] == $host_id))echo 'selected';?>><?= $name['freeswitch_host'] ?></option>
                <?php
              } ?>
            </select>
             </div>
                  <div class="col-md-5 form-group h-auto">
                   <label class="col-md-12 p-0 control-label">Command </label>
                       <input type="text" class="col-md-12 form-control form-control-lg" value="<?php echo $command_show; ?>" id="freeswitch_command" name="freeswitch_command" placeholder="command" >
                       <div class="text-danger tooltips error_div float-left p-0" id="error_field_command"></div>
                 </div>
                  <div class="align-self-center col-md-2 text-center">
                      <button name="action" type="submit" value="save" class="btn btn-success" >Submit</button>
                 </div>
          </div>

<?php if((isset($permissioninfo['fsmonitor']['fs_cli']['execute']) && $permissioninfo['fsmonitor']['fs_cli']['execute'] == 0  && ($permissioninfo['login_type'] == '-1'))){?>          
<div class="col-md-12">
  <button type="button" onclick="validateForm();" class="btn btn-success float-right">Execute</button>   
</div>
<?php } ?>
   </div>
    <div class="col-md-12 p-0 mt-4">
      <?php if($command != ''){ ?>
      <div class="col-md-12"><h4 class="alert-dark p-2">Command <font color="blue"><?php echo $command_show; ?></font></h4>

      

        <div class="predemo col-md-12 p-0">
          <h6 class="predemo"><?php echo "<pre><predemo>".$response."\n"; ?></h6>
        </div>
</div>
        <?php } ?>
      
    </div>
              </form>
            </div>  
         </div>  
                </div>
      </div>
    </div>
</section>




<? endblock() ?>
<? end_extend() ?>  

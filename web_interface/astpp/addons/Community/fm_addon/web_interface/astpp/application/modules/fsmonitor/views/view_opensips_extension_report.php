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
	      window.location.href = "<?php echo base_url();?>fsmonitor/sip_devices/";
	}
    }
   });
});
</script>
<?php 
	$filename = $licence_file;
	if(file_exists($filename))
	{ 
	 $localkey = file_get_contents($filename);
	}
	else{
	 $localkey = '';
	}
	$results = yourprefix123_check_license($licensekey="", $localkey);
	$status = $results['status'];
	$status = 'Active';
	if($status != "Active" ){
  ?>
<script type="text/javascript">
 	      window.location.href = "<?php echo base_url();?>fsmonitor/sip_devices_authentication/";    
</script>
<?php
exit;}
?>
<?php
	if(common_model::$global_config['system_config']['opensips'] == 1) {
		redirect(base_url()."fsmonitor/sip_devices/");
	}
        $this->db->select("value"); 
        $this->db->where("name",'refresh_second'); 
        $system = $this->db->get("system");
        $system_res=$system->result_array();
        if (!empty($system_res)) {
			$result=$system_res[0];
		?>
		<meta http-equiv="refresh" content="<?php echo $result['value']; ?>" > 
        <?php
        }
?>
<?php
if(empty($_POST)){
$_POST['host_id']=0;
} 
if(isset($_POST['second_reload']) && $_POST['second_reload'] != ''){
	$qry=mysql_query("UPDATE system SET value = '".$_POST['second_reload']."' WHERE name ='refresh_second'")or die(mysql_error());

}
?>
<script type="text/javascript">
$(document).ready(function(){
 var id="<?php echo $_POST['host_id']?>";
  $("#fs_extension").flexigrid({
    url: "<?php echo base_url();?>fsmonitor/opensips_devices_json/"+id,
    method: 'GET',
    dataType: 'json',
	colModel : [
		{display: 'User Name', name: 'username', width: 200, sortable: false, align: 'center'},
		{display: 'Domain', name: 'domain', width: 190, sortable: false, align: 'center'},
		{display: 'Contact', name: 'contact', width: 400, sortable: false, align: 'center'},
                {display: 'Socket', name: 'socket', width: 220, sortable: false, align: 'center'},
		{display: 'Expires', name: 'expires', width: 195, sortable: false, align: 'center'},
		],

	nowrap: false,
	showToggleBtn: false,
	sortname: "call-id",
	sortorder: "asc",
	usepager: true,
	resizable: true,
	useRp: true,
	rp: 50,
	showTableToggleBtn: false,
	width: "auto",
	height: "auto",
	pagetext: 'Page',
	outof: 'of',
	page:'1',
	nomsg: 'No items',
	procmsg: 'Processing, please wait ...',
	pagestat: 'Displaying {from} to {to} of {total} items',
	onSuccess: function(data){
	  $('a[rel*=facebox]').facebox({
		    loadingImage : '<?php echo base_url();?>/images/loading.gif',
		    closeImage   : '<?php echo base_url();?>/images/closelabel.png'
	    });
	},
	onError: function(){
	    alert('Sorry, we are unable to connect to freeswitch!!!');
	}
});
  $("#host_id").change(function(){
	var id = document.getElementById("host_id").value;

  });
});
</script>
<script>
function myFunction() {
		document.getElementById("extension").submit();
    
}
</script>
<? endblock() ?>
<? startblock('page-title') ?>
<?=$page_title?>
<? endblock() ?>
<? startblock('content') ?>

<section class="slice color-three">
	<div class="w-section inverse p-0">
		<div id="floating-label" class="card col-md-12 px-0 pb-4">
			<h3 class="bg-secondary text-light p-2 rounded-top">Opensips Devices</h3>
	 	<form method="POST" action="" enctype="multipart/form-data" id="ListForm1" name="extension">


			<div class="col-md-6 form-group">
				<label class="col-md-12 p-0 control-label">Refresh Time:</label>
					<select class="col-md-12 form-control form-control-lg selectpicker"  name="host_id" id="host_id" onchange="this.form.submit()" >

  	    <?php
		if($_POST['second_reload'] == ''){
	      ?>
		<option value="15" <?php if(isset($result['value']) && (15 == $result['value']))echo 'selected';?>>15 Second</option>
		<option value="30" <?php if(isset($result['value']) && (30 == $result['value']))echo 'selected';?>>30 Second</option>
		<option value="60" <?php if(isset($result['value']) && (60 == $result['value']))echo 'selected';?>>1 minute</option>
		<option value="120" <?php if(isset($result['value']) && (120 == $result['value']))echo 'selected';?>>2 Minute</option>
		<option value="180" <?php if(isset($result['value']) && (180 == $result['value']))echo 'selected';?>>3 Minute</option>
     		
	     <?php
		}
		else{
	     ?>
		<option value="15" <?php if(isset($_POST['second_reload']) && (15 == $_POST['second_reload']))echo 'selected';?>>15 Second</option>
		<option value="30" <?php if(isset($_POST['second_reload']) && (30 == $_POST['second_reload']))echo 'selected';?>>30 Second</option>
		<option value="60" <?php if(isset($_POST['second_reload']) && (60 == $_POST['second_reload']))echo 'selected';?>>1 Minute</option>
		<option value="120" <?php if(isset($_POST['second_reload']) && (120 == $_POST['second_reload']))echo 'selected';?>>2 Minute</option>
		<option value="180" <?php if(isset($_POST['second_reload']) && (180 == $_POST['second_reload']))echo 'selected';?>>3 Minute</option>

  	      <?php
		}
	?>
					</select>
			</div>
    	</form>
    <br/>
</div>
<br/>
</div>
</section>
<section class="slice color-three pb-4">
	<div class="w-section inverse p-0">
		<div class="card col-md-12 py-4">     
			<form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">    
				<table id="fs_extension" align="left" style="display:none;"></table>
			</form>
		</div>  
	</div>
</section>
 <? endblock() ?>
<? end_extend() ?>
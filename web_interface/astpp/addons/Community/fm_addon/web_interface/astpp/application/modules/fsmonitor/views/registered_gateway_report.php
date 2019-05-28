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
<style>

.label-sm {
    padding: 0.2em 0.9em 0.3em;
    font-size: 11px;
    line-height: 1;
    height: 20px;
}
.label-inverse, .label.label-inverse, .badge.badge-inverse, .badge-inverse {
    background-color: #87B87F;
}
.label-inverse_red, .label_red.label-inverse_red, .badge_red.badge-inverse_red, .badge-inverse_red {
    background-color: red;
}
/*.label {
    border-radius: 0px;
    text-shadow: none;
    font-weight: 400;
    color: #FFF;
    display: inline-block;
    background-color: #ABBAC3;
}*/
.label-inverse_red.arrowed:before_red{
	border-right-color:#333;
	-moz-border-right-colors:#333
}

.label-inverse_red.arrowed-in:before_red{
	border-color:#333 #333 #333 transparent;
	-moz-border-right-colors:#333
}

.label-inverse_red.arrowed-right_red:after{
	border-left-color:#333;
	-moz-border-left-colors:#333
}

.label-inverse_red.arrowed-in-right_red:after{
	border-color:#333 transparent #333 #333;
	-moz-border-left-colors:#333
}

.label-sm_red.arrowed_red{
	margin-left:4px
}

.label-sm_red.arrowed_red:before{
	left:-8px;
	border-width:9px 4px
}

.label-sm_red.arrowed-in_red{
	margin-left:4px
}

.label-sm_red.arrowed-in_red:before{
	left:-4px;
	border-width:10px 4px
}

.label-sm_red.arrowed-right_red{
	margin-right:4px
}

.label-sm_red.arrowed-right_red:after{
	right:-8px;
	border-width:9px 4px
}

.label-sm_red.arrowed-in-right_red{
	margin-right:4px
}

.label-sm_red.arrowed-in-right_red:after{
	right:-4px;
	border-width:9px 4px
}
.label_red.arrowed_red,.label_red.arrowed-in_red{
	position:relative;
	z-index:1
}

.label_red.arrowed_red:before,.label_red.arrowed-in_red:before{
	display:inline-block;
	content:"";
	position:absolute;
	top:0;
	z-index:-1;
	border-color: transparent;
	border-style: solid;
	/*border-width: 0px;*/
	/*border:0px solid transparent;*/
	border-right-color:#D15B47;
	-moz-border-right-colors:#D15B47
}

.label_red.arrowed-in_red:before{
	border-color:#D15B47;
	border-left-color:transparent;
	-moz-border-left-colors:none
}
.label-sm_red {
    padding: 0.2em 0.9em 0.3em;
    font-size: 11px;
    line-height: 1;
    height: 20px;
}
.label-inverse_red, .label_red.label-inverse_red, .badge_red.badge-inverse_red, .badge-inverse_red {
    background-color: #D15B47;
}

.label_red.arrowed_red, .label_red.arrowed-in_red {
    position: relative;
    z-index: 1;
}
.label-sm_red.arrowed-in_red {
    margin-left: 4px;
}
.label_red {
    border-radius: 0px;
    text-shadow: none;
    font-weight: 400;
    color: #FFF;
    display: inline-block;
    background-color: #ABBAC3;
}

</style>
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
	      window.location.href = "<?php echo base_url();?>fsmonitor/gateways/";
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
?>

<?php
    $this->db->select("value"); 
    $this->db->where("name",'refresh_second'); 
    $system = $this->db->get("system");
    $system_res=$system->result_array();
	
if (!empty($system_res[0])){
	$result=$system_res[0];
}
?>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/module_js/generate_grid.js"></script>
<?php
	if(isset($result['value']) && !empty($result['value'])){
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
	$update_array = array("value"=>$_POST['second_reload']);
	$this->db->where("name","refresh_second");
	$this->db->update("system",$update_array);
//	$qry=mysql_query("UPDATE system SET value = '".$_POST['second_reload']."' WHERE name ='refresh_second'")or die(mysql_error());

}

?>
<script type="text/javascript">
$(document).ready(function(){
//  var id = document.getElementById("host_id").value;
 var id="<?php echo $_POST['host_id']?>";

$("#gateway_grid").flexigrid({
    url: "<?php echo base_url();?>fsmonitor/gateways_json/"+id,
    method: 'GET',
    dataType: 'json',
	colModel : [
		{display: 'Gateway Name', name: 'name', width: 120, sortable: false, align: 'center'},
                {display: 'Proxy', name: 'proxy', width: 150, sortable: false, align: 'center'},
                {display: 'User Name', name: 'username', width: 120, sortable: false, align: 'center'},
                {display: 'Call In', name: 'call-in', width: 120, sortable: false, align: 'center'},
                {display: 'Call Out', name: 'call-out', width: 120, sortable: false, align: 'center'},
                {display: 'Fail Call In', name: 'fail-call-in', width: 120, sortable: false, align: 'center'},
                {display: 'Fail Call Out', name: 'fail-call-out', width: 120, sortable: false, align: 'center'},
		{display: 'Status', name: 'status', width:80, sortable: false, align: 'center'},
		{display: 'State', name: 'state', width: 150, sortable: false, align: 'center'},
                {display: 'Action', name: 'action', width: 150, sortable: false, align: 'center'},
		],
	/*buttons : [
		{name: ' ', bclass: 'reload', onpress : reload_button}	
		],*/
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
	nomsg: 'No items',
	procmsg: 'Processing, please wait ...',
	pagestat: 'Displaying {from} to {to} of {total} items',
	onSuccess: function(data){
	$('a[rel*=facebox]').facebox({
		    loadingImage : '<?php echo base_url();?>/assets/images/loading.gif',
		    closeImage   : '<?php echo base_url();?>/assets/images/closelabel.png'
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
    //location.reload();
		document.getElementById("extension").submit();
}
</script>
<? endblock() ?>
<? startblock('page-title') ?>
<?=$page_title?>
<? endblock() ?>
<? startblock('content') ?>
<section class="slice color-three mb-4">
	<div class="w-section inverse p-0">
		<div class="col-md-12">      
			<div class="row">
				<div id="floating-label" class="card col-md-12 px-0 pb-4">
					<h3 class="bg-secondary text-light p-2 rounded-top">Gateways</h3>
					<form method="POST" action="" enctype="multipart/form-data" id="ListForm1" name="extension">    
						<div class="col-md-6 form-group">
							<label class="col-md-12 control-label p-0">Switch Host </label>
							<select class="col-md-12 form-control form-control-lg selectpicker"  name="host_id" id="host_id" onchange="this.form.submit()">
								<option value="0">--All--</option>
								<?php
								foreach($fs_data as $name) { ?>
								<option value="<?= $name['id'] ?>"<?php if(isset($_POST['host_id']) && ($name['id'] == $_POST['host_id']))echo 'selected';?>><?= $name['freeswitch_host'] ?></option>
								<?php
								} ?>
							</select>
						</div>

						<div class="col-md-5 float-right text-right">
							<label class="search_label col-md-5 text-right">Refresh Time:</label>
							<select class="col-md-6 form-control form-control-lg selectpicker"  name="second_reload" id="second_reload" onchange="this.form.submit()" >
								<?php
	    		//for($i=5;$i<=300;$i+=5) {
								if($_POST['second_reload'] == ''){
									?>
									<option value="15" <?php if(isset($result['value']) && (15 == $result['value']))echo 'selected';?>>15 Second</option>
									<option value="30" <?php if(isset($result['value']) && (30 == $result['value']))echo 'selected';?>>30 Second</option>
									<option value="60" <?php if(isset($result['value']) && (60 == $result['value']))echo 'selected';?>>1 minute</option>
									<option value="120" <?php if(isset($result['value']) && (120 == $result['value']))echo 'selected';?>>2 Minute</option>
									<option value="180" <?php if(isset($result['value']) && (180 == $result['value']))echo 'selected';?>>3 Minute</option>
									<!-- <option value="<?php echo $i; ?>"<?php if(isset($result['value']) && ($i == $result['value']))echo 'selected';?>><?php echo $i; ?> Second</option> -->
									<?php
								}
								else{
									?>
									<option value="15" <?php if(isset($_POST['second_reload']) && (15 == $_POST['second_reload']))echo 'selected';?>>15 Second</option>
									<option value="30" <?php if(isset($_POST['second_reload']) && (30 == $_POST['second_reload']))echo 'selected';?>>30 Second</option>
									<option value="60" <?php if(isset($_POST['second_reload']) && (60 == $_POST['second_reload']))echo 'selected';?>>1 Minute</option>
									<option value="120" <?php if(isset($_POST['second_reload']) && (120 == $_POST['second_reload']))echo 'selected';?>>2 Minute</option>
									<option value="180" <?php if(isset($_POST['second_reload']) && (180 == $_POST['second_reload']))echo 'selected';?>>3 Minute</option>

									<!-- <option value="<?php echo $i; ?>"<?php if(isset($_POST['second_reload']) && ($i == $_POST['second_reload']))echo 'selected';?>><?php echo $i; ?> Second</option> -->

									<?php
								}
				    	      // } ?>
				    	  </select>
				    	</div>
				    </form>
				</div>
			</div>  
		</div>
	</div>
</section>

<section class="slice color-three">
	<div class="w-section inverse p-0">
		<div class="card col-md-12 py-4">     
			<form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">    
				<table id="gateway_grid" class="flex_grid" align="left" style="display:none;"></table>
			</form>
		</div>  
	</div>
</section>

<? endblock() ?>
<? end_extend() ?>  

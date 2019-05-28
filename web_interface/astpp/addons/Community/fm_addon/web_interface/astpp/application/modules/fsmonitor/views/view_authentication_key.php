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
/*$(document).ready(function(){ 
  $.ajax({
    type:'POST',
    url: "<?php echo base_url();?>fsmonitor/sip_devices_file_exits/",
    cache    : false,                 
    async: false, 
    success: function(data) {
//alert(data);
  if(data == 1){

	if('<?= $type ?>' == 'Sip'){
	      window.location.href = "<?php echo base_url();?>fsmonitor/sip_devices/";    
	}
	else if('<?= $type ?>' == 'gateways'){
	      window.location.href = "<?php echo base_url();?>fsmonitor/gateways/";    
	}
	else if('<?= $type ?>' == 'fs_cli'){
	      window.location.href = "<?php echo base_url();?>fsmonitor/fs_cli/";    
	}
	else{
	      window.location.href = "<?php echo base_url();?>fsmonitor/live_call_graph/";    
	}
  }
    }
   });
});*/
</script>
<title>Validate License</title>
  <meta charset="UTF-8">
<style>

.sidebar ul li{display: none;}

.button{
  width:100px;
  background:#3399cc;
  display:block;
  margin:0 auto;
  margin-top:1%;
  padding:5px;
  text-align:center;
  text-decoration:none;
  color:#fff;
  cursor:pointer;
  transition:background .3s;
  -webkit-transition:background .3s;
}

.button:hover{
  background:#2288bb;
}

#login
{

  /*width:25%;
  margin:0 auto;
  margin-top:7%;
  margin-bottom:2%;*/
  height:100px;
  transition:opacity 1s;
  -webkit-transition:opacity 1s;
	box-shadow : 1px 1px 7px #999999;
	padding-bottom:15px;
	padding-top:20px;
	margin-top:14%;
	/*margin-top:15%;*/
}

#login h1{
  background:#375C7C;
  height:40px;
  padding:5px 0;
  font-size:140%;
  font-weight:300;
  text-align:center;
  color:#fff;
	font-weight:bold;
	margin-bottom:8px;
	margin-top:20px;
	padding-top:10px;

}
form{
  background:#f0f0f0;
 /* padding:1% 3%;*/
}

input[type="text"]{
  width:92%;
  background:#fff;
  margin-bottom:4%;
  border:2px solid #ccc;
  padding:1%;
  font-family:'Open Sans',sans-serif;
  font-size:95%;
  color:#555;
}


input[type="submit"]{
  width:60%;
  height:40px;
  border:0;
  margin-top:0px;
  padding:1%;
  font-family:'Open Sans',sans-serif;
  font-size:100%;
  color:#fff;
  cursor:pointer;
  transition:background .3s;
  -webkit-transition:background .3s;
}
/*
input[type="submit"]:hover{
}*/

</style>

<script type="text/javascript">


function dateCheck() 
{
var key =  document.getElementById("Lkey").value;  
  if(key == "")
  {
    document.getElementById("login1").style.display= "block";

    return false;
  }
  
}
 function TestOnTextChange()
 {
document.getElementById("login1").style.display= "none";
 }

</script>
<? endblock() ?>
<? startblock('page-title') ?>
<?=$page_title?><br/>
<? endblock() ?>
<? startblock('content') ?>
<section class="col-md-12 slice color-three padding-b-20" style="height:350px;"> 


      <div class="col-md-12 margin-l-20"  style="padding-left:400px !important;">
<div id="login" align="center" class="col-md-6" style="margin-bottom:10px;margin-top:0px;height:300px;">
<!--<img src="<?php echo base_url();?>/assets/images/lock.jpeg" style="height:70px;"> -->

  <h1>Authentication</h1>
        <div style="margin-top:30px;">
  <form  method="POST"  action = "<?php echo base_url();?>fsmonitor/live_call_key/"  align="center" name="myform">
    <p style="float:left;padding:2px;margin-left:34%;margin-bottom:2%;"> <font color="black">Enter Your Key</font></p>
    <input type="hidden" name="log_type" value="<?php echo $type;?>">
    <input type="text" placeholder="License-key" name="akey" id="Lkey" onchange="TestOnTextChange()" />

<div id="login1"  style="display:none;">
    <p style="float:left;margin-left:5%; color:red;"> Please Enter License Key</p>    
</div>

    <input type="submit" value="Validate License" name="btnsubmit" class="btn btn-line-parrot" align="center" id="btnsubmit" onclick=" return dateCheck()"/>
  </form>
</div>
</div>
</div>


  <script src='http://codepen.io/assets/libs/fullpage/jquery.js'></script>

  <script src="js/index.js"></script>
</section>

 <? endblock() ?>
	        
<? end_extend() ?>  


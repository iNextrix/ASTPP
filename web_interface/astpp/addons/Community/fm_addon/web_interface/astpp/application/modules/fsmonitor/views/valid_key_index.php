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
alert("http://"+ip+"<?php echo $url;?>/file_exist.php");


  $.ajax({
    type:'POST',
    url: "http://"+ip+"<?php echo $url;?>/file_exist.php",
    cache    : false,                 
    async: false, 
    success: function(data) {
  if(data){
      window.location.href = "http://"+ip+"/<?php echo $folder_name;?>/#/app/dashboard";    
  }

    }
   });
});
</script>


<!DOCTYPE html>
<html>

<head>
<title>Validate License</title>
  <meta charset="UTF-8">

  
    <link rel="stylesheet" href="../css/login.css" media="screen" type="text/css" />


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
  transition:opacity 1s;
  -webkit-transition:opacity 1s;
	box-shadow : 1px 1px 7px #999999;
	padding-bottom:15px;
	padding-top:20px;
margin-top:13%;
	/*margin-top:15%;*/
}

#login h1{
  background:#3399cc;
  padding:5px 0;
  font-size:140%;
  font-weight:300;
  text-align:center;
  color:#fff;
	font-weight:bold;
	margin-bottom:8px;
	margin-top:0px;
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
  width:100%;
  background:#3399cc;
  border:0;
  padding:1%;
  font-family:'Open Sans',sans-serif;
  font-size:100%;
  color:#fff;
  cursor:pointer;
  transition:background .3s;
  -webkit-transition:background .3s;
}

input[type="submit"]:hover{
  background:#2288bb;
}

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
</head>

<body>

<div id="login" align="center" class="col-md-3 col-xs-offset-5">
 
  <h1>Authentication</h1>
  <form  method="POST"  action = "app/views/check.php"  align="center" name="myform">
    <p style="float:left;padding:2px;margin-left:4%;margin-bottom:2%;"> Enter Your Key</p>
    <input type="text" placeholder="License-key" name="akey" id="Lkey" onchange="TestOnTextChange()" />

<div id="login1"  style="display:none;">
    <p style="float:left;margin-left:5%; color:red;"> Please Enter License Key</p>    
</div>

    <input type="submit" value="Validate License" name="btnsubmit" align="center" id="btnsubmit" onclick=" return dateCheck()"/>
  </form>
</div>



  <script src='http://codepen.io/assets/libs/fullpage/jquery.js'></script>

  <script src="js/index.js"></script>

</body>

</html>

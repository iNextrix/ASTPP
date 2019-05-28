<?php
	error_reporting(0);
?>
<? extend('master.php') ?>
<? startblock('extra_head') ?>
<html>

<head>

  <meta charset="UTF-8">

  <title>Authentication</title>

    <link rel="stylesheet" href="../css/login.css" media="screen" type="text/css" />
<style>
body
{
background: none repeat scroll 0 0 #ffffff;
}
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
  transition:opacity 1s;
  -webkit-transition:opacity 1s;
	box-shadow : 1px 1px 7px #999999;
	
	margin-top:17%;
}


#login h1{
  background:#375C7C;
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
 height:80px;
}


</style>


<script type="text/javascript">

setTimeout(openUrl, 2000);

function openUrl(){

   window.history.back();

}
</script>

<?php
session_start();
include "view_freeswitch_request.php";
include "config.php";
$url_array=explode("/",$_SERVER['REQUEST_URI']);
$folder_name=$url_array[1];

if($_POST['btnsubmit'])
{
	$log_type = $_POST['log_type']	;
	$licensekey = $_POST['akey'];
	$localkey = "";
 	$results = yourprefix123_check_license($licensekey, $localkey);
	if($results['status'] == 'Active'){
		$localkey = $results['localkey']; 
	}
	$status = $results['status'];

	//$filename = getcwd().'/application/modules/fsmonitor/views/'.$licence_file;
	$filename = $licence_file;
//echo $filename; exit;
	//$accessTime = time() - (7 * 24 * 60 * 60); 
	//touch($filename, $accessTime);
	$FileContent = $localkey;
echo $localkey; exit;
		if(file_exists($filename))
		{	
			chmod($filename, 0777); 
			$myfile = fopen($filename, "w") or die("Unable to open file!");
			$txt = $localkey;
			fwrite($myfile, $txt);
			fclose($myfile);
		}
		else 
		{
			if(@file_put_contents($filename,$FileContent))
			{
				chmod($filename, 0777);			
				$path = $_SERVER['DOCUMENT_ROOT'];
				$user_name = "root";			
				chown($path, $user_name);
			}
			else
			{		
				
			}
		}
	}

	if(file_exists($filename))
	{
		$filecontent = file_get_contents($filename);
		if($localkey == $filecontent && $status == "Active" )
		{
 ?>
		<script>
			if('<?= $log_type ?>' == 'Sip'){
			     window.location.href ="<?php echo base_url();?>fsmonitor/sip_devices/";
			}
			else if('<?= $log_type ?>' == 'gateways'){
			      window.location.href = "<?php echo base_url();?>fsmonitor/gateways/";
		    
			}
			else if('<?= $log_type ?>' == 'fs_cli'){
			      window.location.href = "<?php echo base_url();?>fsmonitor/fs_cli/";    
			}
			else{
			      window.location.href = "<?php echo base_url();?>fsmonitor/live_call_graph/";    
			}
		</script>
		<?php
		}else{
			?>
	<script>
	</script>
<? endblock() ?>
<? startblock('page-title') ?>
<?= $page_title ?><br/>
<? endblock() ?>
<? startblock('content') ?>
<section class="slice color-three padding-b-20">
          <div class="col-md-12 margin-l-20"  style="padding-left:400px !important;">
	<div id="login" align="center" class="col-md-6" style="margin-bottom:60px;margin-top:50px; height:150px;">
	<!--<img src="<?php echo base_url();?>/assets/images/keys.jpeg" style="height:80px;"> -->
	  <h1>Alert</h1>
	  <form method="POST" align="center">
	    <h2 style="float:left;padding:2px;margin-bottom:2%;margin-top:5%;width:100%;font-size:20pt;color:#000;"> Please Enter Valid Key</h2>
	    
	  </form>
	
	</div>
	</div>   
	</section>

	<?php
	
		}
	}
	else
	{ 
	?>
	<script>
	</script>
<? endblock() ?>
<? startblock('page-title') ?>
<?= $page_title ?><br/>
<? endblock() ?>
<? startblock('content') ?>
<section class="slice color-three padding-b-20">
          <div class="col-md-12 margin-l-20"  style="padding-left:400px;">
	<div id="login" align="center" class="col-md-6" style="margin-bottom:60px;margin-top:50px; height:150px;">
	<!--<img src="<?php echo base_url();?>/assets/images/keys.jpeg" style="height:80px;"> -->
	  <h1>Alert</h1>
	  <form method="POST" align="center">
	    <h2 style="float:left;padding:2px;margin-bottom:2%;margin-top:5%;width:100%;font-size:20pt;"> Please Enter Valid Key</h2>
	    
	  </form>
	
	</div>
	</div>   
	</section>

	<?php
	
	}

?>

 <? endblock() ?>
	        
<? end_extend() ?>  

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
	      window.location.href = "<?php echo base_url();?>fsmonitor/live_call_graph/";
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
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/chart/highcharts.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/chart/exporting.js"></script> 
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/module_js/generate_grid.js"></script> 
<?php
if(empty($_POST)){
	$_POST['host_id']=0;
} 
?>

    <script type="text/javascript">
$(document).ready(function(){
 var id="<?php echo $_POST['host_id']?>";
//  var id = document.getElementById("host_id").value;
	$(function () {
	    $(document).ready(function() {
		Highcharts.setOptions({
		    global: {
			useUTC: false
		    }
		});
		var chart;
		var url="<?php echo base_url();?>fsmonitor/livecall_data/"+id;
		$('#container').highcharts({
		    chart: {
			type: 'spline',
			animation: Highcharts.svg,
			marginRight: 10,
			events: {
			    load: function() {
				var series = this.series[0];
				setInterval(function() {
				     var x = (new Date()).getTime();
				     var y = 0;
				    var data = [];
				    $.ajax({
				    
					url:url,
					async: false,
					dataType: 'JSON',
					success: function(response_data) {
					$("#count").html("Call Count : "+response_data[0]);
					  x = (new Date()).getTime();
					  y = response_data[0];
					}
				    })
				    series.addPoint([x, y], true, true);
				},5000);
			    }
			}
		    },
		    title: {
			text: 'Live Call data'
		    },
		    xAxis: {
			type: 'datetime',
			tickPixelInterval: 150
		    },
		    yAxis: {
			allowDecimals: false,
			title: {
			    text: 'Value'
			},
			plotLines: [{
			    value: 0,
			    width: 1,
			    color: '#808080'
			}]
		    },
		    tooltip: {
			formatter: function() {
				return '<b>'+ this.series.name +'</b><br/>'+
				Highcharts.dateFormat('%Y-%m-%d %H:%M:%S', this.x) +'<br/>'+
				Highcharts.numberFormat(this.y, 2);
			}
		    },
		    legend: {
			enabled: false
		    },
		    exporting: {
			enabled: false
		    },
		    series: [{
			name: 'Total Calls',
			data: (function() {
			    var data = [];
			      $.ajax({
				  url: url,
				  async: false,
				  dataType: 'JSON',
				  success: function(response_data) {
				  $("#count").html("Call Count : "+response_data[0]);
 				    for (i = 0; i <= 10; i++) {
 				    time = (new Date()).getTime(),i;
 				      data.push({
 				      
 					x: time + i * 1000,
 					y: response_data[0]
 				    });
 				  }
				}
			      });
			    // generate an array of random data
			    return data;
			})()
		    }]
		});
	    });
	    
	});
       $("#host_id").change(function(){
       var id = document.getElementById("host_id").value;
       //reload_button('gateway_grid');
       //jQuery('#gateway_grid').flexReload();
    });
});
    </script>
<script>
function myFunction() {
    location.reload();
}
</script>

<? endblock() ?>

<? startblock('page-title') ?>
<?= $page_title ?>
<? endblock() ?>

<? startblock('content') ?>
<section class="slice color-three padding-b-20"> 
<!-- <div class="row"> -->
  <div id="floating-label" class="card">
	 <form method="POST" action="" enctype="multipart/form-data" id="ListForm1" name="extension">    
          
	  
	 	<div class="col-md-6 form-group">
	 		<label class="col-md-12 p-0 control-label" style="width:150px;">Switch Host : </label>
	 		<!-- <div style="width:500px; " > -->
	 		<select class="col-md-12 form-control form-control-lg selectpicker"  name="host_id" id="host_id" onchange="this.form.submit()">
	 			<option value="0">--Select All--</option>
	 			<?php
	 			foreach($fs_data as $name) { ?>
	 			<option value="<?= $name['id'] ?>"<?php if(isset($_POST['host_id']) && ($name['id'] == $_POST['host_id']))echo 'selected';?>><?= $name['freeswitch_host'] ?></option>
	 			<?php
	 		} ?>
		 	</select>
		 </div>
	  
	<!-- </div>  -->
	<!--
	<div class="col-md-5" style="float:right;">
	  <label class="col-md-1 margin-l-20" style="width:150px;">Refresh Second:</label>
	   <select class="col-md-5 form-control "  name="second_reload" id="second_reload" onchange="this.form.submit()" >
  	      <?php
    		for($i=5;$i<=300;$i+=5) { ?>
     		 <option value="<?php echo $i; ?>"<?php if(isset($result['value']) && ($i == $result['value']))echo 'selected';?>><?php echo $i; ?> Second</option>
  	      <?php
    	       } ?>
	    </select>
</div>
-->
   </form>
  </div>
 <!-- </div>   -->

	<!--<input id = 'count' type = 'text' name = 'count' readonly = 'readonly' />-->
<h1 id='count' class="h1 p-4 m-0"></h1>

<div id="container"></div>
</section>

<? endblock() ?>

<? startblock('sidebar') ?>

<? endblock() ?>
<? end_extend() ?> 



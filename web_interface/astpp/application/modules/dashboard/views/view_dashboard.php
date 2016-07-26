<? extend('master.php') ?>
<? startblock('page-title') ?>

<?= $page_title ?>

    <script type="text/javascript" src="<?php echo base_url();?>assets/js/chart/highcharts.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/chart/exporting.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/chart/highcharts-3d.js"></script>
<style>
.second{
 color: black;
 opacity:0.4;
 border: 0px;
 width: 100%; 
 padding:100px 0px 100px 0px;
 margin:5px;  
 display:relative;
 text-align:center;
 font-size:250%;
 /* -ms-transform: rotate(100deg); 
    -webkit-transform: rotate(100deg);
    transform: rotate(320deg);*/
}
</style>
<script type="text/javascript">
	
   function get_recharge_info(){
            $.ajax({
            type:'POST',
//     		dataType: 'JSON',
		url: "<?php echo base_url();?>"+'dashboard/customerReport_recent_payments/',
//		cache    : false,
//		async    : false,
                success: function(response_data) {
                     var custom_data=JSON.parse(response_data);
                      if(custom_data !=''){
                      $("div.recharge_not_data").hide();
		      $("div.recharge_data").show();
                          var str = "<table class='table table-bordered flexigrid'>";  
                          var arrayLength = custom_data.length;
                          for (var i = 0; i < arrayLength; i++) {
				  str=str+"<tr>";
 				  if(i==0){
 				    str=str+"<th style='text-align:center;'>"+custom_data[i].payment_date+"</th>";   				  
 				    str=str+"<th style='text-align:center;'>"+custom_data[i].accountid+"</th>";  
 				    str=str+"<th style='text-align:center;'>"+custom_data[i].credit+"</th>";  
 				  }else{
 				    str=str+"<td style='text-align:center;'>"+custom_data[i].payment_date+"</td>";   				  
				    str=str+"<td style='text-align:center;'>"+custom_data[i].accountid+"</td>";  
 				    str=str+"<td style='text-align:center;'>"+custom_data[i].credit+"</td>";  
 				  }
 				  str=str+"</tr>";
			  }   
                          str+="</table>";
                                document.getElementById("recharge_data").innerHTML = str;  
                        }
                        if(custom_data ==''){
                             $("div.recharge_data").hide();		    
		    $("div.recharge_not_data").addClass("second");
		    $("div.recharge_not_data").show();
		    $('div.recharge_not_data').text('No Records Found');
                        }
                    
                }
            });
      };
       function build_recharge_graph(){ 
// 	var d = new Date();
// 	var n = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
// 	    
// 	var month = n[d.getMonth()];
// 	  var year=d.getFullYear();
            $.ajax({
                type:'POST',
                url: "<?php echo base_url();?>dashboard/customerReport_call_statistics_with_profit/",
                dataType: 'JSON',
//                cache    : false,
//                async    : false,  
		beforeSend: function(){
                       $("#call-graph").append('<div class="loading col-md-offset-6"><img src="<?php echo base_url();?>/assets/images/loading.gif" /></div>');
                       $("#call-graph").show();
                   }, 
                success: function(response_data) {
//alert(response_data);
			$("#call-graph").hide();
                $('#call_graph_data').highcharts({
		  chart: {
		      zoomType: 'xy'
		  },
		  title: {
		      text: 'ABC'
		  },
		  xAxis: [{
		      categories:response_data['date']
		  }],
		  yAxis: [{
			  min: 0,
			  title: {
			      text: 'Total Calls'
			  }
		      }, {
			  min: 0,
			  opposite: true, //optional, you can have it on the same side.
			  title: {
			      text: 'Call Ratio with Profit'
			  }
		      }],
		  tooltip: {
				  backgroundColor: '#FEFEC5',
				  borderColor: 'black',
				  borderRadius: 10,
				  borderWidth: 2,
				  formatter: function() {
				      return this.series.name+': <b>'+this.y+'</b>';
				  }
		  },
		  legend: {
// 		  		      title: {
// 			text: "<p>"+month+","+year+"</p>",
// 			style: {
// 			  fontStyle: 'vedana',
// 			  color:'brown',
// 			}
// 		      },

		      layout: 'horizontal',
		      align: 'center',
		      x: 0,
		      verticalAlign: 'top',
		      y: 0,
		      backgroundColor: '#EFEFEF'
		  },
		  series: [
		      {
		      name: 'Total Calls',
		      type: 'column',
		      color:'#C0C0C0  ',
		      data: response_data['total']
		      
		  },
		      {
		      name: 'Answered Calls',
		      type: 'spline',
		      color:'blue',
		      yAxis:1,
		      data: response_data['answered'],
		      marker: {
			  enabled: true
		      },
		      dashStyle: 'shortdot'
		  },
		  {
		      name: 'Failed Calls',
		      type: 'spline',
		      color:'red',
		      yAxis:1,
		      data: response_data['failed'],
		      marker: {
			  enabled: true
		      },
		      dashStyle: 'shortdot'
		  },
		  {
		      name: 'Profit',
		      type: 'spline',
		      yAxis:1,
		      color:'green',
		      data: response_data['profit'],
		      marker: {
			  enabled: true
		      },
		      dashStyle: 'shortdot'
		  }   
		  ]
        });
              }
                });
            }
     function build_call_graph(url){ 
            $.ajax({
                type:'POST',
                url: "<?php echo base_url();?>dashboard/customerReport_maximum_call"+url+"/",
                dataType: 'JSON',
                beforeSend: function(){
                       $("#call-count").append('<div class="loading col-md-offset-6"><img src="<?php echo base_url();?>/assets/images/loading.gif" /></div>');
                       $("#call-count").show();
                   },
//                cache    : false,
//                async    : false,   
                success: function(response_data) {
			$("#call-count").hide();
		  if(response_data == ''){
		    $("div.call_count_data").hide();		    
		    $("div.not_data").addClass("second");
		    $("div.not_data").show();
		    $('div.not_data').text('No Records Found');
		  }
		  else{
		  $("div.not_data").hide();
		  $("div.call_count_data").show();
		    $('#call_count_data').highcharts({
		      chart: {
			type: 'pie',
			options3d: {
			    enabled: true,
			    alpha: 45,
			    beta: 0,
			    depth: 25,
			    viewDistance: 25
			}
		      },
		      title: {
			text: ""
		      },
		      tooltip: {
			backgroundColor: '#FEFEC5',
			borderColor: 'black',
			borderRadius: 10,
			borderWidth: 2,
			formatter: function() {
			    return this.point.name+': <b>'+this.y+'</b>';
			}
		      },
		      subtitle: {
			text: ''
		      },
		      plotOptions: {
			pie: {
			allowPointSelect: true,
			cursor: 'pointer',
			depth: 25,
			dataLabels: {
			    enabled: true,
			    format: '{point.name}'
			}
			}
		      },
		      series: [{
			name: '',
			data: response_data
		      }]
		  });
		      }
		      }
                });
            }
      $(document).ready(function() {
	  get_recharge_info();
	  build_recharge_graph();
	  build_call_graph('minutes');
	  $('input[name=calls_pie_chart]').change(function(){
	  radiobuttonvalue = $("input[name='calls_pie_chart']:checked").val();
	      build_call_graph(radiobuttonvalue);  
	  });
      });
    </script> 
<? endblock() ?>
<? startblock('content') ?>

<section class="slice">
	<div class="w-section inverse no-padding">
    	<div class="container">
   	    	<div class="row">
			 <div class="col-md-12 no-padding">        
                <div class="row">
                    <div class="col-lg-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title"><i class="fa fa-bar-chart-o fa-fw"></i> Monthly Stat <?=$date;?></h3>
                            </div>
                            <div class="panel-body">
            					<div id="call-graph"></div>
               					<div id='call_graph_data' class='call_graph_data'></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title"><i class="fa fa-bar-chart-o fa-fw"></i> Top 10 Accounts</h3>
                            </div>
                            <div class="panel-body">
		                         <div class="w-box col-md-6 padding-t-10 padding-b-10 pull-right"> 
		                           <input type="radio" name="calls_pie_chart" checked="checked" value="minutes" class="ace"><label class="lbl">By Minutes</label>
		                           &nbsp;&nbsp;
		                           <input type="radio" name="calls_pie_chart" value="count" class="ace"><label class="lbl"> By Calls</label></div>
					  <div id="call-count"></div>
		                           <div id='call_count_data' class=' call_count_data col-md-12' style ='display:none'></div>  
		                           <div id='not_data' class='col-md-12 not_data' style ='display:none'></div>
	               			</div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title"><i class="fa fa-bar-chart-o fa-fw"></i>Recharge Information</h3>
                            </div>
                            <div class="panel-body">
						          <div id='recharge_data' class='col-md-12 recharge_data' style ='display:none'></div>
						          <div id='recharge_not_data' class='col-md-12 recharge_not_data' style ='display:none'></div>
                            </div>
                        </div>
                    </div>
                </div>                
	       		 </div>	        
            </div>
        </div>
    </div>
</section>

<? endblock() ?>
<? startblock('sidebar') ?>
<? endblock() ?>
<? end_extend() ?> 
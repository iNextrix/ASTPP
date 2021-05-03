<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/chart/highcharts.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/chart/exporting.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/chart/highcharts-3d.js"></script>
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
    }
    .back_strip
    {
   }

<!--Name:pooja
Date: 01-07-2016 -->
 .ball-clip-rotate>div
 {
	 
display:inline-block;
	-webkit-animation:1s ease-in-out 0s normal none infinite running spin-rotate;
	animation:1s ease-in-out 0s normal none infinite running spin-rotate;
	background-color:#fff;
	width:42px;
	height:44px;
	border-radius:100%;
	margin:2px;
	-webkit-animation-fill-mode:both;
	animation-fill-mode:both;
	border:0px solid #fff;
	border-bottom-color:transparent;
	background:url('<?= base_url() ?>assets/images/loder-1.png')!important;
	
	
	}

@-webkit-keyframes spin-rotate
{
0%{-webkit-transform:rotate(0deg);transform:rotate(0deg)}

    to
    {
    -webkit-transform:rotate(1turn);transform:rotate(1turn)
    }
}
@keyframes spin-rotate
{
0%{-webkit-transform:rotate(0deg);transform:rotate(0deg)}
to{-webkit-transform:rotate(1turn);transform:rotate(1turn)}
}
</style>
<script type="text/javascript">
    
    function build_recharge_graph(drop_val){
    		var month_year=$("#month_year_dropdown").val();
    		var items = month_year.split('#');	
        var year=items[1];
        var month=items[0];
        $.ajax({
            type:'POST',
            dataType: 'json',
            async: false ,
            url: "<?php echo base_url(); ?>dashboard/customerReport_call_statistics_with_profit/",
            data:{"month":month, "year":year ,"drop_val":drop_val},
            beforeSend: function(){
                $("#call-graph").append('<div class="loading col-md-offset-6"><div class="ball-clip-rotate"><div></div></div></div>');
                $("#call-graph").show();
            },
            complete: function(){
                $("#call-graph").empty();
                $("#call-graph").hide();
            },
            success: function(response_data) {
                $("#call-graph").hide();
                $('#call_graph_data').highcharts({
                    chart: {
                        zoomType: 'xy'
                    },
                    title: {
                        text: 'ABC'
                    },
                    xAxis: [{
                            categories:response_data.date
                        }],
                    yAxis: [{
                            min: 0,
                            title: {
                                	text: ' '
                            }
                        }, {
                            min: 0,
                            opposite: true, 
                            title: {
                                text: '<?php echo gettext("Profit per day"); ?>'
                            }
                        }],
                    tooltip: {
                        backgroundColor: '#FEFEC5',
                        borderColor: 'black',
                        borderRadius: 10,
                        borderWidth: 2,
                        formatter: function() {
							var today_dropdown=$("#today_dropdown").val();
							if(this.series.name == 'Total Calls'){
								if(today_dropdown == 't_month'){
										return '<b>Total : </b>'+ this.y
										+'<br/><b>ACD : </b>'+ response_data.acd[this.x-1][1]
										+'<br/><b>MCD : </b>'+ response_data.mcd[this.x-1][1]
										+'<br/><b>ASR : </b>'+ response_data.asr[this.x-1][1];
								}else{
										var day_str = new Date(year+"-"+month+"-"+this.x);
										var day_count = day_str.getDay();
										return '<b>Total : </b>'+ this.y
										+'<br/><b>ACD : </b>'+ response_data.acd[day_count-1][1]
										+'<br/><b>MCD : </b>'+ response_data.mcd[day_count-1][1]
										+'<br/><b>ASR : </b>'+ response_data.asr[day_count-1][1];
								}
							}else{
								return this.series.name+': <b>'+this.y+'</b>';
							}
                        }
                                  
                    },
                    legend: {
                        layout: 'horizontal',
                        align: 'center',
                        x: 0,
                        verticalAlign: 'top',
                        y: 0,
                        backgroundColor: '#EFEFEF'
                    },
                    series: [
                        {
                            name: '<?php echo gettext("Total Calls"); ?>',
                            type: 'column',
                            color:'#6E8CD7  ',
							yAxis:0,
                            data: response_data.total
                        },
                        {
                            name: '<?php echo gettext("Answered Calls"); ?>',
                            type: 'spline',
                            color:'#34D3EB',
                            yAxis:0,
                            data: response_data.answered,
                            marker: {
                                enabled: true
                            },
                            dashStyle: 'shortdot'
                        },
                        {
                            name: '<?php echo gettext("Failed Calls"); ?>',
                            type: 'spline',
                            color:'#E94E02',
                            yAxis:0,
                            data: response_data.failed,
                            marker: {
                                enabled: true
                            },
                            dashStyle: 'shortdot'
                        },
                        {
                            name: '<?php echo gettext("Profit"); ?>',
                            type: 'spline',
                            yAxis:1,
                            color:'#008000',
                            data: response_data.profit,
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
    
    function get_account_count(drop_val,year,month){
    		$.ajax({
            type:'POST',
            url: "<?php echo base_url(); ?>dashboard/account_count",
            dataType: 'JSON',
            data:{"drop_val":drop_val,"year":year,"month":month},
            success: function(response_data) {     
            	$("#accounts_count").html(response_data.count);
            }
        });
    }
    
    function get_total_calls(drop_val,year,month){
    		$.ajax({
            type:'POST',
            url: "<?php echo base_url(); ?>dashboard/call_count",
            dataType: 'JSON',
            data:{"drop_val":drop_val,"year":year,"month":month},
            success: function(response_data) {     
            	$("#call_count").html(response_data.total_calls);
            }
        });
    }
    
    function get_orders_count(drop_val,year,month){
    		$.ajax({
            type:'POST',
            url: "<?php echo base_url(); ?>dashboard/orders_count",
            dataType: 'JSON',
            data:{"drop_val":drop_val,"year":year,"month":month},
            success: function(response_data) {     
            	$("#order_count").html(response_data.count);
            }
        });
    }
    
    function get_refill_value(drop_val,year,month){
    		$.ajax({
            type:'POST',
            url: "<?php echo base_url(); ?>dashboard/getrefill_value",
            dataType: 'JSON',
            data:{"drop_val":drop_val,"year":year,"month":month},
            success: function(response_data) {     
            	$("#refill_value").html(response_data.total_refill_amount);
            }
        });
    }
    
    function build_call_graph(url,drop_val){
    		var month_year=$("#month_year_dropdown").val();
    		var items = month_year.split('#');	
        var year=items[1];
        var month=items[0];
        get_account_count(drop_val,year,month);
				get_total_calls(drop_val,year,month);
				get_orders_count(drop_val,year,month);
				get_refill_value(drop_val,year,month);
				
        $.ajax({
            type:'POST',
            url: "<?php echo base_url(); ?>dashboard/customerReport_maximum_call"+url+"/",
            dataType: 'JSON',
            async: false ,
            data:{"year":year,"month":month,"drop_val":drop_val},
            beforeSend: function(){
                $("#call-count").append('<div class="loading col-md-offset-6"><div class="ball-clip-rotate"><div></div></div></div>');
                $("#call-count").show();
            },
            complete: function(){
                $("#call-count").empty();
                $("#call-count").hide();
            },
            success: function(response_data) {
           // alert(response_data);
				var radiobuttonvalue = $("input[name='calls_pie_chart']:checked").val();
				if(radiobuttonvalue == 'count'){
					var graphby = "<?php echo gettext('By Calls'); ?>";
				}else{
					var graphby = "<?php echo gettext('By Minutes'); ?>";
				}
				$("#bygraph").val(graphby);
                $("#call-count").hide();
                if(response_data == ''){
                    $("div.call_count_data").hide();            
                    $("div.not_data").addClass("second");
                    $("div.not_data").show();
                    $('div.not_data').html('<i class="fa fa-meh-o"></i> <?php echo gettext("No Records Found"); ?>');
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
                                    enabled: false
                                },
                                showInLegend: true
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
    
    function build_country_graph(url,drop_val){
    		var month_year=$("#month_year_dropdown").val();
    		var items = month_year.split('#');	
        var year=items[1];
        var month=items[0];

			//	alert("<?php echo base_url(); ?>dashboard/customerReport_maximum_country"+url+"/");
        $.ajax({
            type:'POST',
            url: "<?php echo base_url(); ?>dashboard/customerReport_maximum_country"+url+"/",
            dataType: 'JSON',
            data:{"year":year,"month":month,"drop_val":drop_val},
            beforeSend: function(){
                $("#country-count").append('<div class="loading col-md-offset-6"><div class="ball-clip-rotate"><div></div></div></div>');
                $("#country-count").show();
            },
            complete: function(){
                $("#country-count").empty();
                $("#country-count").hide();
            },
            success: function(response_data) {
				var radiobuttonvaluecountry = $("input[name='country_pie_chart']:checked").val(); 
				if(radiobuttonvaluecountry == 'count'){
					var graphby = "<?php echo gettext('By Calls'); ?>";
				}else{
					var graphby = "<?php echo gettext('By Minutes'); ?>";
				}
				$("#bygraphcountry").val(graphby);
                $("#country-count").hide();
                if(response_data == ''){
                    $("div.country_count_data").hide();            
                    $("div.country_not_data").addClass("second");
                    $("div.country_not_data").show();
                    $('div.country_not_data').html('<i class="fa fa-meh-o"></i> <?php echo gettext("No Records Found"); ?>');
                }
                else{
                    $("div.country_not_data").hide();
                    $("div.country_count_data").show();
                    $('#country_count_data').highcharts({
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
                                    enabled: false
                                },
                                showInLegend: true
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
    
    function build_today_result(){
    		$.ajax({
            type:'POST',
            url: "<?php echo base_url(); ?>dashboard/get_today_result",
            dataType: 'JSON',
            success: function(response_data) {     
            	$("#today_refill_value").html(response_data.today_refill_amount);
            	$("#today_order_count").html(response_data.today_order_count);
            	$("#today_accounts_count").html(response_data.today_account_count);
            	$("#today_call_count").html(response_data.today_total_calls);
            }
        });
    }
    
    $(document).ready(function() {
	    $('.selectpicker').selectpicker();  
        var monthNames = [
                        "<?php echo gettext('January'); ?>",
                        "<?php echo gettext('February'); ?>",
                        "<?php echo gettext('March'); ?>",
                        "<?php echo gettext('April'); ?>",
                        "<?php echo gettext('May'); ?>",
                        "<?php echo gettext('June'); ?>",
                        "<?php echo gettext('July'); ?>",
                        "<?php echo gettext('August'); ?>",
                        "<?php echo gettext('September'); ?>",
                        "<?php echo gettext('October'); ?>",
                        "<?php echo gettext('November'); ?>",
                        "<?php echo gettext('December'); ?>"
            ];
        var month=$("#month_dropdown").val();
        $("#month_year_name").html(monthNames[month-1]);
        build_recharge_graph('t_week');
        build_call_graph('minutes','t_week');
        build_country_graph('minutes','t_week');
        
        build_today_result();

        $('input[name=calls_pie_chart]').change(function(){
            var radiobuttonvalue = $("input[name='calls_pie_chart']:checked").val();
            var drop_val=$("#today_dropdown").val();
            build_call_graph(radiobuttonvalue,drop_val);
        });
       	$('input[name=country_pie_chart]').change(function(){
            var radiobuttonvaluecountry = $("input[name='country_pie_chart']:checked").val();
            var drop_val=$("#today_dropdown").val();
            build_country_graph(radiobuttonvaluecountry,drop_val);
        });
        $("#month_year_dropdown").change(function(){
            var month_year=$("#month_year_dropdown").val();
            var radiobuttonvalue = $("input[name='calls_pie_chart']:checked").val();
            var radiobuttonvaluecountry = $("input[name='country_pie_chart']:checked").val();
            $("#month_year_name").html(monthNames[month-1]);
            build_recharge_graph('t_month');
            build_country_graph(radiobuttonvaluecountry,'t_month');
            build_call_graph(radiobuttonvalue,'t_month');
			
        });
        
        $("#today_dropdown").change(function(){
            var drop_val=$("#today_dropdown").val();
          	var radiobuttonvalue = $("input[name='calls_pie_chart']:checked").val();
          	var radiobuttonvaluecountry = $("input[name='country_pie_chart']:checked").val();
          	build_recharge_graph(drop_val);
            $("#month_year_name").html(monthNames[month-1]);
            build_country_graph(radiobuttonvaluecountry,drop_val);
            build_call_graph(radiobuttonvalue,drop_val);
            
        });
        
        
        $.ajax({
            type:'POST',
            url: "<?php echo base_url(); ?>dashboard/customerReport_calculation",
            dataType: 'JSON',
            success: function(response_data) {                   	  
            	$("#asr_today").html(response_data.ASR);
            	$("#asr_month").html(response_data.ASR_month);
            	$("#acd_today").html(response_data.ACD);
            	$("#acd_month").html(response_data.ACD_month);
            	$("#mcd_today").html(response_data.mcd);
            	$("#mcd_month").html(response_data.mcd_month);
            	$("#total_calls_today").html(response_data.total_calls);
            	$("#total_calls_month").html(response_data.total_calls_month);
            	$("#debit_today").html(response_data.total_debit);
            	$("#debit_month").html(response_data.total_debit_month);
            	$("#cost_today").html(response_data.total_cost);
            	$("#cost_month").html(response_data.total_cost_month);
            	$("#profit_today").html(response_data.profit);
            	$("#profit_month").html(response_data.profit_month);
            }
        });
        
        
            
             
    });
    
</script> 
<? endblock() ?>
<?php startblock('page-title') ?>
    <?php echo $page_title; ?>
   
<?php endblock() ?>
<? startblock('content') ?>

<section class="slice p-0">
    <div class="w-section inverse p-0">
        <div class="">
                <div class="row">
                    <div class="col-sm-12 mb-5">
                            <div class="col-lg-9 col-md-8 p-0 float-left card border">
                            <h3 class="text-dark float-left col-sm-12 p-3">
                                  <div class="p-0 float-left">
                                     <div class="" style="margin-top:3px;">
                                        <i class="fa fa-bar-chart-o text-primary fa-fw"></i> <?php echo gettext("Call Stat"); ?>
                                     </div>
                                  </div>

                             <div class="float-right col-lg-8 col-md-9 p-0" id="floating-label">
                            
                            <div class="today_dd col-md-3 float-right pb-md-0">
                                <select id="today_dropdown" name='today' class="form-control selectpicker m-0" style="z-index:9;">   
                                		
                                    <option value="t_week"><?php echo gettext("This Week"); ?></option>
                                    <option value="t_month"><?php echo gettext("By Month"); ?></option>
                                </select>
                                
                            </div>
                            <div class="year_select d-none">
                           	<div class="col-md-3 float-right pb-3 pr-md-0 pt-3 pt-md-0 pb-md-0">
                          				<select id="month_year_dropdown" name='year' class="form-control selectpicker m-0" style="z-index:9;">
                          				<?php
                          					for ($i =0; $i < 6; $i++) {
																			echo '<option'.$selected.' value="'.date("m#Y", strtotime( date( 'Y-m-01' )." -$i months")).'">'.date("F Y", strtotime( date( 'Y-m-01' )." -$i months")).'</option>'."\n";
																		}	
																	?>
                          				</select>
                            	</div>
                            </div>
                             </div>
                            </h3>

                                

                            <div id="call-graph"></div>
                            <div id='call_graph_data' class='call_graph_data p-0'></div>
                        </div>

                        <div class="float-left col-lg-3 col-md-4 px-sm-0 pl-md-4 pt-4 pt-md-0 p-0">
                                <div id="month_total_info" class="col-12">
                                <div class="card p-4">
                                        <div class="col-md-12 float-left p-1 alert-dark">
                                            <div class="col-6 float-left"><?php echo gettext("Today"); ?></div>
                                            <div class="col-6 float-left text-right"><?php echo gettext("This Month"); ?></div>
                                        </div>
                                        <div class="col-md-12 float-left p-2 border border-primary">
                                            <div class="col-12 text-left badge text-primary"><?php echo gettext("ASR (%)"); ?></div>
                                            <div class="col-6 float-left" id="asr_today">0.00</div>
                                            <div class="col-6 float-left text-right" id="asr_month">0.00</div>
                                        </div>
                                        <div class="col-md-12 float-left p-2 border border-dark">
                                            <div class="col-12 text-left badge text-dark"><?php echo gettext("ACD"); ?></div>
                                            <div class="col-6 float-left" id="acd_today">0</div>
                                            <div class="col-6 float-left text-right" id="acd_month">0</div>
                                        </div>
                                        <div class="col-md-12 float-left p-2 border border-warning">
                                            <div class="col-12 text-left badge text-warning"><?php echo gettext("MCD"); ?></div>
                                            <div class="col-6 float-left" id="mcd_today">0.0000</div>
                                            <div class="col-6 float-left text-right" id="mcd_month">0.0000</div>
                                        </div>
                                        <div class="col-md-12 float-left p-2 border border-danger">
                                            <div class="col-12 text-left badge text-danger"><?php echo gettext("Total Calls"); ?></div>
                                            <div class="col-6 float-left" id="total_calls_today">0</div>
                                            <div class="col-6 float-left text-right" id="total_calls_month">0</div>
                                        </div>
                                        <div class="col-md-12 float-left p-2 border border-secondary">
                                            <div class="col-12 text-left badge text-secondary"><?php echo gettext("Debit"); ?></div>
                                            <div class="col-6 float-left" id="debit_today">0.0000 USD</div>
                                            <div class="col-6 float-left text-right" id="debit_month">0.0000 USD</div>
                                        </div>
                                        <div class="col-md-12 float-left p-2 border border-info">
                                            <div class="col-12 text-left badge text-info"><?php echo gettext("Cost"); ?></div>
                                            <div class="col-6 float-left" id="cost_today">0.0000 USD</div>
                                            <div class="col-6 float-left text-right" id="cost_month">0.0000 USD</div>
                                        </div>
                                        <div class="col-md-12 float-left p-2 border border-success">
                                            <div class="col-12 text-left badge text-success"><?php echo gettext("Profit"); ?></div>
                                            <div class="col-6 float-left" id="profit_today">0.0000 USD</div>
                                            <div class="col-6 float-left text-right" id="profit_month">0.0000 USD</div>
                                        </div>
                  
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="dashboard_values row mb-5">
                        <a href="<?php echo base_url(); ?>accounts/customer_list/" class="col-lg-3 col-md-6 col-sm-12 pt-2">
                            <div class="bg-primary card col-12 text-light">
                            	<div class="alert-primary col-md-12 pt-2 px-0">
                        					<dl class="row m-0">
                        						<dt class="col-7"><?php echo gettext("Today"); ?></dt>
                        						<dd class="col-5 text-right" id="today_accounts_count">0</dd>
                        					</dl>
	                            </div>
                                <div class="col-lg-8 col-7 float-left py-4">
                                    <div class="h1" id="accounts_count">0</div>
                                    <h3><?php echo gettext("New Accounts"); ?></h3>
                                </div>
                                <div class="col-lg-4 col-5 float-left py-4">
                                    <i class="fa fa-users fa-4x float-left"></i>
                                </div>
                            </div>
                        </a>
                        <a href="<?php echo base_url(); ?>orders/orders_list/" class="col-lg-3 col-md-6 col-sm-12 pt-2">
                            <div class="card col-12 text-light bg-success">
                            <div class="alert-success col-md-12 pt-2 px-0">
                        					<dl class="row m-0">
                        						<dt class="col-7"><?php echo gettext("Today"); ?></dt>
                        						<dd class="col-5 text-right" id="today_order_count">0</dd>
                        					</dl>
	                            </div>
                                <div class="col-lg-8 col-7 float-left py-4">
                                    <div class="h1" id="order_count">0</div>
                                    <h3><?php echo gettext("Orders"); ?></h3>
                                </div>
                                <div class="col-lg-4 col-5 float-left py-4">
                                    <i class="fa fa-shopping-cart fa-4x float-left"></i>
                                </div>
                            </div>
                        </a>
                        <a href="<?php echo base_url(); ?>reports/refillreport/" class="col-lg-3 col-md-6 col-sm-12 pt-2">
                            <div class="card col-12 text-light bg-dark">
                            <div class="alert-dark col-md-12 pt-2 px-0">
                        					<dl class="row m-0">
                        						<dt class="col-6"><?php echo gettext("Today"); ?></dt>
                        						<dd class="col-6 text-right" id="today_refill_value">0</dd>
                        					</dl>
	                            </div>
                                <div class="col-lg-8 col-7 float-left py-4">
                                    <div class="h1" id="refill_value">0</div>
                                    <h3><?php echo gettext("Refills"); ?> (<?php echo $currency;?>)</h3>
                                </div>
                                <div class="col-lg-4 col-5 float-left py-4">
                                    <i class="fa fa-credit-card fa-4x float-left"></i>
                                </div>
                            </div>
                        </a>
                        <a href="<?php echo base_url(); ?>reports/customerReport/" class="col-lg-3 col-md-6 col-sm-12 pt-2">
                            <div class="card col-12 text-light bg-danger">
                            <div class="alert-danger col-md-12 pt-2 px-0">
                        					<dl class="row m-0">
                        						<dt class="col-7"><?php echo gettext("Today"); ?></dt>
                        						<dd class="col-5 text-right" id="today_call_count">0</dd>
                        					</dl>
	                            </div>
                                <div class="col-lg-8 col-7 float-left py-4">
                                    <div class="h1" id="call_count">0</div>
                                    <h3><?php echo gettext("Total Calls"); ?></h3>
                                </div>
                                <div class="col-lg-4 col-5 float-left py-4">
                                    <i class="fa fa-phone fa-4x float-left"></i>
                                </div>
                            </div>
                        </a>
                    </div>

            <div class="row">
                <div class="col-lg-6">
                    <div class="card mb-5">
                            <h3 class="text-dark p-3">
                                <i class="fa fa-users text-primary fa-fw"></i> <?php echo gettext("Top 10 Accounts"); ?>
                              <div class="dropdown float-right col-5 p-0">
								<input type="text" class="border-primary col-8 float-right form-control text-center text-primary" readonly="true" name="bygraph" id="bygraph" value="<?php echo gettext("By Minutes"); ?>" style="background: transparent;font-size: 12px;">
                                <button type="button" class="btn btn-link dropdown-toggle position-relative float-right py-0" id="radioToggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  <i class="fa fa-ellipsis-v"></i>
                                </button>
								
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="radioToggle">
                                  <label class="dropdown-item">
                                    <input type="radio" name="calls_pie_chart" id="option2" value="count" class="ace" autocomplete="off"> <?php echo gettext("By Calls"); ?>
                                  </label>
                                  <label class="dropdown-item">
                                    <input type="radio" name="calls_pie_chart" id="option1" value="minutes" class="ace" autocomplete="off" checked> <?php echo gettext("By Minutes"); ?>
                                  </label>
                                </div>
                              </div>
                            </h3>
                        <div class="card-body">
                            <div id="call-count"></div>
                            <div id='call_count_data' class=' call_count_data col-md-12' style ='display:none'></div>  
                            <div id='not_data' class='col-md-12 not_data' style ='display:none'></div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card mb-5">
                            <h3 class="text-dark p-3">
                              <i class="fa fa-plane text-primary fa-fw"></i> <?php echo gettext("Top 10 Countries"); ?>
                              <div class="dropdown float-right col-5 p-0">
								<input type="text" class="border-primary col-8 float-right form-control text-center text-primary" readonly="true" name="bygraphcountry" id="bygraphcountry" value="By Minutes" style="background: transparent;font-size: 12px;">
                                <button type="button" class="btn btn-link dropdown-toggle position-relative float-right py-0" id="radioToggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  <i class="fa fa-ellipsis-v"></i>
                                </button>
								
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="radioToggle">
                                  <label class="dropdown-item">
                                    <input type="radio" name="country_pie_chart" id="option22" value="count" class="ace" autocomplete="off"> <?php echo gettext("By Calls"); ?>
                                  </label>
                                  <label class="dropdown-item">
                                    <input type="radio" name="country_pie_chart" id="option11" value="minutes" class="ace" autocomplete="off" checked><?php echo gettext("By Minutes"); ?>
                                  </label>
                                </div>
                              </div>
                            </h3>

                        <div class="card-body">
                        		<div id="country-count"></div>
                            <div id='country_count_data' class='col-md-12 country_count_data' style ='display:none'></div>
                            <div id='country_not_data' class='col-md-12 country_not_data' style ='display:none'></div>
                        </div>
                    </div>
                </div>
            </div>   
            
            <div class="row">
                 <div class="col-lg-12">
                    <div class="card">
                        <h3 class="text-dark p-3"><i class="fa fa-shopping-cart text-primary fa-fw"></i> <?php echo gettext("Latest Orders"); ?>
                                
                        <a href="<?php echo base_url();?>orders/orders_list/" class="float-right btn btn-secondary"><?php echo gettext("View All"); ?></a>
                        </h3>
                        <div class="card-body">
			   <div class="table-responsive">
                            <table class="table table-hover">
                              <thead class="thead-light">
                                <tr>
                                  <th scope="col"><?php echo gettext("Date"); ?></th>
                                  <th scope="col"><?php echo gettext("Order"); ?></th>
                                  <th scope="col"><?php echo gettext("Account"); ?></th>
                                  <th scope="col"><?php echo gettext("Payment Method"); ?></th>
                                  <th scope="col"><?php echo gettext("Setup Fee"); ?>  (<?php echo $currency;?>)</th>
                                  <th scope="col"><?php echo gettext("Price"); ?> (<?php echo $currency;?>)</th>
                                  <th scope="col"><?php echo gettext("Status"); ?></th>
                                </tr>
                              </thead>
                              <tbody>
                              <?php
                              		$accountinfo = $this->session->userdata ( 'accountinfo' );
																	if($accountinfo['type'] == '1'){
																		$reseller_id = $accountinfo['id'];
																	}else{
																		$reseller_id = "0";
																	}
																	$where_arr = array (
																			"orders.reseller_id" => $reseller_id 
																	);
            											$query = $this->db_model->getJionQuery('orders','orders.id,orders.order_id ,orders.order_date,orders.accountid,orders.payment_gateway,orders.payment_status,orders.reseller_id,orders.accountid,order_items.setup_fee,order_items.price',$where_arr, 'order_items','orders.id=order_items.order_id', 'inner', 10 , 0,'DESC','orders.order_date');
																	
																	if($query->num_rows > 0){
																		$result_array =  $query->result_array();
																		foreach($result_array as $key=>$val){
																			if($val['accountid'] != "" && $val['accountid'] != "0"){
																				$val['accountid'] = $this->common->get_field_name_coma_new ( 'first_name,last_name,number', 'accounts', $val ['accountid'] );
																			}
																			if($val['reseller_id'] != "" && $val['reseller_id'] != "0"){
																				$val['reseller_id'] = $this->common->get_field_name_coma_new ( 'first_name,last_name,number', 'accounts', $val ['reseller_id'] );
																			}
																			echo "<tr>";
											                echo "<td>".$val['order_date']."</td>";
											                echo "<th scope='row'>".$val['order_id']."</th>";
											                echo "<td>".$val['accountid']."</td>";
											                echo "<td>".$val['payment_gateway']."</td>";
											                echo "<td>".$this->common_model->calculate_currency_customer($val['setup_fee'])."</td>";
											                echo "<td>".$this->common_model->calculate_currency_customer($val['price'])."</td>";
											             
											                if($val['payment_status'] == "PAID"){
						                          	echo "<td><span class='badge badge-success'>".$val['payment_status']."</span></td>";
						                          }else{
						                          	echo "<td><span class='badge badge-danger'>".$val['payment_status']."</span></td>";
						                          }
											                echo "</tr>";
																		}
																	}
                                ?>
                              </tbody>
                            </table>
                         </div>
                        </div>
                    </div>
                 </div>
             </div>             
        </div>
    </div>
</section>
<script>
    $(document).ready(function(){
        $('#today_dropdown').on('change', function() {
          if ( this.value == 't_month')
          {
            $(".year_select").removeClass("d-none");
          }
          else
          {
            $(".year_select").addClass("d-none");
          }
        });
    });
</script>
<? endblock() ?>
<? startblock('sidebar') ?>
<? endblock() ?>
<? end_extend() ?> 


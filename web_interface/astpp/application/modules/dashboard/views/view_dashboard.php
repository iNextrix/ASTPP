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
        /*background-color: #62BFE4;*/
        background: #4B9ED4 none repeat scroll 0% 0%;
border: 1px solid rgba(0, 0, 0, 0.15);

box-shadow: 1px 1px 0px rgba(255, 255, 255, 0.2) inset; }
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
-webkit-transform:rotate(1turn);transform:rotate(1turn)}
}
@keyframes spin-rotate
{
0%{-webkit-transform:rotate(0deg);transform:rotate(0deg)}
to{-webkit-transform:rotate(1turn);transform:rotate(1turn)}
}<!-------------------------------------------------------------->
</style>
<script type="text/javascript">
    
    function get_recharge_info(){
        $.ajax({
            type:'POST',
            url: "<?php echo base_url(); ?>"+'dashboard/customerReport_recent_payments/',
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
                            str=str+"<td style='text-align:right;'>"+custom_data[i].credit+"</td>";  
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
        var year=$("#year_dropdown").val();
        var month=$("#month_dropdown").val();
        var monthNames = ["January",
                         "February",
                         "March",
                         "April",
                         "May",
                         "June",
                         "July",
                         "August",
                         "September",
                         "October",
                         "November",
                         "December"
            ];
        $.ajax({
            type:'POST',
            dataType: 'JSON',
            url: "<?php echo base_url(); ?>dashboard/customerReport_call_statistics_with_profit/",
            data:{"year":year,"month":month},
            beforeSend: function(){
                $("#call-graph").append('<div class="loading col-md-offset-6"><div class="ball-clip-rotate"><div></div></div></div>');
                /* -------------------- */
                $("#call-graph").show();
            },
            complete: function(){
                $("#call-graph").empty();
                $("#call-graph").hide();
            },
            success: function(response_data) {
                $("#call-graph").hide();
		$("#month_total_info").html("<b> ASR : "+response_data.total_count.asr
					    +" | ACD : "+response_data.total_count.acd
                                            +" | MCD : "+response_data.total_count.mcd
					    +" |Total Calls : "+response_data.total_count.sum
                                            +" | Debit : "+response_data.total_count.debit
                                            +" | Cost : "+response_data.total_count.cost
                                            +" | Profit : "+response_data.total_count.profit + "</b>"
                                            );
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
                                text: 'Total Calls ('+monthNames[month-1] +' - '+ year +')'
                            }
                        }, {
                            min: 0,
                            opposite: true, //optional, you can have it on the same side.
                            title: {
                                text: 'Profit per day'
                            }
                        }],
                    tooltip: {
                        backgroundColor: '#FEFEC5',
                        borderColor: 'black',
                        borderRadius: 10,
                        borderWidth: 2,
                        formatter: function() {
                            if(this.series.name == 'Total Calls'){
                                return '<b>Total : </b>'+ this.y
                                    +'<br/><b>ACD : </b>'+ response_data.acd[this.x-1][1]
                                    +'<br/><b>MCD : </b>'+ response_data.mcd[this.x-1][1]
                                    +'<br/><b>ASR : </b>'+ response_data.asr[this.x-1][1];

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
                            name: 'Total Calls',
                            type: 'column',
                            color:'#6E8CD7  ',
							yAxis:0,
                            data: response_data.total
                        },
                        {
                            name: 'Answered Calls',
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
                            name: 'Failed Calls',
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
                            name: 'Profit',
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
    function build_call_graph(url){
        var year=$("#year_dropdown").val();
        var month=$("#month_dropdown").val();
        $.ajax({
            type:'POST',
            url: "<?php echo base_url(); ?>dashboard/customerReport_maximum_call"+url+"/",
            dataType: 'JSON',
            data:{"year":year,"month":month},
            beforeSend: function(){
                $("#call-count").append('<div class="loading col-md-offset-6"><div class="ball-clip-rotate"><div></div></div></div>');
                $("#call-count").show();
            },
            complete: function(){
                $("#call-count").empty();
                $("#call-count").hide();
            },
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
    $(document).ready(function() {
        var monthNames = ["January",
                         "February",
                         "March",
                         "April",
                         "May",
                         "June",
                         "July",
                         "August",
                         "September",
                         "October",
                         "November",
                         "December"
            ];
        var month=$("#month_dropdown").val();
        $("#month_year_name").html(monthNames[month-1]);
        get_recharge_info();
        build_recharge_graph();
        build_call_graph('minutes');
        $('input[name=calls_pie_chart]').change(function(){
            var radiobuttonvalue = $("input[name='calls_pie_chart']:checked").val();
            build_call_graph(radiobuttonvalue);
        });
        $("#year_dropdown").change(function(){
	    var radiobuttonvalue = $("input[name='calls_pie_chart']:checked").val();
            build_recharge_graph();
            build_call_graph(radiobuttonvalue);
        });
        $("#month_dropdown").change(function(){
            var month=$("#month_dropdown").val();
            var radiobuttonvalue = $("input[name='calls_pie_chart']:checked").val();
            $("#month_year_name").html(monthNames[month-1]);
            build_recharge_graph();
            build_call_graph(radiobuttonvalue);
        });
    });
</script> 
<? endblock() ?>
<?php startblock('page-title') ?>
    <?php echo $page_title; ?>
<?php endblock() ?>
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
									
													
                                    <h3 class="panel-title col-md-10">
                                          <div class='col-sm-2 no-padding'>
                    					     <div class="pull-left panel_padding">
                    					        <i class="fa fa-bar-chart-o fa-fw"></i> Monthly Stat
                    					     </div>
                                          </div>


                                          <div class='col-sm-11 no-padding'>
											   <div style="font-size: 13px; width: auto; background:rgba(243, 236, 49, 0.34) none repeat scroll 0% 0% !important;" class="pull-left back_strip panel_padding" >
													<div id="month_year_name" class='pull-left'><?=date('F'); ?></div>
	                                           </div>
											   <div style="width: 91%; font-size: 13px;" class="pull-left back_strip panel_padding">
												    <div id="month_total_info" style="color:#FFFC00;font-weight: 500;" class="pull-left"></div>
											   </div>
                                          </div>
                                    </h3>


                                     <div class="col-md-2 padding-t-5">
                                    
                                    <div class="col-md-5 no-padding pull-right ">
                                        <select id="year_dropdown" name='year' class="form-control no-margin" style="z-index:9;height:29px;width:100% !important;">
                                            <?php
											$currentyear = gmdate('Y');
											$start_year = $currentyear - 1;
											$end_year = $currentyear;
											$yearArray = range($start_year, $end_year);
											foreach ($yearArray as $year) {
												$selected = ($year == $currentyear) ? 'selected' : '';
												echo '<option ' . $selected . ' value="' . $year . '">' . $year . '</option>';
											}
											?>
                                        </select>
                                    </div>
                                           <div class="col-md-6 no-padding pull-right margin-x-4">
                                        <select id="month_dropdown" name="month" class="form-control no-margin" style="z-index:9;height:29px;width:100% !important;">
                                            <?php
											$monthArray = range(1, 12);
											foreach ($monthArray as $month) {
												$monthPadding = str_pad($month, 2, "0", STR_PAD_LEFT);
												$fdate = date("F", strtotime($monthPadding));
												$selected = (date("m") == $monthPadding) ? 'selected' : null;
												echo '<option value="' . $monthPadding . '" ' . $selected . '>' . date("F", mktime(null, null, null, $monthPadding, 1)) . '</option>';
											}
											?>
                                        </select>
                                    </div>
                                     </div>
                                        
                                </div>
                                <div class="panel-body">
                                    <div id="call-graph"></div>
                                    <div id='call_graph_data' class='call_graph_data col-md-12 no-padding'></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title panel_padding margin-l-15"><i class="fa fa-users fa-fw"></i> Top 10 Accounts</h3>
                                </div>
                                <div class="panel-body">
                                    <div class="w-box col-md-6 padding-t-10 padding-b-10 pull-right margin-t-10"> 
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
                                    <h3 class="panel-title panel_padding margin-l-15"><i class="fa fa-usd fa-fw"></i> Refill Information</h3>
                                </div>
                                <div class="panel-body">
                                    <div id='recharge_data' class='col-md-12 recharge_data margin-t-10' style ='display:none'></div>
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

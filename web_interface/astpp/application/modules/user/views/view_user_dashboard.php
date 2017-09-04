<? extend('master.php') ?>
<? startblock('page-title') ?>
  <?php echo $page_title; ?>
<? endblock() ?>
<?php
	$accountinfo=$this->session->userdata('accountinfo');
	$currency=$this->common->get_field_name('currency','currency',array("id"=>$accountinfo['currency_id']));

?>
<? startblock('extra_head') ?>
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
}
</style>
<script type="text/javascript">
     function get_invoices_data(){
            $.ajax({
	      type:'POST',
	      url: "<?php echo base_url(); ?>"+'user/user_dashboard_invoices_data/',
	      cache    : false,
	      async    : false,
              success: function(response_data) {
                     var custom_data=JSON.parse(response_data);
                     
                      if(custom_data !=''){
			  $("div.invoices_not_data").hide();
			  $("div.invoices_data").show();
                          var str = "<table class='table table-bordered flexigrid'>";  
                          var arrayLength = custom_data.length;
                          for (var i = 0; i < arrayLength; i++) {
			    str=str+"<tr>";
			    if(i==0){
			      str=str+"<th style='text-align:center;'>"+custom_data[i].type+"</th>";            
			      str=str+"<th style='text-align:center;'>"+custom_data[i].id+"</th>";  
			      str=str+"<th style='text-align:center;'>"+custom_data[i].from_date+"</th>";  
			      str=str+"<th style='text-align:center;'>"+custom_data[i].invoice_date+"</th>";  
			      str=str+"<th style='text-align:center;'>"+custom_data[i].amount+"</th>";  
			    }else{
			      str=str+"<td style='text-align:center;'>"+custom_data[i].type+"</td>";            
			      str=str+"<td style='text-align:center;'>"+custom_data[i].id+"</td>";  
			      str=str+"<td style='text-align:center;'>"+custom_data[i].from_date+"</td>";  
			      str=str+"<td style='text-align:center;'>"+custom_data[i].invoice_date+"</td>";            
			      str=str+"<td style='text-align:center;'>"+custom_data[i].amount+"</td>";
			    }
			    str=str+"</tr>";
			  }   
                          str+="</table>";
                          document.getElementById("invoices_data").innerHTML = str;  
                       }
                       if(custom_data ==''){
			  $("div.invoices_data").hide();       
			  $("div.invoices_not_data").addClass("second");
			  $("div.invoices_not_data").show();
			  $('div.invoices_not_data').text('No Records Found');
                       }
                }
            });
      };
       function get_payment_data(){
            $.ajax({
            type:'POST',
            url: "<?php echo base_url(); ?>"+'user/user_dashboard_recent_payments/',
            cache    : false,
            async    : false,
                success: function(response_data) {
                     var custom_data=JSON.parse(response_data);
                      if(custom_data !=''){
			$("div.recharge_not_data").hide();
                        $("div.recharge_data").show();
                        var str = "<table class='table table-bordered flexigrid'>";  
                        var arrayLength = custom_data.length;
                        var currency_code = "<?php echo $currency ?>";
                        str=str+"<tr><th style='text-align:center;'>Date</th><th style='text-align:center;'>Amount ("+currency_code +")</th><th style='text-align:center;'>Notes</th></tr>";
                        for (var i = 0; i < arrayLength; i++) {
                          str=str+"<tr>";
                          if(i==0){
                           str=str+"<td style='text-align:center;'>"+custom_data[i].payment_date+"</td>";
                           str=str+"<td style='text-align:center;'>"+custom_data[i].credit+"</td>";
                           str=str+"<td style='text-align:center;'>"+custom_data[i].notes+"</td>";
                          }else{
                           str=str+"<td style='text-align:center;'>"+custom_data[i].payment_date+"</td>";
                           str=str+"<td style='text-align:center;'>"+custom_data[i].credit+"</td>";
                           str=str+"<td style='text-align:center;'>"+custom_data[i].notes+"</td>";
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
function get_package_data(){
            $.ajax({
            type:'POST',
    url: "<?php echo base_url(); ?>"+'user/user_dashboard_package_data/',
    cache    : false,
    async    : false,
                success: function(response_data) {
                    if(response_data !=''){
                     var custom_data=JSON.parse(response_data);
                     var str="";
                      if(custom_data !=''){
                      $("div.package_not_data").hide();
		      $("div.package_data").show();
                      str=str+"<table class='table table-bordered flexigrid'>";
                          var arrayLength = custom_data.length;
                          for (var i = 0; i < arrayLength; i++) {
			      str=str+"<tr>";
			      if(i==0){
				str=str+"<th style='text-align:center;'>"+custom_data[i].package_name+"</th>";
				str=str+"<th style='text-align:center;'>"+custom_data[i].includedseconds+"</th>";
				str=str+"<th style='text-align:center;'>"+custom_data[i].status+"</th>";
			      }else{
				str=str+"<td style='text-align:center;'>"+custom_data[i].package_name+"</td>";
				str=str+"<td style='text-align:center;'>"+custom_data[i].includedseconds+"</td>";
				str=str+"<td style='text-align:center;'>"+custom_data[i].status+"</td>";
			      }
			      str=str+"</tr>";
			   }   
			   str+="</table>";
                           document.getElementById("package_data").innerHTML = str;  
                        }
                        if(custom_data ==''){
			  $("div.package_data").hide();       
			  $("div.package_not_data").addClass("second");
			  $("div.package_not_data").show();
			  $('div.package_not_data').text('No Records Found');
                        }
                    }
                }
            });
      };
 function get_subscription_data(){
            $.ajax({
            type:'POST',
    url: "<?php echo base_url(); ?>"+'user/user_dashboard_subscription_data/',
    cache    : false,
    async    : false,
                success: function(response_data) {
                  
                     var custom_data=JSON.parse(response_data);
                     var str="";
                      if(custom_data !=''){
                      $("div.not_data").hide();
          $("div.subscription_data").show();
                      str=str+"<table class='table table-bordered flexigrid'>";
                          var arrayLength = custom_data.length;
                          for (var i = 0; i < arrayLength; i++) {
                         
          str=str+"<tr>";
          if(i==0){
            str=str+"<th style='text-align:center;'>"+custom_data[i].charge_id+"</th>";
            str=str+"<th style='text-align:center;'>"+custom_data[i].assign_date+"</th>";
            str=str+"<th style='text-align:center;'>"+custom_data[i].sweep_id+"</th>";
          }else{
            str=str+"<td style='text-align:center;'>"+custom_data[i].charge_id+"</td>";
            str=str+"<td style='text-align:center;'>"+custom_data[i].assign_date+"</td>";
           str=str+"<td style='text-align:center;'>"+custom_data[i].sweep_id+"</td>";
          }
          str=str+"</tr>";
        }   
                          str+="</table>";
                                document.getElementById("subscription_data").innerHTML = str;  
                        }
                        if(custom_data ==''){
                        $("div.subscription_data").hide();        
      $("div.subscription_not_data").addClass("second");
      $("div.subscription_not_data").show();
      $('div.subscription_not_data').text('No Records Found');
                        }
                    
                }
            });
      };
      $(document).ready(function() {
      
          get_subscription_data();
          get_package_data();
    get_payment_data();
    get_invoices_data();
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
                        <div class="col-lg-6">
                            <div class="panel panel-default">
                                <div class="panel-heading">
				    <h3 class="panel-title col-md-10">
                                          <div class='col-sm-6 no-padding'>
                    					     <div class="pull-left panel_padding">
                    					        <i class="fa fa-file-text-o fa-fw"></i><?php echo gettext('Invoice Information')?>
                    					     </div>
                                          </div>
                                    </h3>
				</div>
				<div class="panel-body">
				    <div id='invoices_data' class='col-md-12 invoices_data margin-t-10' style ='display:none'></div>
				    <div id='invoices_not_data' class='col-md-12 invoices_not_data' style ='display:none'></div>
				</div>    
			    </div> 
			    </div>
			    
			    <div class="col-lg-6">
                            <div class="panel panel-default">
                                <div class="panel-heading">
				    <h3 class="panel-title col-md-10">
                                          <div class='col-sm-6 no-padding'>
                    					     <div class="pull-left panel_padding">
                    					        <i class="fa fa-usd fa-fw"></i><?php echo gettext('Refill Information')?>
                    					     </div>
                                          </div>
                                    </h3>
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
	    <div class="row">
                <div class="col-md-12 no-padding">        
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="panel panel-default">
                                <div class="panel-heading">
				    <h3 class="panel-title col-md-10">
                                          <div class='col-sm-6 no-padding'>
                    					     <div class="pull-left panel_padding">
                    					        <i class="fa fa-money fa-fw"></i><?php echo gettext('Subscription Information')?>
                    					     </div>
                                          </div>
                                    </h3>
				</div>
				<div class="panel-body">
				    <div id='subscription_data' class='col-md-12 subscription_data margin-t-10' style ='display:none'></div>
				    <div id='subscription_not_data' class='col-md-12 subscription_not_data' style ='display:none'></div>
				</div>    
			    </div> 
			    </div>
			    
			    <div class="col-lg-6">
                            <div class="panel panel-default">
                                <div class="panel-heading">
				    <h3 class="panel-title col-md-10">
                                          <div class='col-sm-6 no-padding'>
                    					     <div class="pull-left panel_padding">
                    					        <i class="fa fa-dropbox fa-fw"></i><?php echo gettext('Package Information')?>
                    					     </div>
                                          </div>
                                    </h3>
				</div>
				<div class="panel-body">
				    <div id='package_data' class='col-md-12 package_data margin-t-10' style ='display:none'></div>
				    <div id='package_not_data' class='col-md-12 package_not_data' style ='display:none'></div>
				</div>    
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

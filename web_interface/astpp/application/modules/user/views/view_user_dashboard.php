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
 function get_payment_data(){
            $.ajax({
            type:'POST',
//     		dataType: 'JSON',
		url: "<?php echo base_url();?>"+'dashboard/user_recent_payments/',
		cache    : false,
		async    : false,
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
 				    str=str+"<th style='text-align:center;'>"+custom_data[i].credit+"</th>";
 				  }else{
 				    str=str+"<td style='text-align:center;'>"+custom_data[i].payment_date+"</td>";
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
     function get_invoices_data(){
            $.ajax({
            type:'POST',
//     		dataType: 'JSON',
		url: "<?php echo base_url();?>"+'user/user_invoices_data/',
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
function get_package_data(){
            $.ajax({
            type:'POST',
//     		dataType: 'JSON',
		url: "<?php echo base_url();?>"+'user/user_package_data/',
		cache    : false,
		async    : false,
                success: function(response_data) {
                  
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
//  				    str=str+"<th style='text-align:center;'>"+custom_data[i].credit+"</th>";
 				  }else{
 				    str=str+"<td style='text-align:center;'>"+custom_data[i].package_name+"</td>";
				    str=str+"<td style='text-align:center;'>"+custom_data[i].includedseconds+"</td>";
//  				    str=str+"<td style='text-align:center;'>"+custom_data[i].credit+"</td>";
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
            });
      };
 function get_subscription_data(){
            $.ajax({
            type:'POST',
//     		dataType: 'JSON',
		url: "<?php echo base_url();?>"+'user/user_subscription_data/',
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
//  				    str=str+"<th style='text-align:center;'>"+custom_data[i].credit+"</th>";
 				  }else{
 				    str=str+"<td style='text-align:center;'>"+custom_data[i].charge_id+"</td>";
				    str=str+"<td style='text-align:center;'>"+custom_data[i].assign_date+"</td>";
//  				    str=str+"<td style='text-align:center;'>"+custom_data[i].credit+"</td>";
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
		    <div class="col-md-6  padding-trb-l">
			<div class="col-md-12 color-three w-box">
			    <h4 class="col-md-5 no-padding" style="color:#3989c0;">Invoice Information</h4>
			    <div id='invoices_data' class='col-md-12 invoices_data' style ='display:none'></div>
			    <div id='invoices_not_data' class='col-md-12 invoices_not_data' style ='display:none'></div>
			</div>
		    </div>   
		    <div class="col-md-6  padding-trb-l">
			<div class="col-md-12 color-three w-box">
			   <h4 class="col-md-5 no-padding" style="color:#3989c0;">Recharge Information</h4>
			   <div id='recharge_data' class='col-md-12 recharge_data' style ='display:none'></div>
			   <div id='recharge_not_data' class='col-md-12 recharge_not_data' style ='display:none'></div>
			</div>
		    </div>
	        </div>
   	        <div class="col-md-12 no-padding">
                <!---GRAPH--->
                <div class="col-md-6  padding-trb-l">
                <div class="col-md-12 color-three w-box">
                <h4 class="col-md-5 no-padding" style="color:#3989c0;">Subscription Information</h4>
	          <div id='subscription_data' class='col-md-12 subscription_data' style ='display:none'></div>
	          <div id='subscription_not_data' class='col-md-12 subscription_not_data' style ='display:none'></div>
	        </div>
	        </div>
                <div class="col-md-6  padding-trb-l">
                <div class="col-md-12 color-three w-box">
                <h4 class="col-md-5 no-padding" style="color:#3989c0;">Package Information</h4>
	          <div id='package_data' class='col-md-12 package_data' style ='display:none'></div>
	          <div id='package_not_data' class='col-md-12 package_not_data' style ='display:none'></div>
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

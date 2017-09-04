<? extend('master.php') ?>
<? startblock('extra_head') ?>
<link href="<?= base_url() ?>assets/css/shop_popup.css" rel="stylesheet" type="text/css"/>
<script src="<?= base_url() ?>assets/js/jquery-ui.min.js"></script>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        build_grid("refill_coupon_grid","",<? echo $grid_fields; ?>,"");
        $("#refill_coupon_recharge").click(function () {
	  var refill_coupon_no = document.getElementById('refill_coupon_number').value; 
	  if(refill_coupon_no !=''){
	    if (!/^[0-9]+$/.test(refill_coupon_no)) {
	      $("#refill_coupon_number_error").html('The Coupon Number field must contain only numbers.');
	      document.getElementById('refill_coupon_number').focus();
	    }
	    else{
	      $.ajax({
		  type: "POST",
		  url: "<?= base_url()?>user/user_refill_coupon_number/"+refill_coupon_no+"/",
		  data:'',
		  success:function(response) {
		  var data = jQuery.parseJSON(response);
		    if(response == 1){
			$("#refill_coupon_number_error").html('The  Coupon Number field have inactive refill coupon.');
		    }else if (response ==2){
			$("#refill_coupon_number_error").html('This Coupon Number is already in use.');
		    }else if (response ==3){
			$("#refill_coupon_number_error").html('This Coupon Number is not found.');
		    }
		    else{
			$("#amount_refill").html(data.amount);
			$("#new_balance").html(data.new_balance);
			$("#dialog").dialog({modal: true, height: 230, width: 400 });
		    }
		  }
	      });
	  }
	  }else{
	      $("#refill_coupon_number_error").html('The Coupon Number field is required.');
	      document.getElementById('refill_coupon_number').focus();
	  }
	});
	
	$("#submit_refill_coupon").click(function () {
	    var refill_coupon_no = document.getElementById('refill_coupon_number').value;
	    window.location = "<?= base_url()?>user/user_refill_coupon_action/"+refill_coupon_no+"/";
	});
    });
    
</script>
<style>
    #err
    {
         height:20px !important;width:100% !important;float:left;
    }
    label.error {
        float: left; color: red;
        padding-left: .3em; vertical-align: top;  
        padding-left:0px;
        margin-top:-10px;
        width:100% !important;
       
    }
</style>
<? endblock() ?>

<? startblock('page-title') ?>
<?= $page_title ?>
<? endblock() ?>

<? startblock('content') ?>   
<section class="slice color-three margin-b-20">
    <div class="w-section inverse no-padding">
        <div class="container">
            <div class="row">
                <div class="portlet-content">
                    <form method="post" name="refill_coupon_add" id="refill_coupon_add" action="#" enctype="multipart/form-data" class="margin-t-20">
			          <label class="col-md-2" ><?php echo gettext('Coupon Number:')?></label>
						  <div class="col-md-2">
				  	  	<input type="input" class="col-md-4 form-control" name="refill_coupon_number" id="refill_coupon_number" maxlength="20">
				  	  </div>
			       </form>
			       <div class="col-md-2">
				<a class="btn btn-line-parrot margin-l-10" id="refill_coupon_recharge"><?php echo gettext('Recharge')?></a>
			       </div>
			       <div class ='col-md-12 no-padding '  style="color:red;display:block;margin-left:17.8%;!important" id="refill_coupon_number_error" name='refill_coupon_number_error'></div>

                </div>
            </div>
        </div>
    </div>
</section>

<section class="slice color-three padding-b-20 padding-t-20">
	<div class="w-section inverse no-padding">
    	<div class="container">
        	<div class="row">
				<div class="col-md-12 color-three padding-b-20 slice color-three pull-left ">
                    <table id="refill_coupon_grid" align="left" style="display:none;"></table>
                </div>   				
    		</div>
    	</div>
	</div>
</section>
<div id="dialog" style='display:none;' title="REFILL COUPON RECHARGE">
	<center>
    	<p>You just recharged  account </p>
		<div>
			<font size="4" color="red"><span id="popup_num" name="popup_num"></span></font> with <font size="4" color="red">
			  <span id="amount_refill" name="amount_refill"></span></font>
		</div>
		<div>
			 The new balance will be 
			  <font size="4" color="red">
			      <span id="new_balance" name="new_balance"></span>
			  </font>
		<br/>
		</div>
		<br/>
		<div>
		    <input type= "button" class="btn btn-success" id= "submit_refill_coupon" style="color:white;background-color:#79aed5;" onclick="get_balance();" value = "Continue"/>
		</div>
	</center>
</div>             
                
<? endblock() ?>	

<? end_extend() ?>  


<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
	function invoice_delete(inv_id){
				$.ajax({
			type: "POST",
			url: "<?= base_url()?>/invoices/invoice_delete_statically/"+inv_id,
			data:'',
			success:function(alt) {
				   var confirm_string = "Are you sure want to delete record.";
				var answer = confirm(confirm_string);
				if(answer){
					window.location.href="<?= base_url()?>/invoices/invoice_delete_massege/";
				}
				else{
					return false;
				}
			}
		});
	}
	function validateForm(){
		  if(document.getElementById('from_date').value == ""){
			$('#error_msg_from').text( "Please select from date" );
			document.getElementById('from_date').focus();
			return false;
		  }
		  if(document.getElementById('to_date').value == ""){
			$('#error_msg_to').text( "Please select to date" );
			document.getElementById('to_date').focus();
			return false;
		  }
		  if(document.getElementById('to_date').value < document.getElementById('from_date').value){
			$('#error_msg_to').text( "Please select to date bigger than from date" );
				document.getElementById('to_date').focus();
				return false;
		  }
		  document.getElementById('invoice').disabled = 'true';
		  document.getElementById("myForm2").submit();     
		  event.preventDefault();
	}
    $(document).ready(function() {
        build_grid("invoices_grid","",<? echo $grid_fields; ?>,<? echo $grid_buttons; ?>);
        $('.checkall').click(function () {
            $('.chkRefNos').attr('checked', this.checked);
        });
        $("#invoice_search_btn").click(function(){
            post_request_for_search("invoices_grid","","invoice_search");
        });        
        $("#id_reset").click(function(){
            clear_search_request("invoices_grid","");
        });
        $("#generate_search").click(function(){
			$("#search_generate_bar").slideToggle("slow");
        });
        $("#invoice_date").datetimepicker({format:'Y-m-d',timepicker: false});		
        var currentdate = new Date(); 
        var invoice_from_date = currentdate.getFullYear() + "-"
            + ('0' + (currentdate.getMonth()+1)).slice(-2) + "-" 
            + ('0' + (currentdate.getDate()+1)).slice(-2) + "";
        var invoice_to_date = currentdate.getFullYear() + "-"
           +('0' + (currentdate.getMonth()+1)).slice(-2) + "-" 
            + ('0' + (currentdate.getDate()+1)).slice(-2) + "";
		$("#invoice_from_date").val(invoice_from_date);		
        $("#invoice_from_date").datetimepicker({format:'Y-m-d',timepicker: false});
        $("#invoice_to_date").val(invoice_to_date);
        $("#invoice_to_date").datetimepicker({format:'Y-m-d',timepicker: false});
        $("#from_date").val('');
        $("#from_date").datetimepicker({format:'Y-m-d',timepicker: false});		
        $("#from_date").change(function(){
			$('#error_msg_from').text('');
			return false;
		});
        $("#to_date").val('');
        $("#to_date").datetimepicker({format:'Y-m-d',timepicker: false});
	    $("#to_date").change(function(){
			$('#error_msg_to').text('');
			return false;
		});
		$("#accountid").change(function(){
			$('#error_msg_port').text('');
			return false;
		});
    });
</script>
	
<? endblock() ?>

<? startblock('page-title') ?>
    <?= $page_title ?>
<? endblock() ?>

<? startblock('content') ?>     
<?php
$login_type = $this->session->userdata['userlevel_logintype']; 
 $account_data = $this->session->userdata("accountinfo");
 $id = $account_data['id'];

?>   

<div class="portlet-header ui-widget-header" align=right><span id="generate_search" style="cursor:pointer;align:right;margin-right:10px" class='btn-warning btn'><?php echo gettext('Generate Invoice'); ?></span><span class="ui-icon ui-icon-circle-arrow-s"></span></div>

<section class="slice color-three">
	<div class="w-section inverse no-padding">
    	<div class="container">
   	    <div class="row">
            	<div class="portlet-content"  id="search_bar" style="cursor:pointer; display:none">
                    	<?php echo $form_search; ?>
    	        </div>
            </div>
        </div>
    </div>
</section>
<div class="portlet-content"  id="search_generate_bar" style="cursor:pointer; display:none">
<section class="slice color-three padding-b-20">
	<div class="w-section inverse no-padding">
    	<div class="container">
        	<div class="row">
                <div class="col-md-12 padding-t-10" style="margin-top:10px;" >
	<form method="post" action="<?= base_url() ?>invoices/invoice_screen/" enctype="multipart/form-data" name='form1' id="myForm2" >
	<fieldset>	
		<legend><?php echo gettext('Generate Invoice'); ?></legend>
				
			<div style="" ><label style="text-align:left;float:left;" class="col-md-1"><?php echo gettext('From Date'); ?>    </label>
			<div class="col-md-2 ">
			     <input class="form-control " id="from_date" name="fromdate" size="20" type="text">
			</div>
			</div>
		        <div><label style="text-align:left;float:left;" class="col-md-1"><?php echo gettext('To Date'); ?>   </label>
			<div class="col-md-2 ">
		              <input class="col-md-1 form-control " value="" id="to_date" name="todate" size="20" type="text">
		        </div>
		        </div>
	<div class="col-md-5"><label style="text-align:left;float:left;" class="col-md-3"><?php echo gettext('Accounts'); ?> </label>
			<?php
				if($login_type == -1 || $login_type == 4 || $login_type == 2){
				        $where="deleted = '0' AND reseller_id = '0' AND status = '0' AND (type= '0' OR type= '3' OR type= '1')";
				}if($login_type == 1 ){

				        $where="deleted = '0' AND reseller_id = '$id' AND status = '0' AND (type= '0' OR type= '3' OR type='1')";
				
				}
		$account=$this->db_model->build_dropdown_invoices('id,first_name,last_name,number,type', 'accounts', '', $where);?>
		    
	   <?php  echo form_dropdown_all('accountid', $account,''); ?>
			</div>
	                <div>
		        <div><label style="text-align:left;float:left;" class="col-md-1"><?php echo gettext('Notes'); ?>   </label>
			<div class="col-md-2 ">
		              <input class="col-md-1 form-control " value="" id="notes" name="notes" size="20" type="text">
		        </div>
		        </div>
			  <input type="button" class="btn btn-line-parrot margin-l-20" name="invoice" value="Generate invoice" id="invoice"  onClick="validateForm();" style="margin-left:01%;">
			</div>
                      	<span style="color:red;margin-left:120px;float:left;" id="error_msg_from"></span>
		        <span style="color:red;margin-left:440px;float:left;" id="error_msg_to"></span>
			<span style="color:red;margin-left:750px;float:left;" id="error_msg_port"></span>
	</fieldset>	
	    </form>
                  
            </div>
        </div>
    </div>
</section>
</div>
<br/>
<section class="slice color-three padding-b-20">
	<div class="w-section inverse no-padding">
    	<div class="container">
        	<div class="row">
                <div class="col-md-12">      
                        <form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">
                            <table id="invoices_grid" align="left" style="display:none;"></table>
                        </form>
                </div>  
            </div>
        </div>
    </div>
</section>

<? endblock() ?>	

<? end_extend() ?> 

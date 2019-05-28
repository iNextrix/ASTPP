<?php include(FCPATH.'application/views/popup_header.php'); ?>

<section class="slice m-0">
 <div class="w-section inverse p-0">
        <div class="col-md-12 card-header">
	        <h3 class="fw4 p-4 m-0"><? echo $page_title; ?></h3 class="bg-secondary text-light p-3 rounded-top">
		</div>
  </div>    
</section>
<section class="slice color-three pb-4">
  <div id="floating-label" class="w-section inverse p-4">
	 <form method="POST" action="<?= base_url() ?>did_purchase/did_purchase_account_save/" enctype="multipart/form-data" id="did_forward" name="did_forward">    
   <div class='col-md-12 form-group'>
        <label class="col-md-3 p-0">Accounts:</label>
        <div class="col-md-6 p-0">
	   
 		<?php
		  if(isset($logtype) && $logtype != 0 || $logtype != 3 )
		  {
			$reseller_id=$this->session->userdata ( 'did_reseller_id' );
			if(isset($logtype) && $logtype == 1)
			{
				$reseller_id=$accountinfo['reseller_id'];
			}else{
				$reseller_id=isset($reseller_id) ? $reseller_id :0;
			}
		 	$where = array("status"=>0,"deleted"=>0,"type"=>0,"reseller_id"=>$reseller_id);
			$accountid_arry = array("id" => "accountid", "name" => "accountid", "class" => "accountid");
			$accountid = form_dropdown($accountid_arry, $this->db_model->build_concat_dropdown("id,first_name,last_name,number", "accounts", "where_arr", $where), '');
			echo $accountid;
		  }
		?>

        </div>
    </div>
	<div class='col-md-12'>
		<div class="col-md-1 float-right badge">Voicemail</div>	
	</div>
	<div class='col-md-12 form-group'>
        <label class="col-md-3 p-0">Call Type:</label>
        <div class="col-md-4 p-0">
	   
 		<?php
			$country_arr = array("id" => "call_type", "name" => "call_type", "class" => "call_type");
			$country = form_dropdown($country_arr, $this->db_model->build_dropdown("call_type_code,call_type", "did_call_types", "", ""), '');
			echo $country;
		?>

		
	    <!-- </select> -->


        </div>






                  


        <div class="col-md-4 pr-0">
	    <input type='text' name='extensions' class="col-md-12 form-control form-control-lg">

        </div>
        <div class="col-md-1 pr-0">
		<input type='checkbox' name="call_type_vm_flag" >
       </div>
      </div>
	 <div class="card" id="floating-label">
           <div class="col-12 form-inline">
	     <h3 class="bg-secondary text-light p-3 rounded-top">DID Forward</h3>
		<input class="col-md-12 form-control form-control-lg" name='id'  type="hidden"/>
	
	
	<!--<div class='col-md-12 form-group'>
        <label class="col-md-3 p-0">Destination:</label>
        <div class="col-md-4 p-0">
	   <select class="form-control form-control-lg selectpicker"  name="extensions" id="extensions">
		<option value="0" <?php if(isset($extensions) && (0 == $extensions))echo 'selected';?>>DID-Local</option>
		<option value="1" <?php if(isset($extensions) && (1 == $extensions))echo 'selected';?>>DID@IP/URL </option>
		<option value="2" <?php if(isset($extensions) && (2 == $extensions))echo 'selected';?>>Direct-IP </option>
		<option value="3" <?php if(isset($extensions) && (3 == $extensions))echo 'selected';?>>Other </option>
		<option value="4" <?php if(isset($extensions) && (4 == $extensions))echo 'selected';?>>PSTN </option>
		<option value="5" <?php if(isset($extensions) && (5 == $extensions))echo 'selected';?>>SIP-DID </option>

	
	    </select>
        </div>
        <div class="col-md-4 pr-0">
	   <?php if($extensions == '') { ?>
	    <input type='text' name='extensions' class="col-md-12 form-control form-control-lg">
		<?php } else { ?>
	    <input type='text' name='extensions' value= "<?php echo $extensions;  ?>" class="col-md-12 form-control form-control-lg">
	  <?php } ?>
        </div>
        <div class="col-md-1 pr-0">
		<input type='checkbox' name="extensions" <?php if($extensions_vm_flag == 0){ ?> checked <?php } ?>>
       </div>>-->
      </div>
	<div class='col-md-12 form-group'>
        <label class="col-md-3 p-0">Always:</label>
        <div class="col-md-4 p-0">
	   <select class="form-control form-control-lg selectpicker"  name="always" id="always">
		<!--<option value="3" <?php if(isset($ALWAYS) && (3 == $ALWAYS))echo 'selected';?>>--Select-- </option>-->
		<option value="0" <?php if(isset($always) && (0 == $always))echo 'selected';?>>DID-Local</option>
		<option value="1" <?php if(isset($always) && (1 == $always))echo 'selected';?>>DID@IP/URL </option>
		<option value="2" <?php if(isset($always) && (2 == $always))echo 'selected';?>>Direct-IP </option>
		<option value="3" <?php if(isset($always) && (3 == $always))echo 'selected';?>>Other </option>
		<option value="4" <?php if(isset($always) && (4 == $always))echo 'selected';?>>PSTN </option>
		<option value="5" <?php if(isset($always) && (5 == $always))echo 'selected';?>>SIP-DID </option>

	
	    </select>
        </div>
        <div class="col-md-4 pr-0">
	    <input type='text' name='always_destination' class="col-md-12 form-control form-control-lg">
	
        </div>
        <div class="col-md-1 pr-0">
		<input type='checkbox' name="always_vm_flag" >
       </div>
      </div>
     <div class='col-md-12 form-group'>
        <label class="col-md-3 p-0">If Busy:</label>
        <div class="col-md-4 p-0">
	   <select class="form-control form-control-lg selectpicker"  name="user_busy" id="user_busy">
		<!--<option value="3" <?php if(isset($ALWAYS) && (3 == $ALWAYS))echo 'selected';?>>--Select-- </option>-->
		<option value="0" <?php if(isset($user_busy) && (0 == $user_busy))echo 'selected';?>>DID-Local</option>
		<option value="1" <?php if(isset($user_busy) && (1 == $user_busy))echo 'selected';?>>DID@IP/URL </option>
		<option value="2" <?php if(isset($user_busy) && (2 == $user_busy))echo 'selected';?>>Direct-IP </option>
		<option value="3" <?php if(isset($user_busy) && (3 == $user_busy))echo 'selected';?>>Other </option>
		<option value="4" <?php if(isset($user_busy) && (4 == $user_busy))echo 'selected';?>>PSTN </option>
		<option value="5" <?php if(isset($user_busy) && (5 == $user_busy))echo 'selected';?>>SIP-DID </option>
	 </select>
        </div>
        <div class="col-md-4 pr-0">
	    <input type='text' name='user_busy_destination' class="col-md-12 form-control form-control-lg">

        </div>
        <div class="col-md-1 pr-0">
		<input type='checkbox' name="user_busy_vm_flag" >
       </div>
      </div>
    <div class='col-md-12 form-group'>
        <label class="col-md-3 p-0">If SIP Not Registered:</label>
        <div class="col-md-4 p-0">
	   <select class="form-control form-control-lg selectpicker"  name="user_not_registered" id="user_not_registered">
		<!--<option value="3" <?php if(isset($ALWAYS) && (3 == $ALWAYS))echo 'selected';?>>--Select-- </option>-->
		<option value="0" <?php if(isset($user_not_registered) && (0 == $user_not_registered))echo 'selected';?>>DID-Local</option>
		<option value="1" <?php if(isset($user_not_registered) && (1 == $user_not_registered))echo 'selected';?>>DID@IP/URL </option>
		<option value="2" <?php if(isset($user_not_registered) && (2 == $user_not_registered))echo 'selected';?>>Direct-IP </option>
		<option value="3" <?php if(isset($user_not_registered) && (3 == $user_not_registered))echo 'selected';?>>Other </option>
		<option value="4" <?php if(isset($user_not_registered) && (4 == $user_not_registered))echo 'selected';?>>PSTN </option>
		<option value="5" <?php if(isset($user_not_registered) && (5 == $user_not_registered))echo 'selected';?>>SIP-DID </option>
	    </select>
        </div>
        <div class="col-md-4 pr-0">
	    <input type='text' name='user_not_registered_destination' class="col-md-12 form-control form-control-lg">

        </div>
        <div class="col-md-1 pr-0">
		<input type='checkbox' name="user_not_registered_vm_flag" >
       </div>
      </div>
       <div class='col-md-12 form-group'>
        <label class="col-md-3 p-0">If No Answer:</label>
        <div class="col-md-4 p-0">
	   <select class="form-control form-control-lg selectpicker"  name="no_answer" id="call_not_answer_type">
		<!--<option value="3" <?php if(isset($ALWAYS) && (3 == $ALWAYS))echo 'selected';?>>--Select-- </option>-->
		<option value="0" <?php if(isset($no_answer) && (0 == $no_answer))echo 'selected';?>>DID-Local</option>
		<option value="1" <?php if(isset($no_answer) && (1 == $no_answer))echo 'selected';?>>DID@IP/URL </option>
		<option value="2" <?php if(isset($no_answer) && (2 == $no_answer))echo 'selected';?>>Direct-IP </option>
		<option value="3" <?php if(isset($no_answer) && (3 == $no_answer))echo 'selected';?>>Other </option>
		<option value="4" <?php if(isset($no_answer) && (4 == $no_answer))echo 'selected';?>>PSTN </option>
		<option value="5" <?php if(isset($no_answer) && (5 == $no_answer))echo 'selected';?>>SIP-DID </option>
	    </select>
        </div>
        <div class="col-md-4 pr-0">
	    <input type='text' name='no_answer_destination' class="col-md-12 form-control form-control-lg">

        </div>
        <div class="col-md-1 pr-0">
		<input type='checkbox' name="no_answer_vm_flag" >
       </div>
      </div>	
	  <div class="col-12 my-4">
   	   <div class="col-md-6 float-left">
        	<button class="btn btn-success btn-block" name="action" value="Save" type="submit">Save</button>
          </div>
   	<div class="col-md-6 float-left">
    		<button class="btn btn-secondary btn-block mx-2" name="cancel" onclick="return redirect_page('NULL')" value="Cancel" type="button">  Cancel </button>
  	</div>                        
</div>	
	

   </div>
  </div>
</form>
</div>
</section>




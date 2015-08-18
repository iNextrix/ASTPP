<? extend('master.php') ?>	
<? startblock('page-title') ?> <? echo "Dashboard"; ?><br/>
<? endblock() ?>    
<? startblock('content') ?>  
<!--<div class="inner-page-title">			
    <h2>Account Details</h2>
</div> -->


    		<section class="slice color-three">
			<div class="w-section inverse no-padding">
				<div class="container">
        				<div class="row">
                				<div class="col-md-12">  
				
					
					<!--<a href="<?//=base_url()?>user/user_payment/">payment</a>--> 
					<!--	<li class="col-md-12">
							<label class="col-md-3 no-padding">Account Number</label>
							<input class="col-md-5 form-control" type="text" maxlength="100" size="20" value="<?= $account['number'] ?>" name="number">
						</li>
<li class="col-md-12">
							<label class="col-md-3 no-padding">Balance</label>
							<input class="col-md-5 form-control" type="text" maxlength="100" size="20" value="<?= ($account['balance']); ?>" name="balance">
						</li>
<li class="col-md-12">
							<label class="col-md-3 no-padding">Name</label>
							<input class="col-md-5 form-control" type="text" maxlength="100" size="20" value="<?= $account['first_name'] . '' . $account['last_name'] ?>" name="name">
						</li>
<li class="col-md-12">
							<label class="col-md-3 no-padding">Company</label>
							<input class="col-md-5 form-control" type="text" maxlength="100" size="20" value="<?= $account['company_name'] ?>" name="company_name">
						</li>
<li class="col-md-12">
							<label class="col-md-3 no-padding">Address</label>
							<input class="col-md-5 form-control" type="text" maxlength="100" size="20" value="<?= $account['address_1'] ?>" name="address_1">
						</li>
<li class="col-md-12">
							<label class="col-md-3 no-padding">City</label>
							<input class="col-md-5 form-control" type="text" maxlength="100" size="20" value="<?= $account['city'] ?>" name="city">
						</li>
<li class="col-md-12">
							<label class="col-md-3 no-padding">Email</label>
							<input class="col-md-5 form-control" type="text" maxlength="100" size="20" value="<?= $account['email'] ?>" name="email">
						</li>
<li class="col-md-12">
							<label class="col-md-3 no-padding">Province/State</label>
							<input class="col-md-5 form-control" type="text" maxlength="100" size="20" value="<?= $account['province'] ?>" name="province">
						</li>
<li class="col-md-12">
							<label class="col-md-3 no-padding">Zip/Postal Code</label>
							<input class="col-md-5 form-control" type="text" maxlength="100" size="20" value="<?= $account['postal_code'] ?>" name="postal_code">
						</li>
<li class="col-md-12">
							<label class="col-md-3 no-padding">Country</label>
							<input class="col-md-5 form-control" type="text" maxlength="100" size="20" value="<?= $account['country_id'] ?>" name="country_id">
						</li>
<li class="col-md-12">
							<label class="col-md-3 no-padding">Credit Limit in</label>
							<input class="col-md-5 form-control" type="text" maxlength="100" size="20" value="<?= $account['credit_limit'] ?>" name="credit_limit">
						</li>
<li class="col-md-12">
							<label class="col-md-3 no-padding">Timezone</label>
							<input class="col-md-5 form-control" type="text" maxlength="100" size="20" value="<?= $account['timezone_id'] ?>" name="timezone_id">
						</li>
<li class="col-md-12">
							<label class="col-md-3 no-padding">Max Channels</label>
							<input class="col-md-5 form-control" type="text" maxlength="100" size="20" value="<?= $account['maxchannels'] ?>" name="maxchannels">
						</li>
<li class="col-md-12">
							<label class="col-md-3 no-padding">Telephone</label>
							<input class="col-md-5 form-control" type="text" maxlength="100" size="20" value="<?= $account['telephone_1'] ?>" name="telephone_1">
						</li>
-->

<br/>
            		 	<table>  
					<tr>
					    <th class="col-md-3 no-padding">Account</th>
					    <td class="col-md-5"><?= $account['number'] ?></td>
					</tr>
					<tr>                    
						<th class="col-md-3 no-padding">Balance</th>
						<td class="col-md-5"><?= ($account['balance']); ?></td>
					</tr>
			    	<? /*	<tr>                
						<th>Account Type</th><td><?= ucfirst(Common_model::$global_config['userlevel'][$account['type']]); ?></td>
					</tr> */ ?>
					<tr>
					    <th class="col-md-3 no-padding">Name</th>
					    <td class="col-md-5"><?= $account['first_name'] . " " . $account['last_name'] ?></td>
					</tr>
					<tr>
						<th class="col-md-3 no-padding">Company</th>
						<td class="col-md-5"><?= $account['company_name'] ?></td>
					</tr>
					<tr>
						<th class="col-md-3 no-padding">Address</th>
						<td class="col-md-5"><?= $account['address_1'] ?></td>
					</tr>
					<tr>
						<th class="col-md-3 no-padding">City</th>
						<td class="col-md-5"><?= $account['city'] ?></td>
					</tr>
					<tr>
						<th class="col-md-3 no-padding">Email</th>
						<td class="col-md-5"><?= $account['email'] ?></td>
					</tr>
					<tr>
						<th class="col-md-3 no-padding">Province/State</th>
						<td class="col-md-5"><?= $account['province'] ?></td>
					</tr>
					<tr>  
						<th class="col-md-3 no-padding">Zip/Postal Code</th>
						<td class="col-md-5"><?= $account['postal_code'] ?></td>
					</tr>
					<tr>    
						<th class="col-md-3 no-padding">Country</th>
						<td class="col-md-5"><?= $account['country_id'] ?> </td>
					</tr>
				 <? /*       <tr>  
						<th>Billing Schedule</th><td><?= ucfirst(Common_model::$global_config['sweeplist'][$account['sweep_id']]); ?></td>
					</tr>   */ ?>
					<tr>
						<th class="col-md-3 no-padding">Credit Limit in</th>
						<td class="col-md-5"><?= $account['credit_limit'] ?></td>
					</tr>
					<tr> 
						<th class="col-md-3 no-padding">Timezone</th>
						<td class="col-md-5"><?= $account['timezone_id'] ?></td>     
					</tr> 
					<tr>  
						<th class="col-md-3 no-padding">Max Channels</th>
						<td class="col-md-5"><?= $account['maxchannels'] ?></td>
					</tr>
					<tr>  
						<th class="col-md-3 no-padding">Telephone</th>
						<td class="col-md-5"><?= $account['telephone_1'] ?></td>
					</tr>
            			</table>  
	
        	</div>
		       
		       <div class="col-md-12 margin-t-20 margin-b-20" >
				<?if( Common_model::$global_config['system_config']['paypal_status'] == 0){?>
				<a href="<?= base_url() ?>user/user_payment/"> 
				    <input class="btn btn-line-parrot pull-right" type="submit" name="payment" value="Recharge Account" />
				</a>   
				<?}else{?>
				    <a href="#"> 
				      <input class="btn btn-line-parrot" type="button" onclick="javascript:alert('Please enable paypal module from admin');" name="payment" value="Recharge Account" />
				    </a>   
				<?}?>  
				<a href="<?= base_url() ?>user/user_edit_account/">
				    <input class="btn btn-line-sky pull-right margin-x-10" rel="facebox"  type="submit" name="action" value="Edit Account" />
				</a>
			</div></div></div></div>
        	</section>
	  
				
<? endblock() ?>
<? startblock('sidebar') ?>
<? endblock() ?>
<? end_extend() ?>  

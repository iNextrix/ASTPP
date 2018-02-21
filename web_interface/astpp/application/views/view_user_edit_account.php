
<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
        <div class="portlet-header ui-widget-header">Edit Account<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
        <div class="portlet-content">
        <form action="<?=base_url()?>user/update/<?=$this->session->userdata('username')?>" id="frm_callshop" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="mode" value="Edit Account"/>
        Please enter the following information to edit account details<br><br>
      
            <ul style="width:675px; list-style:none;">
            
       <fieldset  style="width:660px;">
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Edit Account</span></legend>
                <li>   
                    <?//echo "<pre>";print_r($record);?>
                  <label class="desc">Account Number:</label>
                  <input size="20" class="text field medium" readonly name="Account Number" value="<?=$record['0']->number?>"</label>
                </li>
                 <li>
                  <label class="desc">Language:</label>
                  <?=form_languagelist('language',$record['0']->language,array("class"=>"select field small"))?>
                </li>
                <li>
                  <label class="desc">Company:</label>
                  <input size="20" class="text field medium" name="company" value="<?=$record['0']->company_name?>">
                </li>
                <li>
                  <label class="desc">Province/State:</label>
                  <input size="20" class="text field medium" name="state" value="<?=$record['0']->province;?>">
                </li>
                <li>
                  <label class="desc">Email:</label>
                   <input size="20" class="text field medium" name="email"value="<?=$record['0']->email;?>">
                </li>
               
                <li>
                  <label class="desc">Name</label>
                  <input size="60" class="text field medium"  name="name" value="<?=$record['0']->first_name;?>">
                </li>
                <li>
                  <label class="desc">City:</label>
                  <input name="city" size="30" class="text field medium" value="<?=$record['0']->city;?>">
                </li>
                <li>
                  <label class="desc">Telephone:</label>
                  <input name="telephone" size="30" class="text field medium" value="<?=$record['0']->telephone_1;?>">
                </li>
                <li>
                  <label class="desc">Address:</label>
                  <input size="20" class="text field medium" name="Address" value="<?=$record['0']->address_1;?>">
                </li>
                   
            <li>
                <?//=$record['0']->tz;?>
                  <label class="desc">Country:</label>
                  <?=form_countries('country',$record['0']->country,array("class"=>"select field small"))?>
                </li>
                <li>
                  <label class="desc">Zip/Postal Code:</label>
                  <input name="code" size="20" class="text field medium" value="<?=$record['0']->postal_code;?>">
                </li>
                <li> 
                <li>
                    
                  <label class="desc">Timezone:</label>
                  <?=form_timezone('timezone',$record['0']->tz,array("class"=>"select field medium"))?>
                </li>
                
            
                
               	</fieldset>
                      
            </ul>
        <br />
        <input type="submit" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Save" />
        <br>
        
        
        </form>
        </div>
</div>
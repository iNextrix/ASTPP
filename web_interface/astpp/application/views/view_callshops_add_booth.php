<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
        <div class="portlet-header ui-widget-header">Create CallShop Booth<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
        <div class="portlet-content">
        <form action="<?=base_url()?>callshops/add_booth" id="frm_callshop_booth" method="POST" enctype="multipart/form-data">
        Please enter the following information to create a new callshop Booth<br><br>
        <ul style="width:675px; list-style:none;">
        <fieldset  style="width:660px;">
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Callshop Booth</span></legend>
            <li>   
              <label class="desc">Booth Name</label>
              	<input name="accounttype" value="6" type="hidden">	
                 <input name="sweep" value="0" type="hidden">
                <input name="number" size="20" type="text" class="text field medium">
              </label>
            </li>
            <li>
              <label class="desc">Password</label>
              <input size="20" class="text field medium" name="accountpassword" type="password">
            </li>
            <li>
            <label class="desc">Pricelist</label>
           <select name="pricelist" id="pricelist">
           <?php foreach($price_list as $key => $value){?>
           <option value="<?=$value?>"><?=$value?></option>
           <? } ?>
           </select>
            </li>
            <li>
              <label class="desc">Credit Limit in </label>
              <input size="20" class="text field medium" name="credit_limit">
            </li>
            <li>
            <label class="desc">Currency</label>
            <?=$currency?>
            </li>
            <li>
              <label class="desc">Post Paid</label>
             <select name="posttoexternal">
             <option value="0">NO</option>
             <option selected="selected" value="1">YES</option>
             </select>
            </li>
            <li>
            <label class="desc">Dialed Number Mods</label>
            <input name="dialed_modify" size="20" type="text"  class="text field medium">
            </li>
            <li>
            <label class="desc">IP Address</label>
            <input name="ipaddr" value="dynamic" size="20" type="text" class="text field medium">
            </li>
            <li>
            <label class="desc">Add Device</label>
            <label><input name="SIP" value="on" type="checkbox">SIP</label>
            <label><input name="IAX2" value="on" type="checkbox">IAX2</label>
           </li>
            <li>
              <label class="desc">Language</label>
              <?=form_languagelist('language', 'en',array("class"=>"select field medium"))?>
            </li>
            <li>
              <label class="desc">Device Context</label>
            <input name="context" value="<?=@$context?>" type="text" class="text field medium">
            </li>
           
            <li>
              <label class="desc"></label>      
            </li>
            <li>
              <label class="desc"></label>
            </li>      
           	</fieldset> 
        </ul>
         <br>
        <input type="submit" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Generate Booth" />
        <br>
        <br /> 
      
        </form>
        </div>
</div>
<script type="text/javascript" src="/js/validate.js"></script>
<script type="text/javascript">

		$(document).ready(function() {

		// validate signup form on keyup and submit

		$("#frm_manage_did").validate({

			rules: {
				number: "required",
				limittime: "required"
			}

		});

		});

	</script>

<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
  <div class="portlet-header ui-widget-header">
    <?=isset($did)?"Edit":"Add New"?>
    DID<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
  <div class="portlet-content" style="width:830px;">
    <form action="<?=base_url()?><?=isset($did)?"did/manage/edit":"did/manage/add"?>" id="frm_manage_did" method="POST" enctype="multipart/form-data">
      <ul>
        <div style="width:400px; float:left; padding:5px;">
          <fieldset  style="width:400px;">
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">DID Information</span></legend>
            <li>
              <label class="desc">Number:</label>
              <?php if(isset($did)){?>
              <label class="desc">
                <?=@$did['number']?>
              </label>
              <input type="hidden" name="number" value="<?=@$did['number']?>" />
              <?php }else{?>
              <input type="text" name="number" class="text field medium"  size="20" value="" />
              <?php }?>
            </li>
            <?php if($this->session->userdata('logintype') != 1){?>
            <li>
              <label class="desc">Country:</label>
              <?php if(isset($did) && $this->session->userdata('logintype') == 1){?>
              <label class="desc">
                <?=@$did['country']?>
              </label>
              <?php }else{?>
              <?=form_countries('country',@$did['country'],array("class"=>"select field small"))?>
              <?php }?>
            </li>
            <?php }?>
            <?php if($this->session->userdata('logintype') != 1){?>
            <li>
              <label class="desc">Province:</label>
              <?php if(isset($did) && $this->session->userdata('logintype') == 1){?>
              <label class="desc">
                <?=@$did['province']?>
              </label>
              <?php }else{?>
              <input type="text" class="text field medium" name="province"  size="20"  value="<?=@$did['province']?>"/>
              <?php }?>
            </li>
            <?php }?>
            <?php if($this->session->userdata('logintype') != 1){?>
            <li>
              <label class="desc">City:</label>
              <?php if(isset($did) && $this->session->userdata('logintype') == 1){?>
              <label class="desc">
                <?=@$did['city']?>
              </label>
              <?php }else{?>
              <input type="text" class="text field medium" name="city"  size="20"  value="<?=@$did['city']?>"/>
              <?php }?>
            </li>
            <?php }?>
            <?php if($this->session->userdata('logintype') != 1){?>
            <li>
              <label class="desc">Provider:</label>
              <?php if(isset($did) && $this->session->userdata('logintype') == 1){?>
              <label class="desc">
                <?=@$did['provider']?>
              </label>
              <?php }else{?>
              <select name="provider" class="select field medium" >
                <?=@$providers?>
              </select>
              <?php }?>
            </li>
            <?php }?>
          </fieldset>
        </div>
        <div style="width:400px; float:left; padding:5px; margin-left:10px;">
          <fieldset  style="width:385px;">
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">DID Billing</span></legend>
            <?php if($this->session->userdata('logintype') != 1){?>
            <li>
              <label class="desc">Account:</label>
              <?php if(isset($did) && $this->session->userdata('logintype') == 1){?>
              <label class="desc">
                <?=@$did['account']?>
              </label>
              <?php }else{?>
              <select name="account"  class="select field medium">
                <option value=""></option>
                <?=@$accounts?>
              </select>
              <?php }?>
            </li>
            <?php }?>
            <li>
              <label class="desc">Setup Fee:</label>
              <input type="text" class="text field medium" name="setup"  size="20"   value="<?=$this->common_model->calculate_currency(@$did['setup'],'','',true,false)?>"/>
            </li>
            <li>
              <label class="desc">Disconnection Fee:</label>
              <input type="text" class="text field medium" name="disconnectionfee"  size="20"  value="<?=$this->common_model->calculate_currency(@$did['disconnectionfee'],'','',true,false)?>" />
            </li>
            <li>
              <label class="desc">Monthly:</label>
              <input type="text" class="text field medium" name="monthlycost"  size="20"  value="<?=$this->common_model->calculate_currency(@$did['monthlycost'],'','',true,false)?>" />
            </li>
            <li>
              <label class="desc">Connect:</label>
              <input type="text" class="text field medium" name="connectcost"  size="20"  value="<?=$this->common_model->calculate_currency(@$did['connectcost'],'','',true,false)?>"/>
            </li>
            <li>
              <label class="desc">Included:</label>
              <input type="text" class="text field medium" name="included"  size="20"  value="<?=@$did['includedseconds']?>"/>
            </li>
            <li>
              <label class="desc">Cost:</label>
              <input type="text" class="text field medium" name="cost"  size="20"  value="<?=$this->common_model->calculate_currency(@$did['cost'],'','',true,false)?>"/>
            </li>
            <li>
              <label class="desc">Increments:</label>
              <input type="text" class="text field medium" name="inc"  size="3"  value="<?=@$did['inc']?>"/>
            </li>
            <?php if($this->session->userdata('logintype') != 1){?>
            <li>
              <label class="desc">Limit Length:</label>
              <select name="limittime" class="select field medium" >
                <option value="1" <?php if(@$did['limittime'] == "1"){ echo "selected='selected'";}?> >YES</option>
                <option value="0" <?php if(@$did['limittime'] == "0"){ echo "selected='selected'";}?> >NO</option>
              </select>
            </li>
            <li>
              <label class="desc">Variables:</label>
              <input type="text" class="text field medium" name="variables"  size="60"  value="<?=@$did['variables']?>"/>
            </li>
            <li>
              <label class="desc">Bill on Allocation:</label>
              <select name="chargeonallocation" class="select field medium" >
                <option value="1" <?php if(@$cc['chargeonallocation'] == "1"){ echo "selected='selected'";}?> >YES</option>
                <option value="0" <?php if(@$cc['chargeonallocation'] == "0"){ echo "selected='selected'";}?> >NO</option>
              </select>
            </li>
          </fieldset>
        </div>
        <div style="width:400px; float:left; padding:5px; margin-top:-165px;">
          <fieldset  style="width:400px;">
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">DID Setting</span></legend>
            <li>
              <label class="desc">Dialstring:</label>
              <input type="text" class="text field medium" name="extension"  size="20"  value="<?=@$did['extensions']?>" />
            </li>
            <li>
              <label class="desc">Max Channels:</label>
              <input type="text" class="text field medium" name="maxchannels" size="3"  value="<?=@$did['maxchannels']?>"/>
            </li>
            <?php }?>
            <?php if($this->session->userdata('logintype')== 1){?>
            <li>
              <label class="desc">Prorate:</label>
              <select name="prorate" class="select field medium" >
                <option value="1" <?php if(@$cc['prorate'] == "1"){ echo "selected='selected'";}?> >YES</option>
                <option value="0" <?php if(@$cc['prorate'] == "0"){ echo "selected='selected'";}?> >NO</option>
              </select>
            </li>
            <?php }?>
            <li>
              <label class="desc">Dial As:</label>
              <input type="text" class="text field medium" name="dial_as"   value="<?=@$did['dial_as']?>"/>
            </li>
          </fieldset>
        </div>
      </ul>
      <div style="width:100%;float:left;height:50px;margin-top:20px;">
        <input type="submit" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="<?=isset($did)?"Save...":"Insert...";?>" />
      </div>
    </form>
  </div>
</div>

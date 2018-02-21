<? extend('master.php') ?>
<?php error_reporting(E_ERROR);?>
<? startblock('extra_head') ?>
<?php endblock() ?>
<?php startblock('page-title') ?>
<?=$page_title?>
<br/>
<?php endblock() ?>
<?php startblock('content')?>
 
<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
  <div class="portlet-header ui-widget-header"><!--< ?php echo isset($account)?"Edit":"Create New";?> Account-->
    <?=@$page_title?>
    <span class="ui-icon ui-icon-circle-arrow-s"></span></div>
  <div class="portlet-content">
    <form action="<?php echo base_url();?>accounting/invoiceconf/" id="invoiceconf" method="POST" enctype="multipart/form-data">
      <div style="margin:0 auto; width:1050px">
      <div style="width:500px; float:left">
        <div style="width:500px; margin-bottom:10px;">
          <fieldset  style="width:500px;">
            <legend><span style="font-size:14px; font-weight:bold; color:#000; padding:5px;"> Configuration </span></legend>
            <div class="content-box-wrapper">
              <ul>
                <li>
                  <label class="desc">Company name:</label>
                  <input name="company_name" type="text" class="text field medium" id="company_name" value="<?=$invoiceconf['company_name']?>"  size="20"/>
                </li>
                <li>
                  <label class="desc">Address</label>
                  <input name="address" type="address" class="text field medium" id="address"  value="<?=$invoiceconf['address']?>"  size="20"/>
                </li>
                <li>
                  <label class="desc">City</label>
                  <input name="city" type="city" class="text field medium" id="address"  value="<?=$invoiceconf['city']?>"  size="20"/>
                </li>
                <li>
                  <label class="desc">Province</label>
                  <input name="province" type="province" class="text field medium" id="address"  value="<?=$invoiceconf['province']?>"  size="20"/>
                </li>
                <li>
                  <label class="desc">Country</label>
                  <input name="country" type="country" class="text field medium" id="address"  value="<?=$invoiceconf['country']?>"  size="20"/>
                </li>
                <li>
                  <label class="desc">Zipcode</label>
                  <input name="zipcode" type="zipcode" class="text field medium" id="address"  value="<?=$invoiceconf['zipcode']?>"  size="20"/>
                </li>
                <li>
                  <label class="desc">Telephone</label>
                  <input name="telephone" type="telephone" class="text field medium" id="address"  value="<?=$invoiceconf['telephone']?>"  size="20"/>
                </li>
                <li>
                  <label class="desc">Fax</label>
                  <input name="fax" type="fax" class="text field medium" id="address"  value="<?=$invoiceconf['fax']?>"  size="20"/>
                </li>
                <li>
                  <label class="desc">Email Address</label>
                  <input name="emailaddress" type="emailaddress" class="text field medium" id="address"  value="<?=$invoiceconf['emailaddress']?>"  size="20"/>
                </li>
                <li>
                  <label class="desc">Website</label>
                  <input name="website" type="website" class="text field medium" id="address"  value="<?=$invoiceconf['website']?>"  size="20"/>
                </li>
              </ul>
            </div>
          </fieldset>
        </div>`        
      <div style="width:100%; float:left;">
        <input class="ui-state-default float-right ui-corner-all ui-button" type="submit" name="action" value="Save" />
      </div>
    </form>
  </div>
</div>
<?php 
	//echo $form;
?>
<? endblock() ?>
<? startblock('sidebar') ?>
<? endblock() ?>
<? end_extend() ?>

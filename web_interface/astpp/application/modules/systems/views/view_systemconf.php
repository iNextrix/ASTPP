<? extend('master.php') ?>
<?php error_reporting(E_ERROR);?>
<? startblock('extra_head') ?>
<?php endblock() ?>
<?php startblock('page-title') ?>
<?=ucfirst($group_title).' - '.$page_title?>
<br/>
<?php endblock() ?>
<?php startblock('content')?>
<style>
/*a.tooltip1 {outline:none; } a.tooltip1 strong {line-height:30px;} a.tooltip1:hover {text-decoration:none;} a.tooltip1 span { z-index:10;display:none; padding:14px 20px; margin-top:-5px; margin-left:10px; width:300px; line-height:16px; } a.tooltip1:hover span{ display:inline; position:absolute; color:#111; border:1px solid #DCA; background:#fffAF0;} .callout {z-index:20;position:absolute;top:30px;border:0;left:-0px;} a.tooltip1 span { border-radius:4px; box-shadow: 5px 5px 8px #CCC; }*/
div.tooltips-left {
  position: relative;
  display: inline;
}
div.tooltips-left span {
  position: absolute;
  padding:5px 20px;
  width:300px;
  color: #FFFFFF;
  background: #2E4E6A;
  text-align: center;
  visibility: hidden;
  border-radius: 6px;
}
div.tooltips-left span:after {
  content: '';
  position: absolute;
  top: 50%;
  right: 100%;
  margin-top: -8px;
  width: 0; height: 0;
  border-right: 8px solid #2E4E6A;
  border-top: 8px solid transparent;
  border-bottom: 8px solid transparent;
}

div:hover.tooltips-left span {
  visibility: visible;
  opacity:1;
  left: 100%;
  top: 50%;
  margin-top: -15px;
  margin-left: 15px;
  z-index: 999;
  font-size:12px;
}

div.tooltips-right {
  position: relative;
  display: inline;
}
div.tooltips-right span {
  position: absolute;
  padding:5px 20px;
  width:300px;
  color: #FFFFFF;
  background: #2E4E6A;
  text-align: center;
  visibility: hidden;
  border-radius: 6px;
}
div.tooltips-right span:after {
  content: '';
  position: absolute;
  top: 50%;
  left: 100%;
  margin-top: -8px;
  width: 0; height: 0;
  border-left: 8px solid #2E4E6A;
  border-top: 8px solid transparent;
  border-bottom: 8px solid transparent;
}

div:hover.tooltips-right span {
  visibility: visible;
  opacity:1;
  right: 100%;
  top: 50%;
  margin-top: -15px;
  margin-right: 270px;
  z-index: 999;
  font-size:12px;
}

</style>
<script>

function validateform(){
  var elem = document.getElementById('system_conf_form').elements;
  var flag=false;
        for(var i = 0; i < elem.length; i++)
        { 
          if(elem[i].name!=''){
          var disp=elem[i].name+"_error";
          if(elem[i].name=='did_global_translation'){
          $("#"+disp).parent().css("display","none");
          }else if($.trim(elem[i].value)==''){
          $("#"+disp).parent().css("display","block");
          flag=true;
          }else{
          $("#"+disp).parent().css("display","none"); 
          }
        }
        }
  if(flag)
  {
    return false;
  }
  /*$("#toast-container").css("display","block");
        $(".toast-message").html(ucfirst($group_title)+' Settings updated sucessfully!');
        $('.toast-top-right').delay(5000).fadeOut();*/
  return true;
}
</script>

<div class="container"  style='overflow-x:hidden'>
  <div class="row">
    <section class="slice color-three no-margin">
        <div class="w-section inverse no-padding">
            <div style="color:red;margin-left: 60px;">
                    <?php echo $validation_errors; ?> 
            </div>
            <div class='col-md-12'>
	    <form action="<?=base_url()?>/systems/configuration/<?=$group_title?>" accept-charset="utf-8" id="system_conf_form" method="POST" name="invoice_conf_form"  onsubmit='return validateform()'>
	    <fieldset>
	    <legend> <?=ucfirst($group_title).' - '.$page_title?></legend>
 	    <div style="width:50%;float:left;">
	    <?php $count=ceil(sizeof($details)/2); $i=0; $class="-left";?>
	<?php //echo floor(sizeof($details)/2);?>
	    <?php foreach($details as$key=>$val){ ?>
			<?php if($count==$i){
				echo '</div><div style="width:50%;float:left;">';
				$class="-right";
			} ?>
	    		<div class="col-md-12">
				<label class="col-md-5 no-padding"><?= str_replace('Smtp',"SMTP",ucfirst(str_replace("_"," ",$val['name'])));?></label>
				<div class="tooltips<?=$class?>" href='#'>
				<?php if($val['name']=='paypal_status' || $val['name']=='paypal_mode' || $val['name']=='paypal_mode' || $val['name']=='opensips' || $val['name']=='SMTP' || $val['name']=='paypal_fee' || $val['name']=='email' || $val['name']=='Calling cards rate announce' || $val['name']=='smtp' || $val['name']=='debug' || $val['name']=='country' || $val['name']=='calling_cards_rate_announce' || $val['name']=='base_currency' || $val['name']=='cc_ani_auth' || $val['name']=='calling_cards_timelimit_announce' || $val['name']=='default_timezone' || $val['name']=='calling_cards_balance_announce' ||$val['name']=='timezone'){?>
					<select name="<?=$val['name']?>" class='col-md-5 form-control'>
						<?php foreach($this->common->$val['name']() as $key1=>$val1){?>
						<option value="<?=$key1?>" <?=$val['value']==$key1?"selected='selected'":"";?> ><?=$val1?></option>
						<?php }?>
					</select>
					
				<?php }else if($val['name']=='version'){?>
				    <input value="<?=$val['value']?>" size="20" maxlength="100" class="col-md-5 form-control" type="text" readonly>
				<?php }else{?>
				<input name="<?=$val['name']?>" value="<?=$val['value']?>" size="20" maxlength="100" class="col-md-5 form-control" type="text">
				<? }?>
				<span><?= str_replace('smtp',"SMTP",$val['comment']);?></span></div>
			</div>
			<!--<li class="col-md-12 no-padding">
				<label class="col-md-5 no-padding"></label><span class="col-md-5 no-padding" style='font-size:10px;margin-bottom: 10px;'><?=$val['comment']?></span>
			</li>-->
			<div class="col-md-12 no-padding error_div">
				<div class="col-md-5">&nbsp;</div><span class="popup_error col-md-5 no-padding" id="<?=$val['name']?>_error">The <?= ucfirst(str_replace("_"," ",$val['name']));?> Field Is Required.</span>
			</div>
			<?php $i++;?>
		<?php }?>
		</div>
</fieldset></div></ul><center><div class="col-md-12 margin-t-20 margin-b-20"><button type="submit" value="save" class="btn btn-line-parrot">Save</button></div></center></div></div>
		</form>

	    </div>
        </div>      
    </section>        
  </div>
</div>

 

<? endblock() ?>
<? startblock('sidebar') ?>
<? endblock() ?>
<? end_extend() ?>

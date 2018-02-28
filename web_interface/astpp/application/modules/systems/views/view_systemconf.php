<? extend('left_panel_setting_master.php') ?>
<? startblock('extra_head') ?>
<?php endblock() ?>

<?php startblock('page-title') ?>
    <?php echo ucfirst($group_title).' - '.$page_title?>
<?php endblock() ?>
<?php startblock('content')?>
<style>
div .tleft {
  position: relative;

}
div.tleft .demo {
  visibility: hidden;
  opacity: 1;
  color: #FFF !important;
  position: absolute;
  display: inline-block;
  z-index: 1111;
  background-color: #2E4E6A !important;
  padding: 0.5em 0.8em !important;
  text-transform: none !important;
  margin-right: 0px;
  bottom: 10px;
  border-radius: 5px;
  border-color: #2E4E6A !important;
  text-align: left;
  font-size: 12px;
  width: 280px;
  left: 40%;
}
div.tleft .demo:before {
  content: '';
  position: absolute;
  background-color: #2E4E6A !important;
  bottom: -0.3em;
  left: 70%;
  margin-left: -0.3em;
  right: auto;
  top: auto;
  width: .6em;
  height: .6em;
  transform: rotate(45deg);
  z-index: 2;
  transition:background .1s linear;
}

div:hover.tleft .demo {
 visibility: visible;
  opacity: 1;
  color: #FFF !important;
  position: absolute;
  display: inline-block;
  z-index: 1111;
  background-color: #2E4E6A !important;
  padding: 0.5em 0.8em !important;
  text-transform: none !important;
  margin-right: 0px;
  bottom: 10px;
  border-radius: 5px;
  border-color: #2E4E6A !important;
  text-align: left;
  font-size: 12px;
  width: 280px;
  left: 40%;

}

div.tright {
  position: relative;
}
div.tright .demo {
   visibility:hidden;
  border-radius: 6px;
  text-align: left;
  border-radius: 6px;
  background-color: #2E4E6A !important;
  border-color: #2E4E6A !important;
  color: rgb(255, 255, 255) !important;
  bottom: 25px;
  right: -60%;
  z-index: 11;
  text-transform: none !important;
  position: absolute;
  padding: 0.5em 0.8em !important;
  display: inline-block;
  width:250px !important;
}
div.tright .demo:before {
  content: '';
  position: absolute;
  background-color: #2E4E6A !important;
  bottom: -0.3em;
  left: 70%;
  margin-left: -0.3em;
  right: auto;
  top: auto;
  width: .6em;
  height: .6em;
  transform: rotate(45deg);
  z-index: 2;
  transition:background .1s linear;
}

div:hover.tright .demo {
  visibility: visible;
  opacity: 1;
  color: #FFF !important;
  position: absolute;
  display: inline-block;
  z-index: 1111;
  background-color: #2E4E6A !important;
  padding: 0.5em 0.8em !important;
  text-transform: none !important;
  margin-right: 0px;
  bottom: 10px;
  border-radius: 5px;
  border-color: #2E4E6A !important;
  text-align: left;
  font-size: 12px;
  width: 280px;
  left: 40%;
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
          if(elem[i].name=='did_global_translation' || elem[i].name=='homer_capture_server'){
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
  return true;
}
</script>

<div id="main-wrapper" class="tabcontents">
  <div id="content">
    <div class="row">
      <div class="col-md-12 no-padding color-three border_box">
        <div class="col-md-8"><h2><?= $page_title; ?></h2></div>
        <div class="pull-right">
            <ul class="breadcrumb">
                <li>
                    <a href="<?= base_url()."systems/configuration/global"; ?>"> Global</a>
                </li>
                <?php if ($group_title != "global") { ?>
                <li class="active">
                    <a href="<?= base_url()."systems/configuration/".$group_title; ?>"> <?php echo ucfirst($group_title); ?></a>
                </li>
                <?php } ?>
            </ul>
        </div>
      </div>
      <div class="padding-15 col-md-12">
        <div class="slice color-three pull-left content_border col-md-12">
           <form action="<?=base_url()?>/systems/configuration/<?=$group_title?>" accept-charset="utf-8" id="system_conf_form" method="POST" name="invoice_conf_form"  onsubmit='return validateform()'>
              <fieldset>
               <legend> <?=ucfirst($group_title)?></legend>
                  <div style="width:50%;float:left;">
                    <?php $currency = Common_model::$global_config['system_config']['base_currency'];?>
                    <?php $count=ceil(sizeof($details)/2); $i=0; $class="tleft";?>
                    <?php foreach($details as$key=>$val){ ?>
                          <?php if($count==$i){
							  echo '</div><div style="width:50%;float:left;">';
							  $class="tright";
						  } ?>
                          <div class="col-md-12">
                            <div class="<?=$class?>" href='#'>
                            <label class="col-md-5 no-padding"><?php echo $val['display_name'];?> * </label>
                            <?php if(method_exists($this->common,$val['field_type'])){
								$option_array =  $this->common->{$val['field_type']}();
								$drpstr = '<select name="'.$val['name'].'" class="col-md-5 form-control selectpicker"  data-live-search="true">';
									  foreach($option_array as $option_key=>$option_val){
										$selected = ($val['value'] == $option_key)? "selected='selected'":"";
										$drpstr .= '<option value="'.$option_key.'"'.$selected.'>'.$option_val.'</option>';
									  }
								$drpstr .= '</select>';
								echo $drpstr;
								unset($drpstr);
							} else{ ?>
								<input name="<?php echo $val['name'] ?>" value='<?php echo isset($val['value'])?$val['value']:''; ?>' size="20" maxlength="100" class="col-md-5 form-control" type="text">
						<?php }?>
                              <span class="demo"><?php echo str_replace('smtp',"SMTP",$val['comment']);?></span>

                            </div>
                          </div>

                      <?php $i++;?>
                    <?php }?>
                  </div>
              </fieldset>
              <center>
                <div class="col-md-12 margin-t-20 margin-b-20">
                  <button type="submit" value="save" class="btn btn-line-parrot">Save</button>
                </div>
              </center>
            </form>
        </div>
      </div>
    </div>
  </div>
</div>




<? endblock() ?>
<? startblock('sidebar') ?>
<? endblock() ?>
<? end_extend() ?>

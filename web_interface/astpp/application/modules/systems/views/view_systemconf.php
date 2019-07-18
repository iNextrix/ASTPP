<? extend('left_panel_setting_master.php') ?>
<? startblock('extra_head') ?>
<?php endblock() ?>

<?php startblock('page-title') ?>
    <?php echo ucfirst($group_title).' - '.$page_title?>
<?php endblock() ?>
<?php startblock('content')?>
<style>
.tooltip-inner {
      background-color: #fff;
      color: #000;
      border: 1px solid #000;
      font-size: 14px;
  }
.arrow{
    left: 5% !important;
}
@media screen and (max-width:991px){
 .arrow{
    left: 15% !important;
  } 
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
<script type="text/javascript">
  $(document).ready(function(){

      $('.page-wrap').addClass('addon_wrap');
  });
</script>
<div id="main-wrapper">
  <div id="content" class="container-fluid">
    <div class="row">
      <div class="col-md-12 color-three border_box">
        <div class="col-md-8 float-left my-2"><h2 class="m-0 text-light"><?= $page_title; ?></h2></div>
        <div class="float-right my-2 lh19 pr-4">
          <nav aria-label="breadcrumb float-right">
            <ol class="breadcrumb m-0 p-0">
            </ol>
          </nav>
        </div>
      </div>
      <div class="p-4 col-md-12">
        <div class="slice color-three float-left content_border col-12">
           <form action="<?=base_url()?>/systems/configuration/<?=$group_title?>" accept-charset="utf-8" id="system_conf_form" method="POST" name="invoice_conf_form"  onsubmit='return validateform()'>
              <div class="setting_tooltip col-md-12 col-sm-12 float-left p-0">
							  <?php foreach($details as $key1=>$val1){
									echo "<div class='col-12 px-2'>";
										echo "<ul class='card p-0'>";
												echo "<div class='pb-4' id='floating-label'>";
													echo "<h3 class='bg-secondary text-light p-3 rounded-top'>".$key1."</h3>";?>
														<?php $currency = Common_model::$global_config['system_config']['base_currency'];?>
														<?php 
														$count=count($val1);
														$class="tleft";
														echo "<div class='col-md-12'>";
														echo "<div class='row'>";
														foreach($val1 as $key=>$val){
                              ?>
																<div class="col-md-4 col-sm-12 form-group">
																	 <label title="<?php echo str_replace('smtp',"SMTP",$val['comment']);?>" data-toggle="tooltip" data-placement="top" data-html="true" class="col-sm-6 col-xs-4 p-0 control-label"><?php echo $val['display_name'];?> * </label>
																	 <?php 
																			if($val['name']=='tax_type'){
																					$select_array=array();
																					$options =  $this->common->{$val['field_type']}();
																					  $tmp = str_replace("smtp","SMTP",$val["comment"]);
																					  $form='<select name="'.$val['name'].'[]"'." multiple='multiple' class='selectpicker select field multiselectable col-md-12 form-control form-control-lg' data-hide-disabled='true' data-actions-box='true'>\n";
																					  $selected_options=explode(",",$val['value']);
																					  foreach($selected_options as $key => $value){
																						$select_array[$value]=$value;
																					  }
																					  foreach ($options as $key => $value)
																					  { 
																						$selected = isset($select_array[$key])? "selected='selected'":"";
																						$form .= '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
																					  }
																					  $form .= '</select>';
																					  echo $form;
																			}
																			else if(method_exists($this->common,$val['field_type'])){
																			$option_array =  $this->common->{$val['field_type']}();
																			$tmp = str_replace("smtp","SMTP",$val["comment"]);

																							$drpstr = "<select name='".$val['name']."' class='col-md-12 form-control form-control-lg selectpicker sp'  data-live-search='true' data-live-search-style='begins' >";
																								  foreach($option_array as $option_key=>$option_val){
																									$selected = ($val['value'] == $option_key)? "selected='selected'":"";
																									$drpstr .= '<option value="'.$option_key.'"'.$selected.'>'.$option_val.'</option>';
																								  }
																							$drpstr .= '</select>';
																							echo $drpstr;
																							unset($drpstr);
																			}
																			 else{?>
                                        <input name="<?php echo $val['name'] ?>" value='<?php echo isset($val['value'])?$val['value']:''; ?>' size="20" maxlength="100" class="col-md-12 form-control form-control-lg" type="text">

																			<?php } ?>	
																	
																</div>	
														
														<?php }
														
														 ?>
														</div>
														</div>
												</div>
										</ul>
								</div>
							  <?php } ?>		
						  
			  
				  <center>
					<div class="col-12 my-4">
					  <button type="submit" value="save" class="btn btn-success"><?php echo gettext("Save"); ?></button>
					</div>
				  </center>
			  </div>	  	  
					 	
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
<script type="text/javascript" language="javascript">

$(document).ready(function() {


  $('.multiselectable').on('focus blur', function (e) {
        $(this).parents('.form-group').toggleClass('focused');
  }).trigger('blur');   

});

</script>

<script>
  $('select').on('shown.bs.select', function (e) {
    $(this).tooltip('show');
  });

  $('select').on('hidden.bs.select', function (e) {
    $(this).tooltip('hide');
  });
</script>

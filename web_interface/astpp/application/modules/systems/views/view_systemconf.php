<? extend('master.php') ?>
<? extend('left_panel_setting_master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript"
	src="<?php echo base_url(); ?>assets/ck/ckeditor/ckeditor.js"></script>
<script type="text/javascript"
	src="<?php echo base_url(); ?>assets/ck/ckfinder/ckfinder.js"></script>
<script type="text/javascript"
	src="<?php echo base_url(); ?>assets/ck/ckeditor/adapters/jquery.js"></script>
<script type="text/javascript"
	src="<?php echo base_url(); ?>assets/markup/markitup/sets/default/set.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery.validate.min.js"></script>

<script>
$ (document).ready(function(){
	$('[data-toggle="tooltip"]').tooltip();
    CKEDITOR.replace('text');
    CKEDITOR.config.width = '100%';
});
</script>
<script type="text/javascript">
$(function() {
  	$('#term_condition').markItUp(mySettings);
});

</script>


<?php endblock() ?>

<?php startblock('page-title') ?>
    <?php echo ucfirst($group_title).' - '.$page_title?>
<?php endblock() ?>
<?php startblock('content')?>
<style>
.tooltip-inner {
	max-width: 350px;
	font-size: 14px;
	text-align: left;
	line-height: 1.3;
	padding: 10px 10px;
	background: #284e7b;
	color: #fff;
	opacity: 1;
}
.arrow{
    left: 5% !important;
}
@media screen and (max-width:991px){
 .arrow{
    left: 15% !important;
  } 
}
#floating-label .form-group {
    display: grid;
    height: 55px;
    margin-top: 10px;
    padding: auto;
  
}
.setting_tooltip #floating-label .control-label {
    pointer-events: auto;
    padding: 0 0px 0 15px !important;
}
</style>
<script>
	$(document).on('change','#calling_cards_welcome_file',function(){
	// alert('Change Happened');
	var fileExtension = ['wav']; 
                $("#file_error").addClass("d-none");
				if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                     $(".custom-file-label").text("");
					document.getElementById("calling_cards_welcome_file").value = null;
					$("#welcomeDiv").addClass('d-none');
					$("#file_error").removeClass("d-none");
				}
	});
</script>
<script>

function validateform(){
  var elem = document.getElementById('system_conf_form').elements;
  var flag=false;
        for(var i = 0; i < elem.length; i++)
        {
          if(elem[i].name!=''){
          var disp=elem[i].name+"_error";
          if(elem[i].name=='did_global_translation' || elem[i].name=='homer_capture_server' || elem[i].name=='url' || elem[i].name=='text'){
            $("#"+disp).parent().css("display","none");
          }else if($.trim(elem[i].value)==''){
             $("#"+disp).parent().css("display","block");
            flag=true;
          else{
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
           <form action="<?=base_url()?>/systems/configuration/<?=$group_title?>" accept-charset="utf-8" id="system_conf_form" method="POST" name="invoice_conf_form"  onsubmit='return validateform()' enctype="multipart/form-data">
              <div class="setting_tooltip col-md-12 col-sm-12 float-left p-0">
							  <?php foreach($details as $key1=>$val1){
									echo "<div class='col-12 px-2'>";
										echo "<ul class='card p-0'>";
												echo "<div class='pb-4' id='floating-label'>";
													echo "<h3 class='bg-secondary text-light p-3 rounded-top'>".gettext($key1)."</h3>";?>
														<?php $currency = Common_model::$global_config['system_config']['base_currency'];?>
														<?php 
														$count=count($val1);
														$class="tleft";
														echo "<div class='col-md-12'>";
														if($group_title!="term_and_condition"){
														echo "<div class='row'>";
													  }
														foreach($val1 as $key=>$val){
                              ?>
														<!--	<div class="col-md-4 col-sm-12 form-group">
																	 <label title="<?php echo str_replace('smtp',"SMTP",$val['comment']);?>" data-toggle="tooltip" data-placement="top" data-html="true" class="col-sm-6 col-xs-4 p-0 control-label"><?php echo gettext($val['display_name']);?> * </label> -->
																	 <?php if($group_title=="term_and_condition"){
																		if($val['name']=="text"){
															echo'<div class="col-md-12 text-center"> OR </div>';
														}
																	 echo "<div class='row'>";
																	 }
																	 ?>
																	 <?php if($group_title=="term_and_condition"){ ?>
																	 	<div class="col-md-12 col-sm-12 form-group h-auto pt-4">
																	 		<?php }else{ ?>
																	 	<div class="col-md-4 col-sm-12 form-group ">
																	 	<?php } ?>
																	 	<label title="<?php echo str_replace('smtp',"SMTP",$val['comment']);?>" data-toggle="tooltip" data-placement="top" data-html="true" class="col-sm-6 col-xs-4 p-0 control-label" padding="0 0px 0 15px"><?php echo gettext($val['display_name']);?> <?php echo $val['name'] == 'text' || $val['name'] == 'url' ? "" : "*" ?> </label>
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
																					  
																			}else if($val['name']=='text'){ ?>
																			<div>
																			<textarea name="<?php echo $val['name'] ?>" value='' rows="1" size="20" maxlength="256" class="Emailtemplate" id="term_condition" onkeypress="myFunction()"><?php echo isset($val['value'])?$val['value']:''; ?></textarea>
																			</div>
																			<?php  }
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
																			}else if($val['name'] == 'calling_cards_welcome_file'){ ?>
                                        <div class="col-md-12 px-0">
                                          <input type="file" name="<?php echo $val['name'] ?>" id="<?php echo $val['name'] ?>" class="col-12 custom-file-input custom_class mit-20"/>
                                        <label class="btn-file btn-primary col-md-12 custom-file-label mit-20 text-left" for="file"><?php echo isset($val['value'])?$val['value']:''; ?> <span id="welcomeDiv" class="answer_list float-left d-none"></span> </label>
                                        <span id="file_error" class="text-danger  d-none col-12"><?php echo gettext("Please select only .wav files."); ?></span>						  </div>
                                        <?php } else{
                                        ?>
                                        <input  id="<?php echo $val['name'] ?>" name="<?php echo $val['name'] ?>"  onkeypress="myFunction()" value='<?php echo isset($val['value'])?$val['value']:''; ?>' rows="1" size="20" maxlength="256" class="col-md-12 form-control form-control-lg" type="text">
                                      

																			<?php } ?>	
																	
																</div>	
														
                                <?php 
														if($group_title=="term_and_condition"){
															echo "</div>";
														}
                          }
                          if($group_title!="term_and_condition"){?>
														</div>
                            <?php }?>
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

function myFunction() {
	
	 var x = document.getElementById("url").value;
               if(x != '')
               { 
				   CKEDITOR.instances.term_condition.setData('');
			   }else
			   {
				    document.getElementById("url").value = '';
			   }
	}
	
// field validation t&c text

CKEDITOR.on('instanceCreated', function(e) {
        e.editor.on('contentDom', function() {
            e.editor.document.on('keypress', function(event) {
            document.getElementById("url").value = '';
            }
        );
    });
}); 

  //end 

</script>
<script>
	$('input[type="file"]').change(function(e){
        var fileName = e.target.files[0].name;
        $('.custom-file-label').html(fileName);
        $("#welcomeDiv").removeClass('d-none');
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

<? extend('master.php') ?>

	<? startblock('extra_head') ?>
		
<? endblock() ?>

    <? startblock('page-title') ?>
        <?=$page_title?><br/>
    <? endblock() ?>
    
	<? startblock('content') ?>




<script type="text/javascript">
  $(document).ready(function() {
  // validate signup form on keyup and submit
	$("#frm_fssi").validate({
		rules: {
			tem_name: "required"
//			template: "required",
//			vm_password: "required"
		}
	});
  });
</script>

 
    
<!--<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
            <div class="portlet-header ui-widget-header">Email Template<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
            <div class="portlet-content">-->
            <form method="post" action="<?=base_url()?><?=isset($template)?"systems/template/edit/$edit_id":"systems/template/add"?>" id="frm_fssi" enctype="multipart/form-data">
            <?php if(isset($system)){?>
            
            <?php }?>
            <ul style="width:1037px">
            <fieldset  style="width:1005px;margin-left: 120px;">
            <legend><span style="font-size:14px; font-weight:bold; color:#000;">Email Template</span></legend>
            <li>
            <label style="font-weight: 500px;" class="desc" >Template Name:</label><input class="text field medium" type="text" readonly value="<?php if(isset($template['name'])) echo $template['name']; ?>" name="tem_name"  size="20" />
            </li>
            <li>
            <label class="desc">Subject:</label><input class="text field medium" type="text" value="<?php if(isset($template['subject'])) echo $template['subject'];?>" name="subject"  size="20" />
            </li>
            <li>
                
            <label class="desc">Template:</label>
            <textarea name='template' class="textarea medium">
                              
         <?   if (isset($_POST['template'])) {
                                        echo $_POST['template'];
                                    } else {
                                        if (isset($template['template'])) echo $template['template'];
                                    }
            ?>
            </textarea>
            
            
            
            </li>
<!--           <li>
            <label class="desc">Reseller:</label><input class="text field medium" type="text" value="<?php if(isset($template['reseller'])) echo $template['reseller'];?>" name="reseller"  size="20" />
            </li>-->
            <li>



<?  //echo "<pre>";print_r($template);
?>
                  <div style="width:100%;float:left;height:50px;margin-top:20px;">
            <input class="ui-state-default float-right ui-corner-all ui-button" type="submit" name="action" value="Save..." />
            </div>
                
            </fieldset>
			</ul>
          
            </form>            
            </div>
</div>
<script type="text/javascript">
                                    CKEDITOR.replace( 'template',
                                    {
//                                            skin : 'v2'
                                            skin : 'office2003',
                                            height:"350", width:"880"
                                    });
</script>

<?php 
	//echo $form;
?>
    <? endblock() ?>
	
    <? startblock('sidebar') ?>
    Filter by
    <? endblock() ?>
    
<? end_extend() ?>  

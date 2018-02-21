<script type="text/javascript">
    $("#submit").click(function(){
        submit_form("chrges_form");
    })
</script>
<!--
<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
  <div class="portlet-header ui-widget-header"><!--< ?php echo isset($account)?"Edit":"Create New";?> Account
    <?=@$page_title?>
    <span class="ui-icon ui-icon-circle-arrow-s"></span></div>
     <?php echo form_error('description'); ?>
    <?php echo $form;?>
</div>
-->

<section class="slice gray no-margin">
 <div class="w-section inverse no-padding">
   <div>
     <div>
        <div class="col-md-12 no-padding margin-t-15 margin-b-10">
	        <div class="col-md-10"><b><? echo $page_title; ?></b></div>
	  </div>
     </div>
    </div>
  </div>    
</section>

<div>
  <div>
    <section class="slice color-three no-margin">
	<div class="w-section inverse no-padding">
            <div style="color:red;margin-left: 60px;">
             <?php echo $form; ?>
                <?php if (isset($validation_errors)) echo $validation_errors; ?> 
            </div>
           
        </div>      
    </section>
  </div>
</div>

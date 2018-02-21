
<script type="text/javascript">
$(document).ready(function() {
document.getElementsByName("currency_id")[0].selectedIndex = <?=$currency_id-1?>;
document.getElementsByName("timezone_id")[0].selectedIndex = <?=$timezone_id-1?>;
document.getElementsByName("country_id")[0].selectedIndex = <?=$country_id-2?>;
    $("#submit").click(function(){
        submit_form("customer_bulk_form","<?php echo base_url();?>accounts/customer_bulk_save/");
    });
});
</script>

<!--

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
                <?php if (isset($validation_errors)) echo $validation_errors; ?> 
            </div>
            <?php echo $form; ?>
        </div>      
    </section>
  </div>
</div>


......................
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
          <div class="row">
            <section class="slice color-three no-margin">
                <div class="w-section inverse no-padding"> 
                    <div style="color:red;margin-left: 60px;">
                        <?php
                        if (isset($validation_errors)) {
                           $validation_array=json_decode($validation_errors);
                           if(is_object($validation_array)){
                           $validation_array = get_object_vars($validation_array);
                           foreach($validation_array as $key=>$value)
		              echo $value."<br/>";
                           }
                           else
		              echo $validation_errors;
                           
                        }
                        ?>
                    </div>
        <?php echo $form; ?>
                </div>      
            </section>        
          </div>
        </div>    

<!--

<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
  <div class="portlet-header ui-widget-header">
    <?=@$page_title?>
    <span class="ui-icon ui-icon-circle-arrow-s"></span></div>
<div style="color:red;margin-left: 60px;">
    <?php if(isset($validation_errors))echo $validation_errors; ?> 
    </div>
    <?php echo $form;?>
</div>


-->

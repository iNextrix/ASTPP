<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
    //    $('#tabs').tabs();
    });
</script>
<? endblock() ?>

<? startblock('page-title') ?>
<?= $page_title ?><br/>
<? endblock() ?>

<? startblock('content') ?>        
<div class="container">
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






    <!--    <div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
            <div class="portlet-header ui-widget-header"><!--< ?php echo isset($account)?"Edit":"Create New";?> Account-->
      <!--          <?= @$page_title ?>
                <span class="ui-icon ui-icon-circle-arrow-s"></span></div>
            <div style="color:red;margin-left: 60px;">
                <?php if (isset($validation_errors)) {
                    echo $validation_errors;
                } ?> 
            </div>
            <?php echo $form; ?>
        </div>
    </div>
</div> -->
<? endblock() ?>	
<? startblock('sidebar') ?>
Filter by
<? endblock() ?>
<? end_extend() ?>  

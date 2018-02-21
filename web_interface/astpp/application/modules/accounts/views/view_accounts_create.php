<? extend('master.php') ?>
<?php error_reporting(E_ERROR); ?>
<? startblock('extra_head') ?>
<script type="text/javascript">
    $(document).ready(function() {
//         $(".invoice_day").hide();
//         $('label[for="Billing Day"]').hide()
        $(".sweep_id").change(function(e){
            if(this.value != 0){
                $.ajax({
                    type:'POST',
                    url: "<?= base_url()?>/accounts/customer_invoice_option",
                    data:"sweepid="+this.value, 
                    success: function(response) {
                        $(".invoice_day").html(response);
                        $('.invoice_day').show();
                        $('label[for="Billing Day"]').show()
                    }
                });
            }else{
//                 $('label[for="Billing Day"]').hide()
//                 $('.invoice_day').css('display','none');                
            }
        })
    })
</script>
<?php endblock() ?>
<?php startblock('page-title') ?>
<?= $page_title ?>
<br/>
<?php endblock() ?>
<?php startblock('content') ?>

<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
    <div class="portlet-header ui-widget-header"><!--< ?php echo isset($account)?"Edit":"Create New";?> Account-->
        <?= @$page_title ?>
        <span class="ui-icon ui-icon-circle-arrow-s"></span></div>
    <div style="color:red;margin-left: 60px;">
        <?php
        $data_errrors = json_decode($validation_errors);
        foreach ($data_errrors as $key => $value) {
            echo $value . "<br/>";
        }
        ?> 
    </div>
<?php echo $form; ?>
</div>

<? endblock() ?>
<? startblock('sidebar') ?>
<? endblock() ?>
<? end_extend() ?>

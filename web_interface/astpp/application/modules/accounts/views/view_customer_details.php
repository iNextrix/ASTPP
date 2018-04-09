<? extend('left_panel_master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        $(".sweep_id").change(function(){
            var sweep_id =$('.sweep_id option:selected').val();
            if(sweep_id != 0){
                $.ajax({
                    type:'POST',
                    url: "<?= base_url() ?>/accounts/customer_invoice_option/<?= $invoice_date ?>",
                    data:"sweepid="+sweep_id, 
                    success: function(response) {
                        $(".invoice_day").html(response);
                        $('.selectpicker').selectpicker('refresh');
                        $('.invoice_day').show();
                        $('label[for="Billing Day"]').show()
                    }
                });
            }else{
                $('label[for="Billing Day"]').hide()
                $('.invoice_day').css('display','none');                
            }
        });
        $(".change_pin").click(function(){
         var str_size='<?php echo $callingcard; ?>';
            $.ajax({type:'POST',
                url: "<?= base_url()?>accounts/customer_generate_number/"+str_size,
                success: function(response) {
                  var data=response.replace('-',' ');
                    $('#change_pin').val(data.trim());
                }
            });
        });
    });
    /************************************************************************/       

</script>
<style>
    label.error {
        float: left; color: red;
        padding-left: .3em; vertical-align: top;  
        padding-left:40px;
        margin-top:20px;
        width:1500% !important;
    }
</style>
<?php endblock() ?>
<? startblock('page-title') ?>
<?= $page_title ?>
<? endblock() ?>
<?php startblock('content') ?>
<div id="main-wrapper" class="tabcontents">
    <div id="content">   
        <div class="row"> 
            <div class="col-md-12 no-padding color-three border_box"> 
                <div class="pull-left">
                    <ul class="breadcrumb">
                    <?php $entity = $entity_name == 'provider' ? 'customer' : $entity_name; ?>
                        <li><a href="<?= base_url()."accounts/".strtolower($entity)."_list/"; ?>"><?= ucfirst($entity_name); ?>s</a></li>
                        <li class="active"><a href="<?= base_url()."accounts/".strtolower($entity_name)."_edit/".$edit_id."/"; ?>"> <?= ucfirst(@$accounttype); ?> Profile </a></li>
                    </ul>
                </div>
                <div class="pull-right">
                    <ul class="breadcrumb">
		      <li class="active pull-right"><a href="<?= base_url()."accounts/customer_list/"; ?>"> <i class="fa fa-fast-backward" aria-hidden="true"></i> Back</a></li>
                    </ul>
                </div>
            </div>     


            <div class="padding-15 col-md-12">
                <div class="slice color-three pull-left content_border">
                    <?php echo $form; ?>
                    <?php if (isset($validation_errors) && $validation_errors != '') { ?>
                        <script>
                            var ERR_STR = '<?php echo $validation_errors; ?>';
                            print_error(ERR_STR);
                        </script>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<? endblock() ?>
<? end_extend() ?>  

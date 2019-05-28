<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript">
    $(document).ready(function() {

        $('#customer_form').submit(function(){
            $("input[type='submit']", this)
            .val("Please Wait...")
            .attr('disabled', 'disabled');
            return true;
        });
        $(".change_pass").click(function(){
            $.ajax({type:'POST',
                url: "<?= base_url()?>accounts/customer_generate_password/",
                success: function(response) {
                    if(response.length > 50){
                       location.reload(true);
                   }
                   $('#password').val(response.trim());
               }
           });
        })
        $(".change_number").click(function(){
            $.ajax({type:'POST',
                url: "<?= base_url()?>accounts/customer_generate_number/",
                success: function(response) {
                   if(response.length > 50){
                       location.reload(true);
                   }
                   var data=response.replace('-',' ');
                   $('#number').val(data.trim());
               }
           });
        });
        $(".change_pin").click(function(){
            $.ajax({type:'POST',
                url: "<?= base_url()?>accounts/customer_generate_pin/",
                success: function(response) {
                  var data=response.replace('-',' ');
                  $('#change_pin').val(data.trim());
              }
          });
        });
        $(".digit_length").change(function(){
            var digit=this.value;
            $.ajax({type:'POST',
                url: "<?= base_url()?>accounts/customer_generate_number/"+digit,
                success: function(response) {
                    $('#number').val(response.trim());
                }
            });
        });
        <?php if ($entity_name != 'admin' && $entity_name != 'subadmin') { ?>
           document.getElementsByName("sweep_id")[0].selectedIndex = <?=1?>;

           $(".sweep_id").change(function(e){
            if(this.value != 0){
                $.ajax({
                    type:'POST',
                    url: "<?= base_url()?>/accounts/customer_invoice_option/",
                    data:"sweepid="+this.value, 
                    success: function(response) {
						
                        $('.invoice_day').parents('li.form-group').removeClass("d-none");               
                        $('.invoice_day').selectpicker('show');
                        $('#invoice_day').html(response);
                        $('.selectpicker').selectpicker('refresh');
                    }
                });
            }else{

                $('.invoice_day').parents('li.form-group').addClass("d-none");               
            }
        });
           $("#reseller").change(function(){
            $.ajax({
                type:'POST',
                url: "<?= base_url()?>/accounts/customer_pricelist/",
                data:"reseller_id="+this.value, 
                success: function(response) {
                   $("#pricelist_id").html(response);
                   $("#non_cli_pricelist_id").html(response);
                   $('.selectpicker').selectpicker('refresh');
               }
           });
            $.ajax({
                type:'POST',
                url: "<?= base_url()?>/accounts/reseller_distributor/",
                data:"reseller_id="+this.value, 
                success: function(response) {
                   response = $.trim(response); 
                   if(response == "Yes"){
                     $('.is_distributor').parents('li.form-group').removeClass("d-none");               
                     $('.is_distributor').selectpicker('show');
                 }else{
                     $('.is_distributor').parents('li.form-group').addClass("d-none");
                 }
             }
         });
        });
           $("#reseller").change();
           $(".sweep_id").change();
       <?php } ?> 
   });

</script>
<?php endblock() ?>
<?php startblock('page-title') ?>
<?= $page_title ?>
<?php endblock() ?>
<?php startblock('content') ?>
<div class="p-0">
	<section class="slice color-three">
		<div class="w-section inverse p-0">
    <?php echo $form; ?>
    <?php
    if (isset($validation_errors) && $validation_errors != '') {
        ?>
      <script>
       var ERR_STR = '<?php echo $validation_errors; ?>';
       print_error(ERR_STR);
   </script>
<? } ?>
</div>
	</section>
</div>
<? endblock() ?>
<? startblock('sidebar') ?>
<? endblock() ?>
<? end_extend() ?>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        $("input[type='hidden']").parents('li.form-group').addClass("d-none");
        
        
    });
</script>

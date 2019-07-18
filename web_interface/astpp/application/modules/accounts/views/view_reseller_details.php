<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
  $(document).ready(function() {
   $('.is_distributor').prop("disabled", true);
  $(".sweep_id").change(function(){
    var sweep_id =$('.sweep_id option:selected').val();
    if(sweep_id != 0){
      $.ajax({
        type:'POST',
        url: "<?= base_url() ?>/accounts/customer_invoice_option/<?= $invoice_date ?>",
        data:"sweepid="+sweep_id, 
        success: function(response) {
          $('.invoice_day').parents('li.form-group').removeClass("d-none");               
          $('.invoice_day').selectpicker('show');
          $('.invoice_day').html(response);
          $('.selectpicker').selectpicker('refresh');
        }
      });
    }else{
      $('.invoice_day').parents('li.form-group').addClass("d-none");                  
    }
  });
  $(".change_pass").click(function(){
    $.ajax({type:'POST',
      url: "<?= base_url() ?>accounts/customer_generate_password/",
      success: function(response) {
        $('#password').val(response.trim());
      }
    });
  })
  $(".change_number").click(function(){
    $.ajax({type:'POST',
      url: "<?= base_url() ?>accounts/customer_generate_number/"+10,
      success: function(response) {
        var data=response.replace('-',' ');
        $('#number').val(data.trim());
      }
    });
  })
});
         

</script>
<script type="text/javascript" language="javascript">
  $(document).ready(function() {
   
    $("input[type='hidden']").parents('li.form-group').addClass("d-none");
    
    
  });
</script>
<script type="text/javascript">
  $(document).ready(function(){
    $('.page-wrap').addClass('addon_wrap');
  });
</script>
<script type="text/javascript">
  $(document).ready(function(){
    $(".breadcrumb li a").removeAttr("data-ripple",""); 
  });
</script>
<style>
label.error {
	float: left;
	color: red;
	padding-left: .3em;
	vertical-align: top;
	padding-left: 40px;
	margin-top: 20px;
	width: 1500% !important;
}
</style>
<? endblock() ?>

<? startblock('page-title') ?>
<?= $page_title ?>
<? endblock() ?>

<? startblock('content') ?>

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
Filter by
<? endblock() ?>
<? end_extend() ?>
<script type="text/javascript" language="javascript">

$(document).ready(function() {
  $("textarea").parents('li.form-group').addClass("h-auto");	
});

</script>

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
                        $("#invoice_day").html(response);
                        $('.selectpicker').selectpicker('refresh');
                        $('.invoice_day').parent('li').show();
                    }
                });
            }else{
               $('.invoice_day').parent('li').hide();               
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
        var expiry_date = $("#expiry").val();
        $("#expiry").datetimepicker({
           value:expiry_date,
           uiLibrary: 'bootstrap4',
           iconsLibrary: 'fontawesome',
           modal:true,
           format: 'yyyy-mm-dd HH:mm:ss',
           footer:true
       });
    });
</script>
<script type="text/javascript">
  $(document).ready(function(){
      $('.page-wrap').addClass('addon_wrap');
      $("span.input-group-append").addClass('align-self-end').removeClass('input-group-append');
      $(".reset_password").parents("li").removeClass('form-group').addClass('mt-4');
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
<?php endblock() ?>
<? startblock('page-title') ?>
<?= $page_title ?>
<? endblock() ?>
<?php startblock('content') ?>
<div id="main-wrapper">
	<div id="content" class="container-fluid">
		<div class="row">
			<div class="col-md-12 color-three border_box">
				<div class="float-left m-2 lh19">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb m-0 p-0">
                        <?php $entity = $entity_name == 'provider' ? 'customer' : $entity_name; ?>
                        <li class="breadcrumb-item"><a
								href="<?= base_url()."accounts/".strtolower($entity)."_list/"; ?>"><?= gettext(ucfirst($entity_name)); ?>s</a></li>
							<li class="breadcrumb-item active" aria-current="page"><a
								href="<?= base_url()."accounts/".strtolower($entity_name)."_edit/".$edit_id."/"; ?>"> <?= ucfirst(@$accounttype); ?> <?php echo gettext('Profile');?> </a></li>
						</ol>
					</nav>
				</div>

				<div class="m-2 float-right">
					<a class="btn btn-light btn-hight"
						href="<?= base_url()."accounts/customer_list/"; ?>"> <i
						class="fa fa-fast-backward" aria-hidden="true"></i> <?php echo gettext('Back');?></a>
				</div>
   </div>


			<div class="p-4 col-md-12">
				<div class="slice color-three float-left content_border">
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


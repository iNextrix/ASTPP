<? extend('master.php') ?>
<? startblock('extra_head') ?>
<style>
    #err
    {
         height:20px !important;width:100% !important;float:left;
    }
    label.error {
        float: left; color: red;
        padding-left: .3em; 
        vertical-align: top;  
        padding-left:0px;
        width:100% !important;
       
    }
</style>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery.validate.min.js"></script>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        $('#pin_form').validate({
            rules: {
                pin: {
                    required: true,
                    normalizer: function(value) {
                        this.value = $.trim(value);
                        return this.value;
                    }
                }
            },
            errorPlacement: function(error, element) {
                error.appendTo('#err');
            }
        });
    });

</script>
<script type="text/javascript">
  $(document).ready(function(){
      $(".breadcrumb li a").removeAttr("data-ripple",""); 
  });
</script>
<? endblock() ?>
<? startblock('page-title') ?>
<?= $page_title ?>
<? endblock() ?>
<? startblock('content') ?>

<section class="slice color-three">
    <div class="w-section inverse p-0">
			<div class="pop_md col-12 pb-4 pt-2">
				 <form  method="post" name="pin_form" id="pin_form" action="<?= base_url() ?>user/user_pin_save/" enctype="multipart/form-data">
					<div class="row">
						<div class="col-md-12">
							<div class="card">
						    	<div class="pb-4" id="floating-label">
									<h3 class="bg-secondary text-light p-3 rounded-top"><?php echo gettext('Pin'); ?></h3>
									 <div class="col-md-12">
										  <div class="row p-0">
											  <div class="col-md-4">	
												  <div class="col-md-12 form-group p-0">	
													  <label class="col-md-12 p-0 control-label"><?php echo gettext('Pin');?><span> * </span></label>
													  <input type="input" class="col-md-12 form-control form-control-lg" value="<?php echo isset($pin_info['pin'])?$pin_info['pin']:""; ?>" name="pin" id="pin" maxlength="20">
												  </div>
													 <span id="err"></span>
											  </div>		
										 </div>	 
									</div>
									  <div class="col-12 my-4 text-center">
									   <button class="btn btn-success" id="animap" name="action" value="Save" type="submit"><?php echo gettext('Save');?></button>
									</div>
								</div>
							</div>
						</div>
					</div>				
				</form>
			</div>	
		</div>
</section>

<? endblock() ?>    
<? end_extend() ?>  
<script type="text/javascript">
  $(document).ready(function(){
      $('.page-wrap').addClass('addon_wrap');
  });
</script>

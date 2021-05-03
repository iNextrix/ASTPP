<? extend('master.php') ?>
<? startblock('extra_head') ?>
<style>
    label.error {
        margin-top:-10px;
    }
</style>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery.validate.min.js"></script>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
    
        build_grid("animap_list","",<? echo $grid_fields; ?>,"");
        
        $('#ani_map').validate({
            rules: {
                number: {
                    required: true,
                    normalizer: function (value) {
                    return $.trim(value);
                    }
                }
            },
            
            messages: {
               number: {
                required: "<i style='color:#D95C5C; padding-right: 6px; padding-top: 20px;float: right;' class='fa fa-exclamation-triangle'></i><span class='popup_error error  p-0'><?php echo gettext('The Caller ID field is required.'); ?></span>"
               }
            },
            errorPlacement: function(error, element) {
                 error.appendTo('#err');
            }
        });
         document.getElementById("err").style.display = "block"; 
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
			<div class="col-12">
				 <div class="pull-left" id="left_panel_add">
                <span class="btn btn-line-warning"> <i class="fa fa-plus-circle fa-lg"></i><?php echo gettext('Add');?></span>
         </div>
         <div id="left_panel_delete" class="pull-left margin-t-0 padding-x-4" onclick="delete_multiple('/user/user_animap_delete_multiple/')">
                <span class="btn btn-line-danger">
                    <i class="fa fa-times-circle fa-lg"></i>
                   <?php echo gettext('Delete'); ?>
                </span>
         </div>
			</div>
			<div class="pop_md col-12 pt-4" id="left_panel_form" style="display: none;">
				 <form  method="post" name="ani_map" id="ani_map" action="<?= base_url() ?>user/user_animap_action/" enctype="multipart/form-data">
						<div class="col-md-12 p-0">
							<div class="card">
						    	<div class="pb-4" id="floating-label">
									<h3 class="bg-secondary text-light p-3 rounded-top"><?php echo gettext('Caller ID'); ?></h3>
									 <div class="col-md-12">
										  <div class="row">
												  <div class="col-md-4 form-group">	
													  <label class="col-md-12 p-0 control-label"><?php echo gettext('Caller ID') ?></label>
													  <input type="input" class="col-md-12 form-control form-control-lg" name="number" id="number" maxlength="20">
													   <div class="text-danger tooltips error_div float-left p-0" id="err"></div>
                          </div>
										 </div>	 
									</div>
									 <div class="col-12 my-4 text-center">
									   <button class="btn btn-success" id="animap" name="action" value="Save" type="submit"><?php echo gettext("Save"); ?></button>
									</div>
								</div>
							</div>
						</div>
				</form>
			</div>	
	</div>
</section>

<section class="slice color-three mt-4">
	<div class="w-section inverse p-0">
        <div class="card col-md-12 pb-4">      
                <form method="POST"  enctype="multipart/form-data" id="ListForm">
                    <table id="animap_list" align="left" style="display:none;"></table>
                </form>
        </div>  
    </div>
</section>
<? endblock() ?>    
<? end_extend() ?>  
<script type="text/javascript">
  $(document).ready(function(){
      $('.page-wrap').addClass('addon_wrap');
      $('.checkall').click(function () {
            $('.chkRefNos').prop('checked', $(this).prop('checked'));
        });
  });
</script>

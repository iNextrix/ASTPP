<? extend('left_panel_master.php') ?>
<? startblock('extra_head') ?>
<style>
    label.error {
        float: left; color: red;
        padding-left: .3em; vertical-align: top;  
        padding-left:40px;
        margin-top:20px;
        width:1500% !important;
    }
</style>
<script type="text/javascript">
  $(document).ready(function(){
      $(".breadcrumb li a").removeAttr("data-ripple",""); 
  });
</script>
<?php endblock() ?>
<? startblock('page-title') ?>
<?= $page_title ?>
<? endblock() ?>

<? startblock('content') ?>   
<div id="main-wrapper">
    <div id="content" class="container-fluid">   
        <div class="row"> 
            <div class="col-md-12 color-three border_box"> 
                <div class="float-left m-2 lh19">
                    <nav aria-label="breadcrumb">
						<ol class="breadcrumb m-0 p-0">
						<?php $accountinfo=$this->session->userdata('accountinfo');
						  if($accountinfo['type']==1){ ?>
							<li class="breadcrumb-item"><a href="<?= base_url() . "user/user_myprofile/"; ?>"><?php echo gettext('My Profile'); ?></a></li>
                          <?php } 
                          else{ ?>
								<li class="breadcrumb-item"><a href="#"><?php echo gettext('My Account')?></a></li>
                         <?php } ?>	
							
                       
                        <li class="breadcrumb-item active" aria-current="page"><a href="<?= base_url()."user/user_change_password/"; ?>"> <?php echo gettext('Change Password'); ?> </a></li>
                        </ol>
                    </nav>
                </div>
                <div class="m-2 float-right">
						<a class="btn btn-light btn-hight" href="<?= base_url()."user/user_myprofile/"; ?>"> <i class="fa fa-fast-backward" aria-hidden="true"></i><?php echo gettext('Back'); ?></a>
                </div>
            </div>
            <div class="p-4 col-md-12">
                <div class="col-md-12">
                    <?php echo $form; ?>
                    <?php if (isset($validation_errors) && $validation_errors != '') { ?>
                        <script>
                            var ERR_STR = '<?php echo $validation_errors; ?>';
                            print_error(ERR_STR);
                        </script>
                    <? } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<? endblock() ?>
<? end_extend() ?>  
<script type="text/javascript">
  $(document).ready(function(){
      $('.page-wrap').addClass('addon_wrap');
  });
</script>

<? extend('left_panel_master.php') ?>
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
                        <li><a href="<?= base_url()."package/package_list/"; ?>">Package List </a></li>
                        <li class="active">
                            <a href="<?= base_url()."package/package_edit/".$edit_id."/"; ?>">Details </a>
                        </li>
                    </ul>
                </div>
                  <ul class="breadcrumb">
                <li class="active pull-right">
		      <a href="<?= base_url()."package/package_list/"?>"> <i class="fa fa-fast-backward" aria-hidden="true"></i> Back</a></li>
		      </ul>
            </div>
            <div class="padding-15 col-md-12">
                <div class="slice color-three pull-left content_border">
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

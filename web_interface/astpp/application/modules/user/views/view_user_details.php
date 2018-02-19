<? extend('left_panel_master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
 $(document).ready(function() {
 $(".change_pass").click(function(){
            $.ajax({type:'POST',
                url: "<?= base_url()?>user/user_generate_password/",
                success: function(response) {
                    $('#password').val(response.trim());
                }
            });
        })
        $(".change_number").click(function(){
            $.ajax({type:'POST',
                url: "<?= base_url()?>user/user_generate_number/"+10,
                success: function(response) {
                    var data=response.replace('-',' ');
                    $('#number').val(data.trim());
                }
            });
        })
         });
</script>
<? endblock() ?>

<? startblock('page-title') ?>
<?= $page_title ?>
<? endblock() ?>
<? startblock('content') ?>        
<div id="main-wrapper" class="tabcontents">
    <div id="content">   
        <div class="row"> 
            <div class="col-md-12 no-padding color-three border_box"> 
                <div class="pull-left">
                    <ul class="breadcrumb">
                        <li class="active"><a href="<?= base_url()."user/user_myprofile/"; ?>"> My Profile </a></li>
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
                    <? } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<? endblock() ?>
<? end_extend() ?>  

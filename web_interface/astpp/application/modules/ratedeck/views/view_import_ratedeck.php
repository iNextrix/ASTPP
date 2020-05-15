<? extend('master.php') ?>
<? startblock('extra_head') ?>
<? endblock() ?>
<? startblock('page-title') ?>
<?= $page_title; ?>
<? endblock() ?>
<? startblock('content') ?>        
<?php if ( ! isset($csv_tmp_data)) { ?>

<section class="slice color-three">
    <div class="w-section inverse p-0">
        <form method="post"
            action="<?= base_url()?>ratedeck/ratedeck_preview_file/"
            enctype="multipart/form-data" id="ratedeck">
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-10 clo-sm-12 float-left p-0">
                        <div class="w-box card py-3">
                            <span
                                style="margin-left: 10px; text-align: center; background-color: none; color: #DD191D;">
                            <?

if (isset($error) && ! empty($error)) {
        echo "<span class='row alert alert-danger m-2'>" . $error . "</span>";
    }
    ?>
                           </span>
                            <h3 class="px-4"><?php echo gettext('File must be in the following format(.csv):'); ?></h3>
                            <p><?= $fields;?></p>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-12 float-left pl-md-4 p-0">
                        <div class=" card col-md-12 form-group px-0">
                            <label class="card-header text-center m-0"><?php echo gettext('Get Sample file'); ?></label>
                            <div class="col-md-12 p-3">
                                <a
                                    href="<?= base_url(); ?>ratedeck/ratedeck_download_sample_file/originationrates_sample"
                                    class="btn btn-success btn btn-success btn-block"><i
                                    class="fa fa-download"></i> <?php echo gettext('Download'); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card col-md-12 p-0 mb-4">
                        <div class="pb-4" id="floating-label">
                            <h3 class="bg-secondary text-light p-3 rounded-top"><?php echo gettext('Import Ratedeck'); ?></h3>
                            <input type="hidden" name="mode"      value="import_ratedeck" />
                            <input type="hidden" name="logintype" value="<?= $this->session->userdata('logintype') ?>" />
                            <input type="hidden" name="username"  value="<?= $this->session->userdata('username') ?>" />
                            <div class="col-md-12 form-group">
                                <label class="col-12 control-label mb-4"><?php echo gettext('Select the file'); ?></label>
                                <div class="col-12 mt-4 d-flex">
                                    <div class="col-md-10 float-left" data-ripple="">
                                        <input type="file" name="ratedeckimport" id="ratedeckimport" class="custom-file-input" />
                                        <label class="custom-file-label btn-primary btn-file text-left" for="file" name="lf_ratedeckimport"> </label>
                                    </div>
                                    <div class="col-md-2 float-left align-self-center">
                                        <span id="welcomeDiv" class="answer_list float-left d-none">
                                            <button type="button" title="Cancel" class="btn btn-danger"><?php echo gettext('Remove'); ?></button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label> <span class="mr-4 align-middle"><?php echo gettext('Skip Header'); ?>:</span>
                                    <input type='checkbox' class="align-middle" name='check_header' />
                                </label>
                            </div>
                            <div class="col-md-12 form-group">
                                <div class="col-12 mt-4 d-flex">
                                    <div class="col-md-2 offset-md-8" data-ripple="">
                                        <button class="btn btn-success" id="import_ratedeck" type="submit"
                                        name="action" value="Import"><?php echo gettext('Import'); ?></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card col-md-12 p-0 mb-4">
                        <div class="pb-4" id="floating-label">
                            <h3 class="bg-secondary text-light p-3 rounded-top"><?php echo gettext('Import zones'); ?></h3>
                            <div class="col-md-12 form-group">
                                <label class="col-12 control-label mb-4"><?php echo gettext('Select the file'); ?></label>
                                <div class="col-12 mt-4 d-flex">
                                    <div class="col-md-10 float-left" data-ripple="">
                                        <input type="file" name="ratedeckimport_" id="ratedeckimport_" class="custom-file-input" />
                                        <label class="custom-file-label btn-primary btn-file text-left" for="file" name="lf_ratedeckimport_"> </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <label><span class="mr-4 align-middle"><?=gettext('Import zones form site Ministry of Communications')?></span></label>
                            </div>
                            <div class="col-md-12 form-group">
                                <div class="col-12 mt-4 d-flex">
                                    <div class="col-md-2 offset-md-8" data-ripple="">
                                        <button 
                                                disabled="disabled"
                                                class="btn btn-success"
                                                id="import_ratedeck_"
                                                value="Import">
                                            <?php echo gettext('Import'); ?>
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    <div>
                </div>
</div>
                <div class="col-md-12">
                    <div class="text-right">
                        <button class="btn btn-secondary mx-2" id="ok" type="button"
                            name="action" value="Cancel"
                            onclick="return redirect_page('/ratedeck/ratedeck_list/')" /><?php echo gettext('Cancel'); ?></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<?php }?>    
        
<?php
if (isset($csv_tmp_data) && ! empty($csv_tmp_data)) {
    if (empty($csv_tmp_data[1])) {
        ?>
<section class="slice color-three pb-4">
    <div class="w-section inverse p-0">
        <div class="row">
            <div class="col-md-12">
                <form id="import_form" name="import_form"
                    action="<?=base_url()?>ratedeck/ratedeck_import_file/<?=$check_header;?>/"
                    method="POST">
                    <div class="card p-4 table-responsive">
                        <table width="100%" border="1"
                            class="table table-bordered details_table table">
                                        <?php

$cnt = 0;
        foreach ($csv_tmp_data as $csv_key => $csv_value) {
            if ($csv_key < 15) {
                echo "<tr>";
                foreach ($csv_value as $field_name => $field_val) {
                    if ($csv_key == 0) {
                        $cnt ++;
                        echo "<th>" . ucfirst($field_name) . "</th>";
                    } else {
                        echo "<td>" . $field_val . "</td>";
                    }
                }
                echo "</tr>";
            }
        }
        echo "<tr><td colspan='" . $cnt . "' style='color: red;text-align: center;'>No Records found</td></tr>";
        echo "<tr><td colspan='" . $cnt . "'>
                                                <a href='" . base_url() . "ratedeck/ratedeck_import/'><input type='button' class='btn btn-line-sky pull-right  margin-x-10'  value='Back'/></a>";
        ?> </table>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<?php }else{?>
<section class="slice color-three pb-4">
    <div class="w-section inverse p-0">
        <div class="row">
            <div class="col-md-12">
                <form id="import_form" name="import_form"
                    action="<?=base_url()?>ratedeck/ratedeck_import_file/<?=$check_header;?>/"
                    method="POST">
                    <div class="card p-4 table-responsive">
                        <table width="100%" border="1"
                            class="table-bordered details_table table">
                                            <?php

$cnt = 0;
        foreach ($csv_tmp_data as $csv_key => $csv_value) {
            if ($csv_key < 15) {
                echo "<tr>";
                foreach ($csv_value as $field_name => $field_val) {
                    if ($csv_key == 0) {
                        $cnt ++;
                        echo "<th>" . ucfirst($field_name) . "</th>";
                    } else {
                        echo "<td>" . $field_val . "</td>";
                    }
                }
                echo "</tr>";
            }
        }

        echo "<tr><td colspan='" . $cnt . "'>";
        ?>
                                        <button type="button"
                                class="btn btn-secondary ml-2 float-right" value="Back"
                                onclick="return redirect_page('ratedeck/ratedeck_import/')"><?php echo gettext('Back'); ?></button>
                            <button type="submit" class="btn btn-success float-right"
                                id="Process" ><?php echo gettext('Process'); ?></button>

                                    <?php echo "</td></tr>";?> 
                                        </table>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<?php } ?>
    <?php } ?>
<script>
    $('input[name="ratedeckimport"]').change(function(e){
        var fileName = e.target.files[0].name;
        $('label[name="lf_ratedeckimport"]').html(fileName);
        $("#welcomeDiv").removeClass('d-none');
    });

    $('input[name="ratedeckimport_"]').change(function(e){
        var fileName = e.target.files[0].name;
        $('label[name="lf_ratedeckimport_"]').html(fileName);
        $('#import_ratedeck_').prop('disabled', false);
    });

    $("#welcomeDiv button").on("click",function(){
        $('label[name="lf_ratedeckimport"]').text("");
        document.getElementById("ratedeckimport").value = null;
        $("#welcomeDiv").addClass('d-none');
    });

    $('#import_ratedeck_').click(function(){
        var ufile = $('#ratedeckimport_')[0].files[0];

        if (ufile) {
            var fd = new FormData();
            fd.append('file', ufile);

            $.ajax({
                type: 'post',
                url:'<?=base_url()?>ratedeck/ratedeck_import_zones',
                data:fd,
                contentType: false,
                processData: false,
                success: function(r){
                    r = JSON.parse(r);
                    if (r.hasOwnProperty('code') && parseInt(r.code)===0){
                        show_toast('ok',"<?=gettext('Records imported successfully')?>");
                    } else {
                        show_toast('error',"<?=gettext('Records not imported')?>");
                        console.log(r.detail);
                    }
                },
            });
        }

        return false;
    });

    function show_toast(toast_type, toast_message) {
        if (toast_type == 'error'){
            $("#toast-container_error").hide();
            $("#toast-container_error").css("display","block").delay(5000).fadeOut();
        }
        if (toast_type == 'ok'){
            $("#toast-container").hide();
            $("#toast-container").css("display","block").delay(5000).fadeOut();
        }

        $(".toast-message").html(toast_message);
    }

</script>
<? endblock() ?>    
<? end_extend() ?> 



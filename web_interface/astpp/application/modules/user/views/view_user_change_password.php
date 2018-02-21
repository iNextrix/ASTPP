<? extend('master.php') ?>
<?php error_reporting(E_ERROR); ?>
<? startblock('extra_head') ?>			
<script type="text/javascript">
    $().ready(function() {
        $("#change_pass").validate({
            rules: {
                old_pass: {
                    required: true,
                    minlength: 6
                },
                new_pass: {
                    required: true,
                    minlength: 6
                }

            },
            messages:
                {
                old_pass:'Old Password is required',
                new_pass:'New Password is required'
            }
        });
    });
</script>
<? endblock() ?>

<? startblock('page-title') ?>
<?= $page_title ?><br/>
<? endblock() ?>

<? startblock('content') ?>

<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
    <div class="portlet-header ui-widget-header">Change Password<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
    <div class="portlet-content">
        <form action="<?= base_url() ?>user/user_change_password" id="change_pass" method="POST" enctype="multipart/form-data">
            <ul style="width:600px; list-style:none;">
                <li>   
                    <label class="desc">Old Password:</label>     
                    <input size="20" type="password" name="old_pass" id="old_pass">
                </li>
                <li>   
                    <label class="desc">New Password:</label>     
                    <input type="password" size="20"  field medium" name="new_pass" id="new_pass">
                </li>
            </ul>
            <input type="submit" class="ui-state-default float-right ui-corner-all ui-button" name="action" value="Change Password" />
            <br><br>
            <hr>
        </form>
    </div>
</div>
<? endblock() ?>

<? startblock('sidebar') ?>
Filter by
<? endblock() ?>

<? end_extend() ?>  

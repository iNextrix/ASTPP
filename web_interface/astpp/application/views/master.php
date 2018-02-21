<? include('header.php'); ?>
<br/>
<?php if (isset($astpp_debugmsg)) { ?>
    <div class="response-msg alert" style="width: 90%">
        <br/>
        <?= $astpp_debugmsg ?>
        <br/>
    </div>	
<?php } ?>			
<div id="body_content">
    <div id="sub-nav">
        <div class="page-title">
            <h1>
                <? start_block_marker('page-title') ?>Dashboard<? end_block_marker() ?>			
            </h1>
        </div>
    </div>

    <div id="page-layout">
        <div id="page-content" >
            <div id="page-content-wrapper" <?php if (!isset($astpp_sidebar)) { ?>style="padding-right:10px" <?php } else { ?>style="padding-right:100px"<?php } ?>>					
                <?php
                $astpp_errormsg = $this->session->flashdata('astpp_errormsg');
                if ($astpp_errormsg) {
                    ?>
                    <div class="response-msg error" style="text-align: center;">
                        <strong>Alert : </strong>  <?= $astpp_errormsg ?>
                    </div>	
                    <?php
                    $this->session->set_userdata('astpp_errormsg', '');
                }
                ?>            				
                <?php
                $astpp_notification = $this->session->flashdata('astpp_notification');
                if ($astpp_notification) {
                    ?>
                    <div class="response-msg" style="text-align: center;">
                        <b><?= $astpp_notification ?></b>
                    </div>	
            <?php $this->session->set_userdata('astpp_notification', ''); 
    }
?>            				
                <? start_block_marker('content') ?><? end_block_marker() ?>

                <?php if (isset($astpp_sidebar)) { ?>					
                    <div id="sidebar">
                        <div class="sidebar-content"  style="border:#e5e1d8 solid 5px;">
    <? start_block_marker('sidebar') ?><? end_block_marker() ?>
                        </div>
                    </div>
<?php } ?>					
                <div class="clear"></div>	
            </div>	
        </div>		
    </div>		
<?php include('footer.php'); ?>
</div>
</div>
</body>
</html>

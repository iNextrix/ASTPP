<? extend('left_panel_master.php') ?>
<?php error_reporting(E_ERROR); ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
    function showdiv(key1,key2) {
        document.getElementById(key1).style.display = "none";
        document.getElementById(key2).style.display = "block";
    }
    function save_speed_dial(speed_num){
        var accountid= "<?= $account_data['id'] ?>";
        var speed_dial="speed_dial_"+speed_num;
        var speeddial_number =document.getElementById(speed_dial).value;
        if (!/^[0-9]+$/.test(speeddial_number)) {
            $('#error_'+speed_dial).text( "Please enter only numeric value" );
            document.getElementById(speed_dial).focus();
            return false;
        }
        $.ajax({
            type: "POST",
            url: "<?= base_url() ?>/user/user_speeddial_save/",
            data:{destination:speeddial_number,number:speed_num},
            success:function(response) {
                location.reload(true);
            }
        }); 
    }
    function remove_save_speed_dial(speed_num){
        var accountid= "<?= $account_data['id'] ?>";
        $.ajax({
            type: "POST",
            url: "<?= base_url() ?>/user/user_speeddial_remove/",
            data:{number:speed_num},
            success:function() {
                location.reload(true);
            }
        }); 
    }

</script>
<style>
    label.error {
        float: left; color: red;
        padding-left: .3em; vertical-align: top;  
        padding-left:40px;
        margin-top:20px;
        width:1500% !important;
    }
    .form-control
    {
        height:33px;
    }
</style>
<?php endblock() ?>
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
                        <li><a href="#">Configuration</a></li>
                       <li class="active">
                            <a href="<?= base_url()."user/user_speeddial/"; ?>"> Speed Dial </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="padding-15 col-md-12">
                <div class="slice color-three pull-left content_border">
                    <div id='speed_dial'>
                        <div class="col-md-12 color-three padding-b-20 padding-t-20">
                            <form method="post" name="myform_speed" id="myform_speed" action="#" enctype="multipart/form-data">
                               <div class="col-md-12">
                                <div class="col-md-1 no-padding">Speed Dial<br/>(#Digits)</div> 
                                <div class="col-md-3">Extension</div>
                                <div class="col-md-4">Action</div>
                                 </div>
                                <?php
								$res = $this->db_model->getSelect("*", "speed_dial", array("accountid" => $account_data[0]['id']));
								if ($res->num_rows() > 0) {
									$result = $res->result_array();
								} else {
									$result = 0;
								}
								for ($i = 0; $i <= 9; $i++) {
									?>
                                    <div class="col-md-12">
                                        <div id="key<?php echo $i; ?><?php echo $i + 1; ?>" style="display:block;">
                                            <div class="col-md-1">
                                                <label class="col-md-2">
                                                    <?php echo $i; ?>
                                                </label>
                                            </div>   
                                            <div class="col-md-3">
                                                <label class="col-md-2" name="speed_dial" size="16"> 
                                                    <?php if ($result[$i]['speed_num'] == $i) {
														echo $result[$i]['number'];
													} ?> 
                                                </label>
                                            </div>
                                            <div class="col-md-4 margin-b-10">
                                                <div class="col-md-2 no-padding">
                                                <a  class="btn btn-warning" onclick="showdiv('key<?php echo $i; ?><?php echo $i + 1; ?>','key<?php echo $i; ?><?php echo $i + 2; ?>')"  title="Edit">Edit</a>
                                                </div>
                                                <div class="col-md-2 no-padding">
                                                <a  class="btn btn-line-sky margin-x-10" onclick="remove_save_speed_dial('<?php echo $i; ?>')" title="Delete" name="click0<?php echo $i; ?>" id="click0<?php echo $i; ?>">Delete</a>
                                                </div>
                                            </div>
                                        </div>
                                        
                                          <div id="key<?php echo $i; ?><?php echo $i + 2; ?>" style="display:none;"> 
                                            <div class="col-md-1">
                                            <label class="col-md-2">
                                                
    <?php echo $i; ?>
                                            </label>
                                            </div>
                                               <div class="col-md-3">
                                            <input class="col-md-2 form-control" name="speed_dial_<?php echo $i; ?>" id="speed_dial_<?php echo $i; ?>" size="16" type="text"  value="<?php if (isset($speeddial[$i]) && !empty($speeddial[$i])) {
		echo $speeddial[$i];
	} ?>">
                                               </div>
                                            <div class="col-md-4">
                                          <div class="col-md-2 no-padding">
                                                <a onclick="save_speed_dial('<?php echo $i; ?>')" class="btn btn-line-parrot" title="Save" name="click<?php echo $i; ?>" id="click<?php echo $i; ?>">Save</a>
                                          </div>
                                                <div class="col-md-2 no-padding">
                                                <a onclick="remove_save_speed_dial('<?php echo $i; ?>')" class="btn btn-line-sky margin-x-10" title="Delete" name="click0<?php echo $i; ?>" id="click0<?php echo $i; ?>">Delete</a>
                                                </div>
                                           
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <span style="color:red;float:left;margin-top:-10px; margin-left:115px;" id="error_speed_dial_<?php echo $i; ?>"></span>  
                                    </div>
<?php } ?>
                            </form>
                        </div>                
                    </div>
                </div>
            </div>  
        </div>
    </div> 
</div>
<? endblock() ?>	
<? startblock('sidebar') ?>
Filter by
<? endblock() ?>
<? end_extend() ?>  

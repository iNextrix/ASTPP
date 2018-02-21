<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
    
<? //echo "<pre>"; print_r($this->session->userdata); exit;                 ?>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <title>ASTPP - Open Source Voip Billing Solution</title>
    <script language="javascript" type="text/javascript">
	    var base_url = '<?php echo base_url();?>';
    </script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery-1.7.1.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/flexigrid.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/ui/ui.core.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/ui/ui.datepicker.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/ui/ui.widget.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/ui/ui.mouse.js"></script>
    <script type="text/javascript" src="<?php echo base_url()?>assets/js/ui/ui.tabs.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/superfish.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/live_search.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/tooltip.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/cookie.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/ui/ui.sortable.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/ui/ui.draggable.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/ui/ui.resizable.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/ui/ui.position.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/ui/ui.button.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/ui/ui.dialog.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/custom.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/fg.menu.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/facebox.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/validate.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/ui/ui-1.8.16.custom.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/ui/ui-timepicker-addon.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/ui/ui-sliderAccess.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/popup_search.js"></script>   
    
  
    <link rel="stylesheet" media="all" type="text/css" href="<?php echo base_url();?>assets/css/ui/ui-1.8.16.custom.css" />		    
    <link rel="stylesheet" href="<?php echo base_url();?>assets/css/flexigrid.css" type="text/css">
    <link href="<?php echo base_url();?>assets/css/ui/ui.base.css" rel="stylesheet" media="all" />
    <link href="<?php echo base_url();?>assets/css/themes/apple_pie/ui.css" rel="stylesheet" title="style" media="all" />
    <link href="<?php echo base_url();?>assets/css/themes/apple_pie/ui.css" rel="stylesheet" media="all" />
    <link href="<?php echo base_url();?>assets/css/ui/ui.datepicker.css" rel="stylesheet" media="all" />
    <link href="<?php echo base_url();?>assets/css/facebox.css" rel="stylesheet" media="all" />	
    <link href="<?php echo base_url();?>assets/css/fg.menu.css" rel="stylesheet" media="all" />	
    <link href="<?php echo base_url();?>assets/css/ui/ui.forms.css" rel="stylesheet" media="all" />
    <link rel="stylesheet" href="<?php echo base_url();?>assets/css/astppbilling.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo base_url();?>assets/css/menu.css" type="text/css" media="screen" />    
    
    
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/module_js/generate_grid.js"></script>
    
    <? start_block_marker('extra_head') ?>
    <? end_block_marker() ?>	
</head>
<body>
    
    
    <?//echo "<pre>";print_r(common_model::$global_config['system_config']['opensips']);exit;?>
    
<? $logged_user = ($this->session->userdata('userlevel_logintype')=='-1')?'Admin':$this->session->userdata('user_name');?>    
<div id="page_wrapper">
<div id="page-header">
    <div id="page-header-wrapper" style="height: 140px;">    
        <div id="top" style="height: 78px;" onmouseover="hide_sub()">
        <a href="<?=base_url()?>/dashboard" class="logo" title="ASTPP" >ASTPP</a>
            <div class="welcome">
                    <span class="note" style="color: black;">Welcome <a href="#" title="Welcome  <?=$logged_user?>"><?=ucfirst($logged_user)?></a></span>						
                    <a class="btn ui-state-default ui-corner-all" href="<?php echo base_url();?>login/logout" style="background-color: transparent;">
                    <span class="ui-icon ui-icon-power"></span>Logout</a>						

                    <? //if($this->session->userdata('userlevel_logintype')=='0') {?>    
                        <!--<a href="<? //= base_url()?>user/user_change_password/">Change Password</a>-->
                    <? //}?>
            </div>
        

        <div  style="height: 34px; left: 0;   padding-top: 10px;   position: absolute;   top: 38px; width: 100%;">
            <ul id="menu">
                <? if($this->session->userdata('userlevel_logintype') != '0'){?>
                    <li style="-moz-border-radius: 5px 5px 5px 5px;-webkit-border-radius: 5px 5px 5px 5px;border-radius: 5px 5px 5px 5px;"><a href="<?=base_url()?>dashboard/"><img src="<?=base_url()?>assets/images/menu_icons/Home.png" border="0" width="16" height="16"  />&nbsp;&nbsp;Home</a></li>
                <?} else{?>
                    <li style="-moz-border-radius: 5px 5px 5px 5px;-webkit-border-radius: 5px 5px 5px 5px;border-radius: 5px 5px 5px 5px;"><a href="<?=base_url()?>user/user/"><img src="<?=base_url()?>assets/images/menu_icons/Home.png" border="0" width="16" height="16"  />&nbsp;&nbsp;Home</a></li>    
                <?}?>
            <?php 
            $menu_info = unserialize($this->session->userdata("menuinfo"));
            foreach($menu_info as $menu_key => $menu_values){?>
                <li><a href="#" class="drop_menu"><?=$menu_key;?></a>
                <? if(count($menu_values) >1) {?> <div class="dropdown_3columns"> <? } else {?><div class="dropdown_2columns"> <?}?>
                    <? foreach($menu_values as $sub_menu_key => $sub_menu_values){
                        if(count($menu_values) > 3) {?><div class="custom_col"><?} else{?><div class="col_1"><?}?>
                            <? if(common_model::$global_config['system_config']['opensips']==0 && $sub_menu_key !='Opensips'){ ?>    
                            <h3><?=$sub_menu_key;?></h3>
                            <?} if(common_model::$global_config['system_config']['opensips']==1) {?>
                                <h3><?=$sub_menu_key;?></h3>
                            <? }?>
                            <ul class="simple">
                                <? foreach($sub_menu_values as $sub_menu_lables){ 
                                    if(common_model::$global_config['system_config']['opensips']==1 &&  $sub_menu_lables["menu_label"] !='Freeswitch SIP Devices'){
                                    ?>
                                    <li><a href="<?php echo base_url().$sub_menu_lables["module_url"];?>"><img src="<?=base_url()?>assets/images/menu_icons/<?=$sub_menu_lables['menu_image'];?>" border="0" width="16" height="16" />&nbsp;&nbsp;<?=$sub_menu_lables["menu_label"];?></a></li>
                                 <? }
                                 if(common_model::$global_config['system_config']['opensips']==0 && $sub_menu_key !='Opensips'){ ?>
                                     <li><a href="<?php echo base_url().$sub_menu_lables["module_url"];?>"><img src="<?=base_url()?>assets/images/menu_icons/<?=$sub_menu_lables['menu_image'];?>" border="0" width="16" height="16" />&nbsp;&nbsp;<?=$sub_menu_lables["menu_label"];?></a></li>
                                 <? } }?>
                            </ul>   
                        </div>
                        <?} ?> 
                </div></li> 
            <? } ?>
            </ul>
        </div>
        </div>
    </div>
</div>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">    <title>
	ASTPP - Open Source Voip Billing Solution</title>
  <link rel="icon" href="<? echo base_url(); ?>assets/images/favicon.ico">
    <script language="javascript" type="text/javascript">
	    var base_url = '<?php echo base_url();?>';
    </script>

    <link href="<?= base_url() ?>assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url() ?>assets/fonts/font-awesome-4.2.0/css/font-awesome.css" rel="stylesheet">
    <link href="<?= base_url() ?>assets/css/global-style.css" rel="stylesheet" type="text/css">
    <link href="<?php echo base_url();?>assets/css/facebox.css" rel="stylesheet" media="all" />	
    <link rel="stylesheet" href="<?php echo base_url();?>assets/css/flexigrid.css" type="text/css">


    <script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery-1.7.1.js"></script>

    <link href="<?php echo base_url(); ?>/assets/css/jquery.datetimepicker.css" rel="stylesheet" />
    <script src="<?php echo base_url(); ?>/assets/js/date-time/jquery.datetimepicker.js"></script>

    <link rel="stylesheet" media="all" type="text/css" href="<?php echo base_url(); ?>/assets/css/tabcontent.css" />
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/tabcontent.js"></script>

     <!-- IE -->
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/respond.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/respond.src.js"></script>
    <!-- -->    
     <script type="text/javascript" src="<?php echo base_url();?>assets/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/facebox.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>assets/js/flexigrid.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/module_js/generate_grid.js"></script>
    <noscript>
      <div id="noscript-warning">
	ASTPP work best with JavaScript enabled
      </div>
    </noscript>

<!--<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
    <link href="<?php echo base_url(); ?>/assets/Notifier-Plugin/css/notifier.css" rel="stylesheet" />
    <script src="<?php echo base_url(); ?>/assets/Notifier-Plugin/js/notifier.js"></script>
-->    
    <? start_block_marker('extra_head') ?>
    <? end_block_marker() ?>	

<script>

function PopupCenter(url, title, w, h) {
    // Fixes dual-screen position                         Most browsers      Firefox
    var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
    var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;

    width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
    height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;
top = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

    var left = ((width / 2) - (w / 2)) + dualScreenLeft;
    var top = ((height / 2) - (h / 2)) + dualScreenTop;
    var newWindow = window.open(url, title, ' width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);

    // Puts focus on the newWindow
    if (window.focus) {
        newWindow.focus();
    }
}
</script>    
</head>
<body>
<? 
/*if($this->session->userdata('userlevel_logintype')=='-1')
{
    $logged_user=$acc_info['first_name']." ".$acc_info['last_name'];
}
else
{*/
    $acc_info=$this->session->userdata('accountinfo');
    $logged_user=$acc_info['first_name']." ".$acc_info['last_name'];
//}
?>     
<header>
   
    
    <div class="container">
   
    	<div class="navbar-header pull-left">	
		  <div class="navbar-header">
	      <? if($this->session->userdata('userlevel_logintype') != '0'){?>
			<a class="navbar-brand padding-15" href="<?php echo base_url();?>dashboard/">
                	<img id="logo" alt="dashboard" src="<?php echo base_url();?>assets/images/logo.png">
	      <? } else{?> 
                	<a class="navbar-brand padding-15" href="<?php echo base_url();?>user/user/">
                	<img id="logo" alt="user_logo" src="<?php echo base_url();?>assets/images/logo.png">
		<? }?>
                </a>
          </div>		
		</div>
        <div class="navbar-header pull-right">	
		
       	   <ul class="navbar-profile">
         	 <li>
         	 <? if($this->session->userdata('userlevel_logintype') != '0'){?>
		      <a href="<?php echo base_url();?>dashboard/" class="padding-15">
                <? } else{?>    
		    <a href="<?php echo base_url();?>user/user/" class="padding-15">	
		<? }
                if($this->session->userdata('logintype')!=2){
	                $this->db->select('credit_limit,balance,posttoexternal');
        	        $this->db->where('id',$acc_info['id']);
        	        $result=$this->db->get('accounts');
	        if($result->num_rows() > 0){
  	            $result=$result->result_array();
		    $variable =$result[0]['posttoexternal']==1 ? 'Credit' : 'Balance';  
		    $amount=$result[0]['posttoexternal']==1 ? $result[0]['credit_limit'] :$result[0]['balance'];
		  }
	        $value= $this->common_model->calculate_currency($amount,'','',true);
	        if($value >0){
	        $color='#397A13';
	        }
	        if($value < 0){
		  $color='#EE0E43';
	        }
	        if($value == 0){
		  $color='#1A1919';
	        }
                $balance_str = '<span style="color:'.$color.'"><center><b>('.$variable.' : '.$value.')</b></center></span>';
            }else{
                $balance_str = '';
            }		
		
		?>
                	<!--<img id="logo" alt="user" src="<?php echo base_url();?>assets/images/user.png">-->
               
                <span><strong>Welcome <?= $logged_user?></strong></span>
                  <?php echo $balance_str; ?>              
                 </a>
                <ul class="dropdown-box">
		    <? if($this->session->userdata('userlevel_logintype') != '0' && $this->session->userdata('userlevel_logintype') != '3'){?>
		      <li><a href="<?php echo base_url();?>dashboard/"><i class="fa fa-home"></i> &nbsp;Dashboard</a></li>
		    <? } else{?>    
		      <li><a href="<?php echo base_url();?>user/user/"><i class="fa fa-home"></i> &nbsp;Dashboard</a></li>

		      <li><a href="<?php echo base_url();?>user/user_edit_account/"><i class= "fa fa-user"></i> &nbsp;Profile</a></li>
		    <? }?><!--
		    <? if($this->session->userdata('userlevel_logintype') == '-1'){?>
		    <li><a href="<?php echo base_url();?>systems/configuration/"><i class="fa fa-cog"></i> &nbsp;Settings</a></li>
		      <?}?>-->

<? if($this->session->userdata('userlevel_logintype') == '1'){?>
		    <li><a href="<?php echo base_url();?>accounts/reseller_edit_account/"><i class= "fa fa-user"></i> &nbsp;Profile</a></li>
		      <?}?>
		    
		    <? if($this->session->userdata('userlevel_logintype') == '-1'){?>
		      <li style="-moz-border-radius: 5px 5px 5px 5px;-webkit-border-radius: 5px 5px 5px 5px;border-radius: 5px 5px 5px 5px;"><a href="http://bugs.astpp.org/" target="_blank"><i class= "fa fa-bug"></i> &nbsp;Report a Bug</a></li>
		    <?}?>
		    <? if($this->session->userdata('userlevel_logintype') == '-1'){?>
		      <li style="-moz-border-radius: 5px 5px 5px 5px;-webkit-border-radius: 5px 5px 5px 5px;border-radius: 5px 5px 5px 5px; cursor:pointer">
		      <a onclick="PopupCenter('<?=base_url()?>feedback/',resizable=1,width=580,height=660) "><i class= " fa fa-envelope-o"></i> &nbsp;FeedBack</a></li>
		    <?}?>
                <li><a href="<?php echo base_url();?>logout"><i class="fa fa-power-off"></i> &nbsp;Log out</a></li>
                </ul>
               </li>
              </ul>   
         	
		</div>
        <!-- /.container -->
	</div><!-- /.navbar-header -->
		<!--
	 <div class="navbar navbar-white" style="background-color:#CC3300;min-height:30px;">     
        	<div class="container" style="color:white;padding-left:200px;padding-top:5px;">
            		
			 <div style="padding-top:50px;">
				<b>Donate to Campaigns:</b>
				<a href="http://www.astpp.org/campaigns/ip-management" target="_blank" style="text-decoration: none;color:white;cursor:pointer;margin-left:50px;"><b><u>IP Management</u></b></a>
				
				<a href="http://www.astpp.org/campaigns/did-improvement" target="_blank" style="text-decoration: none;color:white;cursor:pointer;margin-left:50px;margin-right:50px;"><b><u>DID Improvement</u></b></a>
				
				| 
				
		
			<b><a href="http://www.astpp.org/freeswitch-monitoring-addon" target="_blank" style="text-decoration: none;color:white;cursor:pointer;margin-left:50px">
				<u>Get Freeswitch Monitoring Addon</u></a></b>
	
			</div>
		</div>
	</div>-->



     <div id="navbar" class="navbar navbar-white" role="navigation" style="margin-top:5px;">     
        <div class="container">
<div class='col-md-10' style='float:left;'>         <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">                     <span class="sr-only">Toggle navigation</span>                     <span class="icon-bar"></span>                     <span class="icon-bar"></span>                     <span class="icon-bar"></span>         </button>         </div>
            <div class="navbar-collapse collapse no-padding">
                <ul class="nav navbar-nav">
                <? //if($this->session->userdata('userlevel_logintype') != '0'){?>
                    <!--<li><a href="<?//=base_url()?>dashboard/">Home</a></li>-->
                <? //} else{?>
                    <!--<li><a href="<?//=base_url()?>user/user/">Home</a></li>    -->
                <? //}?>
<?php 
            $menu_info = unserialize($this->session->userdata("menuinfo"));
//             echo "<pre>"; 
//               print_r($menu_info);exit;
            foreach($menu_info as $menu_key => $menu_values){?>
                
                <?php
		  if(common_model::$global_config['system_config']['opensips']== 1 &&  $menu_key !='SIP Devices'){
		      echo '<li><a href="#">'.$menu_key.'<b class="caret"></b></a>';
		  }
		  if(common_model::$global_config['system_config']['opensips']== 0 &&  $menu_key != 'Opensips'){
		      echo '<li><a href="#">'.$menu_key.'<b class="caret"></b></a>';  
		  }
                ?>
                
                         <ul class="dropdown-menu">
                    <? foreach($menu_values as $sub_menu_key => $sub_menu_values){?>
                       
                           <?  if($sub_menu_key === 0){ ?>
                            <? foreach($sub_menu_values as $sub_key => $sub_menu_lables){  
                                if(common_model::$global_config['system_config']['paypal_status']== 1 && strtolower($sub_menu_lables["menu_label"]) =='recharge'){ echo ''; ?>				
                    		<?  } elseif(common_model::$global_config['system_config']['opensips']== 0 &&  $sub_menu_lables["menu_label"] !='Opensips'){ ?>
                                     <li><a href="<?php echo base_url().$sub_menu_lables["module_url"];?>"><?=$sub_menu_lables["menu_label"];?></a></li>
                                     <?}
                                     elseif(common_model::$global_config['system_config']['opensips']== 1 &&  $sub_menu_lables["menu_label"] =='SIP Devices'){?>
                                     <? }else{ ?>
                                     <li><a href="<?php echo base_url().$sub_menu_lables["module_url"];?>"><?=$sub_menu_lables["menu_label"];?></a></li>
                             <?}?>
				<? } ?>
                            
                            <?php }else{
				if(common_model::$global_config['system_config']['opensips']==0 && $menu_key !='System Configuration'){ ?>    
				    <li><a href="#"><span><?=$sub_menu_key;?></span><i class="fa fa-caret-right pull-right"></i></a>
				<? } if(common_model::$global_config['system_config']['opensips']==1) {?>
				    <li><a href="#"><span><?=$sub_menu_key;?></span><i class="fa fa-caret-right pull-right"></i></a>
				<?php }                            
                            ?>
                                 <div class="col-4"><div class="col-md-6 no-padding"><ul class="col-12 padding-x-8">
                                <? foreach($sub_menu_values as $sub_menu_lables){ 
				     if($sub_menu_lables['menu_label'] != 'Configuration'){
				  if(common_model::$global_config['system_config']['opensips']==1 &&  $sub_menu_lables["menu_label"] !='SIP Devices'){
				      ?>
				      <li><a href="<?php echo base_url().$sub_menu_lables["module_url"];?>"><?=$sub_menu_lables["menu_label"];?></a></li>
				  <? }
				  if(common_model::$global_config['system_config']['opensips']== 0 && $sub_menu_key !='opensips'){ ?>
				      <li><a href="<?php echo base_url().$sub_menu_lables["module_url"];?>"><?=$sub_menu_lables["menu_label"];?></a></li>
				  <? } 
				} } ?>
				</li></ul></div></div>
                              
                        <?} }  ?> 
                  </ul>   
                </li> 
            <? } 
		//    if($this->session->userdata('userlevel_logintype')== 0 || $this->session->userdata('userlevel_logintype')== 3){
             //    echo '<li><a style = "height: 40px;margin: 5px 0px;padding-top: 10px;" href="'.base_url().'user/user_payment/">Recharge</a>';  
	//	echo '<li>&nbsp;</li>';
         //   }
      if($this->session->userdata('userlevel_logintype')== -1){
		     echo '<li><a class="btn-lightblue" style = "height: 40px;margin: 5px 0px;padding-top: 10px;" href="http://bugs.astpp.org" target = "_blank">Report A Bug</a>';  
     echo '<li>&nbsp;</li>';
     echo '<li><a class="btn-lightblue" style = "height: 40px;margin: 5px 0px;padding-top: 10px;" href="http://www.astpp.org/donate" target = "_blank">Donate Us</a>';
     }
     echo '<li>&nbsp;</li>';
	   
             ?>
            </ul>
            </div><!--/.nav-collapse -->
        </div>
    </div>
</header>

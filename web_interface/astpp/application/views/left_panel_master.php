<? include('header.php'); ?>
<section class="slice color-one">
 <div class="w-section inverse no-padding border_box">
   <div class="container">
     <div class="">
	  </div>
     </div>
    </div>
  </div>    
</section>
<section class="page-wrap">
<div id="toast-container" class="toast-top-right col-md-6" style="display:none;" >
 <div class="toast fa-check toast-success1">
        <button class="toast-close-button">
            <i class="fa fa-close"></i>
        </button>
        <div class="toast-message">
                    Success message
        </div>
  </div>
</div>

<div id="toast-container_error" class="toast-top-right col-md-6" style="display:none;z-index:999"> <!--  style="display:none;" -->
<div class="toast fa fa-times toast-danger1">
        <button class="toast-close-button">
            <i class="fa fa-close"></i>
        </button>
        <div class="toast-message">
                    Error message light
        </div>
  </div>
</div>
<?php
	$astpp_msg = false;
	$msg_type = "";
	$astpp_err_msg = $this->session->flashdata('astpp_errormsg');
	if ($astpp_err_msg) {
		$astpp_msg = $astpp_err_msg;
		$msg_type = "error";
	}
    
   $astpp_notify_msg = $this->session->flashdata('astpp_notification');
   if ($astpp_notify_msg) {  
		$astpp_msg = $astpp_notify_msg;
		$msg_type = "notification";
   }
   if ($astpp_msg) {
?>
<script> 
    var validate_ERR = '<?= $astpp_msg; ?>';
    var ERR_type = '<?= $msg_type; ?>';
    display_astpp_message(validate_ERR,ERR_type);
</script>
<?php } ?>
 <button type="button" class="navbar-toggle nav_sidetoggle collapsed white_bar" data-toggle="collapse" data-target=".sidebar">    
      	      <span class="sr-only">Toggle navigation</span>       
	      <span class="icon-bar"></span> 
	      <span class="icon-bar"></span> 
	      <span class="icon-bar"></span>
	</button> 
<div class="sidebar collapse">
	
             <?php 
			 $uri_string= uri_string();
			 $uri_arr=explode("/",$uri_string);
			 $entity_name=false;
			 if(isset($uri_arr[1]) && !empty($uri_arr[1])){
					$function_explode=explode("_",$uri_arr[1]);
				   $entity_name = isset($function_explode[1]) && !empty($function_explode[1]) ? $function_explode[0] : false;
			 }
             
			 $accountinfo=$this->session->userdata('accountinfo');
			 if($accountinfo['type'] != 0  && $accountinfo['type'] !=3){
			  $menu_info= ($uri_arr[0]=="user"
							? unserialize(RESELLERPROFILE_ARRAY):($uri_arr[0]=="accounts"&& $entity_name =='customer'
							? unserialize(CUSTOMEREDIT_ARRAY)   :($uri_arr[0]=="accounts"&& $entity_name =='provider'
							? unserialize(PROVIDEREDIT_ARRAY)   :($uri_arr[0]=="accounts"&& $entity_name =='reseller' 
							? unserialize(RESELLEREDIT_ARRAY) : ($uri_arr[0] =="package" ? unserialize(PACKAGEEDIT_ARRAY):false) ))));
			 }else{
			   $menu_info=null;
			   $current_menu_url=$uri_arr[0]."/".$uri_arr[1]."/";
			   $new_menu_info=array();
				$menus=  unserialize($this->session->userdata('menuinfo'));
				foreach($menus as $entity_key=>$entity_menu){
					foreach($entity_menu as $entity_subkey=>$entity_submenu){
						 foreach($entity_submenu as $subkey=>$submenus){
							 if($submenus['module_url']==$current_menu_url){
								 $new_menu_info=$entity_menu;
							 }
						 }
					}
				}
				foreach($new_menu_info as $key=>$value){
					foreach($value as $subvalue){
						$menu_info[$subvalue['menu_label']]=$subvalue['module_url'];
					}
				}
			 }
			 if($accountinfo['type']==0 || $accountinfo['type']==3 || $accountinfo['type']==4){
		  if($uri_arr[0]=='user' && $uri_arr[1] =='user_myprofile' || $uri_arr[0]=='user' && $uri_arr[1]=='user_change_password'){
		$menu_info=unserialize(CUSTOMERPROFILE_ARRAY);
		  }
			 }
			 if(!empty($menu_info)){
				echo "<ul class='sidemenu'>";
				$i=0;
				foreach($menu_info as $key=>$value){ 
				$url=($entity_name=='provider'||$entity_name =='customer' || $entity_name =='reseller' || $uri_arr[0] =="package") && isset($uri_arr[2]) && !empty($uri_arr[2])
					?
				base_url().$value.$uri_arr[2]."/" : 
				base_url().$value;
				$value_flag=false;
		if($acc_info['type'] == '3' || $acc_info['type'] == '0'){
		  if($value == "user/user_ipmap/" && $acc_info['allow_ip_management'] == '1'){
			$value_flag=false;
		  }elseif(in_array('user/user_sipdevices/',$menu_info) && $value == "user/user_sipdevices/" && common_model::$global_config['system_config']['opensips']== 0){
			$value_flag=false;
		  }else{
			$value_flag=true;
		  }
				}else{
		  if(common_model::$global_config['system_config']['opensips'] == 1 ){
			  if($value != "accounts/".$entity_name."_opensips/"){
			  $value_flag=true;
			  }else{
			$value_flag=false;
			  }
		  }
		  if(common_model::$global_config['system_config']['opensips']== 0 ){
			  if($value != "accounts/".$entity_name."_sipdevices/"){
			  $value_flag=true;
			  }else{
			$value_flag=false;
			  }
		  }
		  if(common_model::$global_config['system_config']['enterprise'] == 0 ){
			  if(common_model::$global_config['system_config']['opensips']== 0 && $value == "accounts/".$entity_name."_sipdevices/"){
						  $value_flag=true;	
			  }
			  if(common_model::$global_config['system_config']['opensips']== 0 && $value == "accounts/".$entity_name."_opensips/"){
						  $value_flag=false;	
			  }
		  }
				}

				if($value_flag){
					$class = ($value == $uri_arr[0]."/".$uri_arr[1]."/" ) ? 'active' : '';
					if($i==0)
						$class=($uri_arr[1]== $entity_name."_save") ? 'active': $class;
                    
					echo "<li class='$class'><a href ='$url'>".gettext($key)."</a></li>";
				}
				$i++;
				}
				echo "</ul>";
				}
			 ?>
</div>		
<? start_block_marker('content') ?><? end_block_marker() ?>	


<?php include('footer.php'); ?>


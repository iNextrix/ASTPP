<script src="<?php echo  base_url(); ?>assets/js/bootstrap-select.js"></script>
<script src="<?= base_url() ?>assets/js/maxcdn_bootstrap.min.js"></script>
</section>
</span></span>
<footer class="site-footer"> 
   <div class="padding-b-10 col-md-12 no-padding">
	
  <?php
    $this->db->select('*');
	$this->db->where('domain',$_SERVER['HTTP_HOST']);
	$result=$this->db->get('invoice_conf');
	if($result->num_rows() > 0){
		$result=$result->result_array();
		$footer = $result[0]['website_footer'];
	}else{
		$footer = '';
	}
  	if($footer != '' && ($this->session->userdata['logintype'] == 2 || $this->session->userdata['logintype'] ==-1)){ ?>
			 <div  class="pull-left col-md-3">
			<span><?php echo gettext("Powered by")?> </span>
			<a href="http://www.astppbilling.org" target="_blank">
				<span style="color: #216397;text-shadow: 0px 1px 1px #FFF;">
					<strong>ASTPP</strong>
				</span>
			</a>
			<div class="col-md-12 no-padding margin-t-10">
  	   		 <label class="pull-left" style="margin-top:3px;"><i> <?php echo gettext('Follow us on:')?> </i></label>
  	   		 <div class="social-media">
  	   		  <a target="_blank" href="https://www.facebook.com/astppbilling" title="Facebook"> <i class="facebook fa fa-facebook"></i></a>
  	   		  <a target="_blank" href="https://in.linkedin.com/in/astpp-opensource-voip-billing-bb9301b5" title="Linkedin"> <i class="linkin fa fa-linkedin"></i></a>
  	   		  <a target="_blank" href="https://twitter.com/astppbilling" title="Twitter"> <i class="twitter fa fa-twitter "></i></a>
  	   		 <!-- <a target="_blank" href="https://www.pinterest.com/astpp/" title="Pinterest"> <i class="pinterest fa fa-pinterest-p"></i></a>-->
  	   		</div>
		 </div>
	    </div>

	<span class="pull-right padding-r-20 padding-l-16"> <?php echo gettext('Version')?>  <?php echo common_model::$global_config['system_config']['version']; ?>  &nbsp; 
	<!--<a class="btn donate_btn" href="http://www.astppbilling.org/donate" target="_blank" style="background: #7bb935;"><i class="fa fa-dollar"></i>&nbsp;Donate Us</a><div style="margin-top: 7px;"></div>	-->
	<!--<i class="pi-header-block-icon icon-mail pi-icon-base pi-icon-square"></i>
	
			<a href="mailto:sales@inextrix.com?subject=Feedback :&body=ASTPP" style=" margin-left: 7%; font-weight: bold; color: #216397;text-shadow: 0px 1px 1px #FFF;">FEEDBACK</a>-->
	</span>
	<?php	} else  if($this->session->userdata['logintype'] ==0 || $this->session->userdata['logintype'] == 1){
		
		$user_footer = $this->session->userdata('user_footer');	
		if ($user_footer != '') { 
		
			?>
		 <div class="col-md-offset-4 col-md-4"><?=$user_footer ?> </div>
		<?} else {  ?>
	    <div class="col-md-offset-4 col-md-4">Copyright @ <?php echo date("Y"); ?> <a style="color:#3989c0;" href="http://www.inextrix.com" target="_blank"> Inextrix Technologies Pvt. Ltd</a>. All Rights Reserved.
	    
	    </div>
	    
	<? } } else{ ?>
			 <div  class="pull-left col-md-3">
			<span><?php echo gettext('Powered by')?> </span>
			<a href="http://www.astppbilling.org" target="_blank">
				<span style="color: #216397;text-shadow: 0px 1px 1px #FFF;">
					<strong>ASTPP</strong>
				</span>
			</a>
			<div class="col-md-12 no-padding margin-t-10">
  	   		 <label class="pull-left" style="margin-top:3px;"><i> <?php echo gettext('Follow us on:')?> </i></label>
  	   		 <div class="social-media">
  	   		  <a target="_blank" href="https://www.facebook.com/astppbilling" title="Facebook"> <i class="facebook fa fa-facebook"></i></a>
  	   		  <a target="_blank" href="https://in.linkedin.com/in/astpp-opensource-voip-billing-bb9301b5" title="Linkedin"> <i class="linkin fa fa-linkedin"></i></a>
  	   		  <a target="_blank" href="https://twitter.com/astppbilling" title="Twitter"> <i class="twitter fa fa-twitter "></i></a>
  	   		<!--  <a target="_blank" href="https://www.pinterest.com/astpp/" title="Pinterest"> <i class="pinterest fa fa-pinterest-p"></i></a>-->
  	   		</div>
		 </div>
	    </div>

	<span class="pull-right padding-r-20 padding-l-16"> Version  <?php echo common_model::$global_config['system_config']['version']; ?>  &nbsp; 
	<!--<a class="btn donate_btn" href="http://www.astppbilling.org/donate" target="_blank" style="background: #7bb935;"><i class="fa fa-dollar"></i>&nbsp;Donate Us</a><div style="margin-top: 7px;"></div>	-->
	<!--<i class="pi-header-block-icon icon-mail pi-icon-base pi-icon-square"></i>
	
			<a href="mailto:sales@inextrix.com?subject=Feedback :&body=ASTPP" style=" margin-left: 7%; font-weight: bold; color: #216397;text-shadow: 0px 1px 1px #FFF;">FEEDBACK</a>-->
	</span>
		
		<?php } ?>
	
	 <div style="" class="pull-right">
             <button style="" title="English" class="btn no-padding" id="close-image" type="button" name="en_EN" value="en_EN" onclick="get_lang('en_EN')" ;=""><img style="width: 20px; height: 18px;vertical-align:top;" src="<?php echo  base_url(); ?>assets/images/flags/flag_usa.png"></button>
            <button class="btn no-padding" title="Español" id="close-image" type="button" name="es_ES" value="es_ES" onclick="get_lang('es_ES')" ;=""><img style="width: 20px; height: 18px;vertical-align:top;" src="<?php echo  base_url(); ?>assets/images/flags/spain_flag.gif"></button>
            <button class="btn no-padding" title="Français" id="close-image" type="button" name="fr_FR" value="fr_FR" onclick="get_lang('fr_FR')" ;=""><img style="width: 20px; height: 18px;vertical-align:top;" src="<?php echo  base_url(); ?>assets/images/flags/france.png"></button> 
            <button class="btn no-padding" title="Português" id="close-image" type="button" name="pt_BR" value="pt_BR" onclick="get_lang('pt_BR')" ;=""><img style="width: 20px; height: 18px;vertical-align:top;" src="<?php echo  base_url(); ?>assets/images/flags/brazil.png"></button>
        </div>
	
	
   </div>
</footer>   
</body>
</html>
 

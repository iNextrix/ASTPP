<script type="text/javascript" src="<?php echo base_url();?>assets/js/bootstrap.min.js"></script>
</section>
<div class="col-md-12 no-padding"><hr style="border-style:solid none none; border-width:1px 0 0;"></div>
<footer class="site-footer"> 
   <div class="padding-b-10">
  <?php
	if($this->session->userdata['logintype'] == 2){ ?>
	    <div  class="pull-left padding-l-20"><span style="color:#4C4C4C;">Powered by </span><a href="http://www.astppbilling.org" target="_blank"><span style="color: #216397;text-shadow: 0px 1px 1px #FFF;"><strong>ASTPP</strong></span></a>
	     
	    </div>
	    <span class="pull-right padding-r-20"> Version  <?php echo common_model::$global_config['system_config']['version']; ?></span>
	<?php }else{ ?>
	    <div style="margin-left:470px; ">Copyright @ <?php echo date("Y"); ?> <a style="color:#3989c0;" href="http://www.inextrix.com" target="_blank"> Inextrix Technologies Pvt. Ltd</a>. All Rights Reserved.
	    
	    </div>
	<? } ?>
    
   </div>
</footer>
   
</body>
</html>

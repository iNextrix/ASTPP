<? extend('master.php') ?>
<? startblock('extra_head') ?>


<? endblock() ?>

<? startblock('page-title') ?>
<?= $page_title ?>
<? endblock() ?>

<? startblock('content') ?>   
<section class="slice gray no-margin">
 <div class="w-section inverse no-padding">
   <div>
     <div>
        <div class="col-md-12 no-padding margin-t-15 padding-l-16 margin-b-10">
	        <div class="col-md-10"><b></b></div>
	  </div>
     </div>
    </div>
  </div>    
</section>

<section class="slice color-three padding-b-20">
	<div class="w-section inverse no-padding">
    	<div class="container">
        	<div class="row">
                <div class="col-md-12"> 
			<form role="form" class="form-light padding-l-32 padding-r-32"  action="<?php echo base_url(); ?>user/user/change_password/" method="POST">
                        
            	<div class="portlet-content"  id="search_bar" style="cursor:pointer; display:none">
                    	<?php echo $form_search; ?>
    	        </div>    
			<div class="col-md-5 " style="text-align:right;">
                                	<h4>
			<?php echo gettext('Old Password') ?> :
		</h4></div><div class="col-md-5 "><input type="password" class="form-control" id="oldpassword" name="oldpassword" placeholder="Old password" style="height:40px;" >
                                </div>
                               <br/><br/> <br/><br/>
                                <div class="col-md-5 " style="text-align:right;">
                                	<h4>
			<?php echo gettext('New Password') ?>:		
		</h4>          </div> 
				<div class="col-md-5 " style="text-align:right;">
				<input type="password" class="form-control" id="newpassword" name="newpassword" placeholder="password" style="height:40px;">
                                </div>
 				 <br/><br/> <br/><br/>
                                <div class="col-md-5 " style="text-align:right;">
                                	<h4>
			<?php echo gettext('Conform Password')?> :		
			</h4>
			</div>
			<div class="col-md-5 " style="text-align:right;">             
				 <input type="password" class="form-control" id="conformpassword" name="conformpassword" placeholder="Conform Password" style="height:40px;">
                                </div>
                  
                                <br/><br/> <br/><br/>
                                    <div class="col-md-8 " style="text-align:right;">
                                        <button type="submit" class="btn btn-line-parrot pull-right"><?php echo gettext('Change Password') ?></button>                      
                                    </div>
                              <br/><br/>
                            </form>
          </div>  
            </div>
        </div>
    </div>
</section>
<? endblock() ?>	

<? end_extend() ?>   

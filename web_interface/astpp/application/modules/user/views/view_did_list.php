<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        build_grid("did_grid","",<? echo $grid_fields; ?>,"");
    });
</script>

<? endblock() ?>

<? startblock('page-title') ?>
<?= $page_title ?><br/>
<? endblock() ?>

<? startblock('content') ?>   

<section class="slice color-three padding-b-20">
	<div class="w-section inverse no-padding">
    		<div class="container">
        		<div class="row">
                		<div class="col-md-12 color-three padding-t-10" style="padding-top:15px;"> <br/>
                			<form method="post" action="<?= base_url() ?>user/user_dids_action/add/" enctype="multipart/form-data">

    						<label class="col-md-2">Available DIDs : </label>
                    				<div style="width:500px;">
			      				<? echo $didlist; ?>
						</div>        

                    				<input class="margin-l-20 btn btn-success" name="action" value="Purchase DID" type="submit">
	        	
                			</form>
            			</div>
 				<div class="col-md-12 color-three padding-b-20">
                            		<table id="did_grid" align="left" style="display:none;"></table>
        
      				</div>  
            		</div>
        	</div>
    	</div>
</section>

                       
                
<? endblock() ?>	

<? end_extend() ?>  

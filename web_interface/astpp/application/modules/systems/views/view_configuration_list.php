<? extend('master.php') ?>
<? startblock('extra_head') ?>


<script type="text/javascript" language="javascript">
    $(document).ready(function() {
      
        build_grid("configuration_grid","",<? echo $grid_fields; ?>,<? echo $grid_buttons; ?>);
        
      $("#configuration_search_btn").click(function(){
            post_request_for_search("configuration_grid","","configuration_search");
        });        
        $("#id_reset").click(function(){
            clear_search_request("configuration_grid","");
        });
        
    });
</script>
	
<? endblock() ?>

<? startblock('page-title') ?>
    <?= $page_title ?>
<? endblock() ?>

<? startblock('content') ?>        


<section class="slice color-three">
	<div class="w-section inverse no-padding">
    	<div class="container">
   	    <div class="mb-4 col-12">
            	<div class="portlet-content"  id="search_bar" style="cursor:pointer; display:none">
                    	<?php echo $form_search; ?>
    	        </div>
            </div>
        </div>
    </div>
</section>

<section class="slice color-three">
	<div class="w-section inverse no-padding">
    	<div class="container">
        	<div class="row">
                <div class="col-md-12">      
                        <form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">
                            <table id="configuration_grid" align="left" style="display:none;"></table>
                        </form>
                </div>  
            </div>
        </div>
    </div>
</section>

  
<? endblock() ?>	

<? end_extend() ?>  

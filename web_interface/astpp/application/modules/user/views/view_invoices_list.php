<? extend('master.php') ?>
<? startblock('extra_head') ?>

<script type="text/javascript" language="javascript">
    $(document).ready(function() {
      
        build_grid("invoice_grid","",<? echo $grid_fields; ?>,"");
        $("#invoice_search_btn").click(function(){
            post_request_for_search("invoice_grid","","invoice_search");
        });        
        $("#id_reset").click(function(){
            clear_search_request("invoice_grid","");
        });
            jQuery("#date").datetimepicker({format:'Y-m-d'});	
        jQuery("#invoice_date").datetimepicker({format:'Y-m-d'});	
    });
</script>
	
<? endblock() ?>

<? startblock('page-title') ?>
    <?= $page_title ?><br/>
<? endblock() ?>

<? startblock('content') ?>        

<section class="slice color-three">
	<div class="w-section inverse no-padding">
    	<div class="container">
   	    <div class="row">
            	<div class="portlet-content"  id="search_bar" style="cursor:pointer; display:none">
                    	<?php echo $form_search; ?>
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
                        <form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">
                            <table id="invoice_grid" align="left" style="display:none;"></table>
                        </form>
                </div>  
            </div>
        </div>
    </div>
</section>

  
<? endblock() ?>	
<? end_extend() ?>  

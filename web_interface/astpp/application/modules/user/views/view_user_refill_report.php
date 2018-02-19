<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        build_grid("refillreport_grid","",<? echo $grid_fields ?>,"");
        $("#user_refill_report_search_btn").click(function(){
            post_request_for_search("refillreport_grid","","user_refill_report_search");
        });
        $("#id_reset").click(function(){
            clear_search_request("refillreport_grid","");
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
   	    <div class="row">
            	<div class="portlet-content"  id="search_bar" style="cursor:pointer; display:none">
                    	<?php echo $form_search; ?>
    	        </div>
            </div>
        </div>
    </div>
</section>
<section class="slice color-three padding">
    <div class="w-section inverse no-padding">
        <div class="container">
            <div class="row">
                <div class="col-md-12 color-three padding-b-20">
                    <table id="refillreport_grid" align="left" style="display:none;"></table>
                </div>  
            </div>
        </div>
    </div>
</section>

<? endblock() ?>	
<? end_extend() ?>  
 

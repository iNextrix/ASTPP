<? extend('master.php') ?>
<? startblock('extra_head') ?>

<script type="text/javascript" language="javascript">
    $(document).ready(function() {
      
        build_grid("invoices_grid","",<? echo $grid_fields; ?>,<? echo $grid_buttons; ?>);
        $('.checkall').click(function () {
            $('.chkRefNos').attr('checked', this.checked); //if you want to select/deselect checkboxes use this
        });
        $("#invoice_search_btn").click(function(){
            
            post_request_for_search("invoices_grid","","invoice_search");
        });        
        $("#id_reset").click(function(){
            clear_search_request("invoices_grid","");
        });
        
    });
</script>
<script>
       $(document).ready(function() {
       
   jQuery("#invoice_date").datetimepicker({format:'Y-m-d'});		
   jQuery("#date").datetimepicker({format:'Y-m-d'});
//         		customer_cdr_from_date
    });
</script>

<? // echo "<pre>"; print_r($grid_fields); exit;?>
	
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
                            <table id="invoices_grid" align="left" style="display:none;"></table>
                        </form>
                </div>  
            </div>
        </div>
    </div>
</section>

<? endblock() ?>	

<? end_extend() ?>  

<? extend('master.php') ?>
<? startblock('extra_head') ?>

<script type="text/javascript" language="javascript">
    $(document).ready(function() {
      
        build_grid("invoice_grid","",<? echo $grid_fields; ?>,<? echo $fs_grid_buttons; ?>);
        $("#inbound_search_btn").click(function(){
            post_request_for_search("invoice_grid","<?php echo base_url(); ?>rates/user_inboundrates_list_search/","inbound_search");
        });        
        $("#id_reset").click(function(){
            clear_search_request("invoice_grid","<?php echo base_url(); ?>rates/user_inboundrates_list_clearsearchfilter/");
        });
        
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

  <!--
<div style="float:right;"><strong>
        <a class="margin-l-20 btn btn-success" href="/user/user_rates_export_xls">Export CSV <img src="/assets/images/file_tree/xls.png" alt='XLS'/></a> 
        <a class="btn btn-line-parrot" href="/user/user_rates_export_pdf">Export PDF <img src="/assets/images/file_tree/pdf.png" alt='PDF'/></a></strong></div>
<br/><br/> --> 
<? endblock() ?>	
<? end_extend() ?>  

<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
      
        build_grid("payment_report_grid","",<? echo $grid_fields; ?>,"");
        
        $("#cusotmer_cdr_payment_search_btn").click(function(){

            post_request_for_search("payment_report_grid","<?php echo base_url(); ?>reports/customer_paymentreport_search/","cdr_payment_search");
        });        
        $("#id_reset").click(function(){
            clear_search_request("payment_report_grid","<?php echo base_url(); ?>reports/customer_paymentreport_clearsearchfilter/");
        });
        
        
        
    });
</script>
<script>
    $(document).ready(function() {
        $("#customer_cdr_from_date").datetimepicker({ dateFormat: 'yy-mm-dd' });		
        $("#customer_cdr_to_date").datetimepicker({ dateFormat: 'yy-mm-dd' });			
    });
</script>
<? endblock() ?>

<? startblock('page-title') ?>
<?= $page_title ?><br/>
<? endblock() ?>

<? startblock('content') ?>        

<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" id="searchbar">
    <div class="portlet-header ui-widget-header" ><span id="show_search" style="cursor:pointer">Search</span><span class="ui-icon ui-icon-circle-arrow-s"></span></div>
    <div class="portlet-content"  id="search_bar" style="cursor:pointer; display:none">
        <?php echo $form_search; ?>
    </div>
</div>


<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
    <div class="portlet-header ui-widget-header">User Payment List<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
    <div class="portlet-content">         
        <form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">
            <table id="payment_report_grid" align="left" style="display:none;"></table>
        </form>
    </div>
</div>  

<br/><br/>
<? endblock() ?>	
<? end_extend() ?>  

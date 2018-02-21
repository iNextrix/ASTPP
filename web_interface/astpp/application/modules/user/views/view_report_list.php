<? extend('master.php') ?>
<? startblock('extra_head') ?>

<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        build_grid("report_grid","",<? echo $grid_fields; ?>,"");
        $("#cusotmer_cdr_payment_search_btn").click(function(){
            post_request_for_search("report_grid","<?php echo base_url(); ?>reports/user_cdrreport_search/","cdr_customer_search");
        });        
        $("#id_reset").click(function(){
            clear_search_request("report_grid","<?php echo base_url(); ?>reports/user_cdrreport_clearsearchfilter/");
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
    <div class="portlet-header ui-widget-header" ><span id="show_search" style="cursor:pointer">Search</span>
        <span id="active_search"  style="margin-left:10px; text-align: center;background-color: none;color:#1c8400;"></span>
        <span class="ui-icon ui-icon-circle-arrow-s"></span></div>
    <div class="portlet-content"  id="search_bar" style="cursor:pointer; display:none">
        <?php echo $form_search; ?>
    </div>
</div>
<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
    <div class="portlet-header ui-widget-header"><?= $grid_title; ?><span class="ui-icon ui-icon-circle-arrow-s"></span></div>
    <div class="portlet-content">         
        <form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">
            <table id="report_grid" align="left" style="display:none;"></table>
        </form>
    </div>
</div>  

<? endblock() ?>	
<? end_extend() ?>  
<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
      
        build_grid("commission_report_grid","",<? echo $grid_fields; ?>,"");
        
        $("#commission_search_btn").click(function(){
            post_request_for_search("commission_report_grid","<?php echo base_url(); ?>reports/reseller_commissionreport_search/","reseller_commission_search");
        });        
        $("#id_reset").click(function(){
            clear_search_request("commission_report_grid","<?php echo base_url(); ?>reports/reseller_commissionreport_clearsearchfilter/");
        });
    });
</script>
<script>
    $(document).ready(function() {
        $("#commission_from_date").datetimepicker({ dateFormat: 'yy-mm-dd' });		
        $("#commission_to_date").datetimepicker({ dateFormat: 'yy-mm-dd' });			
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
    <div class="portlet-header ui-widget-header">Customer Payment List<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
    <div class="portlet-content">         
        <form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">
            <table id="commission_report_grid" align="left" style="display:none;"></table>
        </form>
    </div>
</div>  

<br/><br/>
<? endblock() ?>	
<? end_extend() ?>  

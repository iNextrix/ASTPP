<? extend('master.php') ?>
<? startblock('extra_head') ?>

<script type="text/javascript" language="javascript">
    $(document).ready(function() {
      
        build_grid("invoice_grid","",<? echo $grid_fields; ?>,"");
        $("#inbound_search_btn").click(function(){
            alert("sddddd");
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

<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" id="searchbar">
    <div class="portlet-header ui-widget-header" ><span id="show_search" style="cursor:pointer">Search</span><span class="ui-icon ui-icon-circle-arrow-s"></span></div>
   <div class="portlet-content"  id="search_bar" style="cursor:pointer; display:none">
    <?php echo $form_search;?>
    </div>
</div>

<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
    <div class="portlet-header ui-widget-header">Invoices List<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
    <div class="portlet-content">         
        <form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">
            <table id="invoice_grid" align="left" style="display:none;"></table>
        </form>
    </div>
</div>  
  
<? endblock() ?>	
<? end_extend() ?>  

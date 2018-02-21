<? extend('master.php') ?>
<? startblock('extra_head') ?>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/module_js/generate_grid.js"></script>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        build_grid("error_list_grid","",<? echo $grid_fields; ?>,"");
    });
</script>

<? endblock() ?>

<? startblock('page-title') ?>
    <?= $page_title ?><br/>
<? endblock() ?>

<? startblock('content') ?>        

<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" id="searchbar">
    <div class="portlet-header ui-widget-header" ><span id="show_search" style="cursor:pointer">Search</span><span class="ui-icon ui-icon-circle-arrow-s"></span></div>
</div>


<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
    <div class="portlet-header ui-widget-header">Error List<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
    <div class="portlet-content">         
        <form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">
            <table id="error_list_grid" align="left" style="display:none;"></table>
        </form>
    </div>
</div>  

<? endblock() ?>	

<? end_extend() ?>  

<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        build_grid("fs_gateway_grid","",<? echo $grid_fields; ?>,<? echo $grid_buttons; ?>);
    });
</script>

<? endblock() ?>
<? startblock('page-title') ?>
<?= $page_title ?><br/>
<? endblock() ?>
<? startblock('content') ?>        
<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
    <div class="portlet-header ui-widget-header">Freeswitch SIP Profile List
        <span id="error_msg" class=" success"></span>
        <span class="ui-icon ui-icon-circle-arrow-s"></span></div>
    <div class="portlet-content">         
        <form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">
            <table id="fs_gateway_grid" align="left" style="display:none;"></table>
        </form>
    </div>
</div>  

<? endblock() ?>	
<? end_extend() ?>  

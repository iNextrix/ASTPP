<? extend('master.php') ?>
<? startblock('extra_head') ?>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/module_js/generate_grid.js"></script>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
      
        build_grid("dispatcher_grid","",<? echo $grid_fields; ?>,<? echo $grid_buttons; ?>);
        
        $("#opensipsdispatcher_search_btn").click(function(){
            post_request_for_search("dispatcher_grid","","dispatcher_search");
        });        
        $("#id_reset").click(function(){
            clear_search_request("dispatcher_grid","");
        });
        
    });
</script>
<? endblock() ?>

<? startblock('page-title') ?>
<?= $page_title ?><br/>
<? endblock() ?>

<? startblock('content') ?>        

<?php echo $form_search ?>


<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
    <div class="portlet-header ui-widget-header">Dispatcher List
        <span id="error_msg" class=" success"></span>
        <span class="ui-icon ui-icon-circle-arrow-s"></span></div>
    <div class="portlet-content">         
        <form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">
            <table id="dispatcher_grid" align="left" style="display:none;"></table>
        </form>
    </div>
</div>  
<? endblock() ?>	
<? end_extend() ?>  

<? extend('master.php') ?>
<? startblock('extra_head') ?>
<script type="text/javascript" language="javascript">
    $(document).ready(function() {
      
        build_grid("configuration_grid","",<? echo $grid_fields; ?>,<? echo $grid_buttons; ?>);
        
        $("#template_search_btn").click(function(){
            post_request_for_search("configuration_grid","","template_search");
        });        
        $("#id_reset").click(function(){
            clear_search_request("configuration_grid","");
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
        <?php echo $form_search; ?>
    </div>
</div>

<div class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">                        
    <div class="portlet-header ui-widget-header">Template List<span class="ui-icon ui-icon-circle-arrow-s"></span></div>
    <div class="portlet-content">         
        <form method="POST" action="del/0/" enctype="multipart/form-data" id="ListForm">
            <table id="configuration_grid" align="left" style="display:none;"></table>
        </form>
    </div>
</div>  

<? endblock() ?>	
<? end_extend() ?>  
